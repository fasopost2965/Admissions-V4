<?php
// Configuration de base de données - École La Victoire (Laragon)
$host = 'localhost';
$dbname = 'ecole_la_victoire';
$username = 'root';
$password = ''; // Laragon utilise un mot de passe vide par défaut

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Démarrer la session
session_start();

// Configuration générale
define('SCHOOL_NAME', 'École La Victoire');
define('SCHOOL_YEAR', '2025-2026');
define('UPLOAD_PATH', '../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Créer le dossier uploads s'il n'existe pas
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

// Fonctions utilitaires
function generateDossierNumber() {
    global $pdo;
    
    $year = date('Y');
    $stmt = $pdo->query("SELECT COUNT(*) FROM inscriptions WHERE YEAR(created_at) = $year");
    $count = $stmt->fetchColumn() + 1;
    
    return sprintf("%s-LV-%04d", $year, $count);
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    $clean = preg_replace('/[^0-9]/', '', $phone);
    return strlen($clean) === 10;
}

function validateCIN($cin) {
    return preg_match('/^[A-Z]{1,2}[0-9]{5,6}$/', strtoupper($cin));
}

function sendJsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function logError($message, $file = null) {
    $logFile = '../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";
    
    if ($file) {
        $logMessage .= " in $file";
    }
    
    file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Créer le dossier logs s'il n'existe pas
if (!file_exists('../logs/')) {
    mkdir('../logs/', 0755, true);
}

// Paramètres de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'admissionsv4');
define('DB_USER', 'root');
define('DB_PASS', ''); // Par défaut, Laragon n'a pas de mot de passe pour root
define('DB_CHARSET', 'utf8mb4');

// Configuration du fuseau horaire
date_default_timezone_set('Africa/Casablanca');
?> 