<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Lire les données JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}

try {
    // Inclure les classes PDF
    require_once('receipt-pdf.php');
    require_once('supplies-pdf.php');
    
    // Générer un numéro de dossier unique si non fourni
    if (!isset($data['dossier_number'])) {
        $data['dossier_number'] = 'INS-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    // Créer le dossier de téléchargement s'il n'existe pas
    $downloadDir = '../downloads';
    if (!is_dir($downloadDir)) {
        mkdir($downloadDir, 0755, true);
    }
    
    // Générer le récépissé
    $receiptGenerator = new InscriptionReceipt();
    $receiptGenerator->generateReceipt($data);
    $receiptFilename = 'recepisse_' . $data['dossier_number'] . '_' . date('YmdHis') . '.pdf';
    $receiptPath = $downloadDir . '/' . $receiptFilename;
    $receiptGenerator->Output($receiptPath, 'F');
    
    // Générer la liste des fournitures
    $suppliesGenerator = new SuppliesList();
    $suppliesGenerator->generateSuppliesList($data);
    $suppliesFilename = 'fournitures_' . $data['dossier_number'] . '_' . date('YmdHis') . '.pdf';
    $suppliesPath = $downloadDir . '/' . $suppliesFilename;
    $suppliesGenerator->Output($suppliesPath, 'F');
    
    // Créer le package ZIP
    $zipFilename = 'dossier_' . $data['dossier_number'] . '_' . date('YmdHis') . '.zip';
    $zipPath = $downloadDir . '/' . $zipFilename;
    
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        // Ajouter les PDFs au ZIP
        $zip->addFile($receiptPath, 'recepisse_inscription.pdf');
        $zip->addFile($suppliesPath, 'liste_fournitures.pdf');
        
        // Ajouter un fichier README
        $readme = createReadmeContent($data);
        $zip->addFromString('LIRE_MOI.txt', $readme);
        
        $zip->close();
        
        // Nettoyer les PDFs temporaires
        unlink($receiptPath);
        unlink($suppliesPath);
        
        // Retourner les informations
        echo json_encode([
            'success' => true,
            'files' => [
                'receipt' => 'recepisse_inscription.pdf',
                'supplies' => 'liste_fournitures.pdf',
                'package' => $zipFilename
            ],
            'urls' => [
                'package' => '/downloads/' . $zipFilename
            ],
            'dossier_number' => $data['dossier_number'],
            'message' => 'Documents générés avec succès'
        ]);
        
    } else {
        throw new Exception('Impossible de créer le package ZIP');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la génération: ' . $e->getMessage()
    ]);
}

function createReadmeContent($data) {
    $studentName = ($data['student_firstname'] ?? '') . ' ' . ($data['student_lastname'] ?? '');
    $content = "ÉCOLE LA VICTOIRE - DOSSIER D'INSCRIPTION\n";
    $content .= "=========================================\n\n";
    $content .= "Élève : " . trim($studentName) . "\n";
    $content .= "N° Dossier : " . $data['dossier_number'] . "\n";
    $content .= "Classe : " . ($data['school_level'] ?? '') . "\n";
    $content .= "Date de génération : " . date('d/m/Y à H:i') . "\n\n";
    
    $content .= "CONTENU DU DOSSIER :\n";
    $content .= "-------------------\n";
    $content .= "1. recepisse_inscription.pdf - Récépissé d'inscription\n";
    $content .= "   > À imprimer en 2 exemplaires (ligne de découpe)\n";
    $content .= "   > 1 copie pour l'école (à remettre signée)\n";
    $content .= "   > 1 copie pour les parents (à conserver)\n\n";
    
    $content .= "2. liste_fournitures.pdf - Liste des fournitures scolaires\n";
    $content .= "   > Adaptée au niveau de classe de l'élève\n";
    $content .= "   > Cases à cocher pour suivi des achats\n";
    $content .= "   > À signer et remettre à l'école\n\n";
    
    $content .= "SERVICES SOUSCRITS :\n";
    $content .= "-------------------\n";
    if (isset($data['transport_scolaire']) && $data['transport_scolaire'] === 'true') {
        $content .= "✓ Transport scolaire - Quartier : " . ($data['transport_quartier'] ?? 'Non spécifié') . "\n";
    }
    if (isset($data['cantine_scolaire']) && $data['cantine_scolaire'] === 'true') {
        $content .= "✓ Cantine scolaire\n";
    }
    if (!isset($data['transport_scolaire']) && !isset($data['cantine_scolaire'])) {
        $content .= "Aucun service supplémentaire\n";
    }
    $content .= "\n";
    
    $content .= "INSTRUCTIONS IMPORTANTES :\n";
    $content .= "-------------------------\n";
    $content .= "• Imprimer tous les documents sur papier blanc A4\n";
    $content .= "• Signer aux emplacements prévus\n";
    $content .= "• Marquer toutes les fournitures au nom de l'enfant\n";
    $content .= "• Apporter le dossier complet le jour de la rentrée\n";
    $content .= "• Conserver une copie du récépissé pour vos archives\n\n";
    
    $content .= "CONTACT ÉCOLE LA VICTOIRE :\n";
    $content .= "--------------------------\n";
    $content .= "📍 Adresse : Avenue Mohammed Daoud, Tétouan\n";
    $content .= "📞 Téléphone : +212 5 39 96 XX XX\n";
    $content .= "📧 Email : contact@groupelavictoire.com\n";
    $content .= "🌐 Site web : www.groupelavictoire.com\n\n";
    
    $content .= "Merci de votre confiance !\n";
    $content .= "L'équipe de l'École La Victoire\n";
    
    return $content;
}
?> 