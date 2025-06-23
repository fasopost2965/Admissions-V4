<?php
// php/save-inscription.php

// Ce script gère la réception des données d'inscription au format JSON,
// se connecte à la base de données et insère une nouvelle ligne dans la table `inscriptions`.

header('Content-Type: application/json');
require_once 'config.php';

// 1. Récupérer les données
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Vérifier si les données JSON sont valides
if ($data === null) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Données JSON invalides.']);
    exit;
}

// 2. Connexion à la base de données
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Log l'erreur réelle pour le débogage interne
    error_log("DB Connection Error: " . $e->getMessage());
    // Réponse générique pour le client
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Erreur interne du serveur. Impossible de se connecter à la base de données.']);
    exit;
}

// 3. Préparer les données pour l'insertion
// La liste des colonnes doit correspondre exactement à votre table `inscriptions`
$columns = [
    'operator_name', 'type_inscription', 'niveau_demande', 'annee_scolaire',
    'nom_eleve_ar', 'prenom_eleve_ar', 'nom_eleve_fr', 'prenom_eleve_fr',
    'sexe_eleve', 'date_naissance_eleve', 'lieu_naissance_eleve', 'nationalite_eleve',
    'langue_maternelle_eleve', 'ecole_provenance', 'motif_changement',
    'nom_pere_ar', 'prenom_pere_ar', 'nom_pere_fr', 'prenom_pere_fr', 'cin_pere',
    'profession_pere', 'tel_pere', 'email_pere',
    'nom_mere_ar', 'prenom_mere_ar', 'nom_mere_fr', 'prenom_mere_fr', 'cin_mere',
    'profession_mere', 'tel_mere', 'email_mere',
    'adresse_parents', 'situation_familiale',
    'service_transport', 'service_cantine',
    'problemes_sante', 'allergies', 'medicaments_reguliers', 'besoins_specifiques',
    'nom_medecin', 'tel_medecin', 'personne_contact_urgence', 'tel_contact_urgence',
    'statut'
];

$insert_data = [];
foreach ($columns as $column) {
    // Gérer les champs de date qui peuvent être vides
    if ($column === 'date_naissance_eleve' && empty($data[$column])) {
        $insert_data[$column] = null;
    } else {
        // Si la clé n'existe pas dans $data, la valeur sera null
        $insert_data[$column] = $data[$column] ?? null;
    }
}

// Définir le statut final de l'inscription
$insert_data['statut'] = 'Validé';

// 4. Construire et exécuter la requête SQL
$sql_columns = '`' . implode('`, `', array_keys($insert_data)) . '`';
$sql_placeholders = ':' . implode(', :', array_keys($insert_data));

$sql = "INSERT INTO inscriptions ($sql_columns) VALUES ($sql_placeholders)";

try {
    $stmt = $pdo->prepare($sql);
    
    // Le liage des paramètres se fait directement dans execute() pour plus de propreté
    $stmt->execute($insert_data);
    
    $last_id = $pdo->lastInsertId();
    
    // 5. Renvoyer une réponse de succès
    echo json_encode([
        'status' => 'success', 
        'message' => 'Inscription enregistrée avec succès dans la base de données.',
        'inscription_id' => $last_id
    ]);

} catch (PDOException $e) {
    error_log("SQL Execution Error: " . $e->getMessage());
    // Fournir plus de détails en environnement de dev si nécessaire
    // $error_details = 'Error: ' . $e->getMessage();
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'error', 
        'message' => 'Une erreur est survenue lors de l\'enregistrement de l\'inscription.'
        // 'details' => $error_details // Décommenter pour débogage
    ]);
}
?> 