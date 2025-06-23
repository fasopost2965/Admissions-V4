<?php
require_once('tcpdf/tcpdf.php');

class InscriptionReceipt extends TCPDF {
    
    // Configuration
    public function __construct() {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);
        $this->SetCreator('École La Victoire');
        $this->SetAuthor('La Victoire de l\'enseignement privé');
        $this->SetTitle('Récépissé d\'inscription');
        $this->SetMargins(15, 20, 15);
        $this->SetAutoPageBreak(true, 20);
    }
    
    // En-tête personnalisé
    public function Header() {
        // Logo centré (si disponible)
        if (file_exists('../assets/logo.png')) {
            $this->Image('../assets/logo.png', 85, 10, 40, 0, 'PNG');
        }
        
        // Espacer après logo
        $this->Ln(35);
        
        // Nom de l'école
        $this->SetFont('helvetica', 'B', 18);
        $this->SetTextColor(0, 60, 113); // Bleu école
        $this->Cell(0, 10, 'LA VICTOIRE DE L\'ENSEIGNEMENT PRIVÉ', 0, 1, 'C');
        
        // Sous-titre
        $this->SetFont('helvetica', '', 12);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 8, 'École Maternelle et Primaire', 0, 1, 'C');
        
        // Ligne dorée
        $this->SetDrawColor(255, 215, 0);
        $this->SetLineWidth(1);
        $this->Line(20, 65, 190, 65);
        
        $this->Ln(10);
    }
    
    // Pied de page
    public function Footer() {
        $this->SetY(-30);
        $this->SetFont('helvetica', 'I', 10);
        $this->SetTextColor(128, 128, 128);
        
        // Informations de contact
        $this->Cell(0, 5, 'Avenue Mohammed Daoud, Tétouan', 0, 1, 'C');
        $this->Cell(0, 5, 'Tél: +212 5 39 96 XX XX | Email: contact@groupelavictoire.com', 0, 1, 'C');
        $this->Cell(0, 5, 'www.groupelavictoire.com', 0, 1, 'C');
    }
    
    // Générer le récépissé (2 copies sur 1 page)
    public function generateReceipt($data) {
        $this->AddPage();
        
        // COPIE 1 - École
        $this->generateCopy($data, 'COPIE ÉCOLE', 20);
        
        // Ligne de découpe avec ciseaux
        $this->SetDrawColor(128, 128, 128);
        $this->SetLineWidth(0.5);
        $this->SetLineDash(array(5, 5));
        $this->Line(10, 148, 200, 148);
        $this->SetLineDash(); // Reset
        
        // Icône ciseaux
        $this->SetFont('helvetica', '', 16);
        $this->Text(8, 145, '✂');
        $this->Text(185, 145, '✂');
        
        // COPIE 2 - Parents
        $this->generateCopy($data, 'COPIE PARENTS', 155);
    }
    
    private function generateCopy($data, $copyType, $startY) {
        $this->SetY($startY);
        
        // Type de copie
        $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 5, $copyType, 0, 1, 'R');
        
        // Titre principal
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(0, 60, 113);
        $this->Cell(0, 10, 'RÉCÉPISSÉ D\'INSCRIPTION', 0, 1, 'C');
        
        // Numéro de dossier (encadré doré)
        $this->SetFillColor(255, 215, 0); // Fond doré
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 12, 'N° DOSSIER : ' . ($data['dossier_number'] ?? 'INS-' . date('Y') . '-' . rand(1000, 9999)), 1, 1, 'C', true);
        
        $this->Ln(5);
        
        // Informations en tableau
        $this->SetFont('helvetica', '', 11);
        $this->SetFillColor(245, 245, 245);
        
        // Tableau d'informations
        $infos = [
            ['Type d\'inscription', $data['inscription_type'] ?? 'Nouvelle inscription'],
            ['Année scolaire', '2025-2026'],
            ['Nom de l\'élève', strtoupper($data['student_lastname'] ?? $data['nom'] ?? '')],
            ['Prénom de l\'élève', $data['student_firstname'] ?? $data['prenom'] ?? ''],
            ['Date de naissance', $data['student_birthdate'] ?? $data['date_naissance'] ?? ''],
            ['Niveau demandé', $this->formatLevel($data['school_level'] ?? $data['niveau'] ?? '')],
            ['Services', $this->formatServices($data)]
        ];
        
        foreach ($infos as $i => $info) {
            $fill = ($i % 2 == 0);
            $this->Cell(60, 8, $info[0] . ' :', 0, 0, 'L', $fill);
            $this->SetFont('helvetica', 'B', 11);
            $this->Cell(110, 8, $info[1], 0, 1, 'L', $fill);
            $this->SetFont('helvetica', '', 11);
        }
        
        $this->Ln(5);
        
        // Date et signature
        $this->SetFont('helvetica', '', 10);
        $this->Cell(85, 5, 'Date : ' . date('d/m/Y'), 0, 0, 'L');
        $this->Cell(85, 5, 'Opératrice : Système Automatique', 0, 1, 'R');
        
        $this->Ln(5);
        
        // Espaces signature
        $this->Cell(85, 15, 'Signature Parents', 'T', 0, 'C');
        $this->Cell(85, 15, 'Cachet École', 'T', 1, 'C');
    }
    
    private function formatServices($data) {
        $services = [];
        
        if (isset($data['transport_scolaire']) && $data['transport_scolaire'] === 'true') {
            $quartier = $data['transport_quartier'] ?? 'Non spécifié';
            $services[] = 'Transport (' . $quartier . ')';
        }
        
        if (isset($data['cantine_scolaire']) && $data['cantine_scolaire'] === 'true') {
            $services[] = 'Cantine';
        }
        
        return empty($services) ? 'Aucun service' : implode(' + ', $services);
    }
    
    private function formatLevel($level) {
        $levels = [
            'PS' => 'Petite Section (PS)',
            'MS' => 'Moyenne Section (MS)',
            'GS' => 'Grande Section (GS)',
            'CP' => 'CP (1ère année primaire)',
            'CE1' => 'CE1 (2ème année primaire)',
            'CE2' => 'CE2 (3ème année primaire)',
            'CM1' => 'CM1 (4ème année primaire)',
            'CM2' => 'CM2 (5ème année primaire)',
            '6EME' => '6ème année primaire'
        ];
        return $levels[$level] ?? $level;
    }
}
?> 