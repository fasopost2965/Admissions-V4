<?php
// php/get-dashboard-stats.php

header('Content-Type: application/json');
require_once 'config.php';

// Initialisation de l'objet de réponse
$response = [
    'status' => 'success',
    'stats' => [
        'totalInscriptions' => 0,
        'nouvellesInscriptions' => 0,
        'reinscriptions' => 0,
        'transferts' => 0, // Bien que non utilisé, on le garde pour la cohérence
        'inscriptionsParNiveau' => [],
        'repartitionServices' => [
            'transport' => 0,
            'cantine' => 0
        ]
    ],
    'recentActivity' => []
];

// Connexion à la base de données
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    $response['status'] = 'error';
    $response['message'] = 'Erreur de connexion à la base de données.';
    error_log($e->getMessage());
    echo json_encode($response);
    exit;
}

try {
    // 1. Statistiques générales
    $stmt = $pdo->query("
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN type_inscription = 'Nouvelle inscription' THEN 1 ELSE 0 END) as nouvelles,
            SUM(CASE WHEN type_inscription = 'Réinscription' THEN 1 ELSE 0 END) as reinscriptions
        FROM inscriptions WHERE statut = 'Validé'
    ");
    $generalStats = $stmt->fetch();

    if ($generalStats) {
        $response['stats']['totalInscriptions'] = (int) $generalStats['total'];
        $response['stats']['nouvellesInscriptions'] = (int) $generalStats['nouvelles'];
        $response['stats']['reinscriptions'] = (int) $generalStats['reinscriptions'];
    }

    // 2. Inscriptions par niveau
    $stmt = $pdo->query("
        SELECT niveau_demande, COUNT(*) as count
        FROM inscriptions
        WHERE statut = 'Validé' AND niveau_demande IS NOT NULL
        GROUP BY niveau_demande
        ORDER BY FIELD(niveau_demande, 'PS', 'MS', 'GS', 'CP', 'CE1', 'CE2', 'CE3', 'CE4', 'CE5', 'CE6', '1AC', '2AC', '3AC')
    ");
    $response['stats']['inscriptionsParNiveau'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Répartition des services
    $stmt = $pdo->query("
        SELECT
            SUM(CASE WHEN service_transport = 'Oui' THEN 1 ELSE 0 END) as transport,
            SUM(CASE WHEN service_cantine = 'Oui' THEN 1 ELSE 0 END) as cantine
        FROM inscriptions WHERE statut = 'Validé'
    ");
    $servicesStats = $stmt->fetch();
    if ($servicesStats) {
        $response['stats']['repartitionServices']['transport'] = (int) $servicesStats['transport'];
        $response['stats']['repartitionServices']['cantine'] = (int) $servicesStats['cantine'];
    }

    // 4. Activité récente (5 dernières inscriptions)
    $stmt = $pdo->query("
        SELECT nom_eleve_fr, prenom_eleve_fr, niveau_demande, date_creation
        FROM inscriptions
        WHERE statut = 'Validé'
        ORDER BY date_creation DESC
        LIMIT 5
    ");
    $response['recentActivity'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    http_response_code(500);
    $response['status'] = 'error';
    $response['message'] = 'Erreur lors de la récupération des statistiques.';
    error_log($e->getMessage());
    echo json_encode($response);
    exit;
}

// Envoyer la réponse finale
echo json_encode($response);

?> 