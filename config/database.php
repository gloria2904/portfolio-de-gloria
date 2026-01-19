<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_gloria');  // ← Nom de votre base
define('DB_USER', 'root');              // ← Votre utilisateur MySQL
define('DB_PASS', '');                  // ← Votre mot de passe MySQL

// Fonction de connexion à la base de données
function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        die("Erreur de connexion à la base de données");
    }
}

// Démarrage de session sécurisée
function secureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0);
        session_start();
    }
}
?>