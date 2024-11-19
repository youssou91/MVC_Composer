<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'dev434');
define('DB_PASSWORD', 'dev434');
define('DB_NAME', 'cours343');
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8');

function getConnection() {
    try {
        // Correction de la chaîne DSN
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
        
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
