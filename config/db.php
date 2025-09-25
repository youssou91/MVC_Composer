<?php
/**
 * Configuration de la base de données
 */

// Utilisation des identifiants par défaut de XAMPP
define('DB_HOST', '127.0.0.1');  // Utilisation de l'adresse IP au lieu de localhost
define('DB_USER', 'root');       // Utilisateur par défaut de XAMPP
define('DB_PASSWORD', '');       // Pas de mot de passe par défaut
define('DB_NAME', 'cours343');   // Votre base de données
define('DB_PORT', 3306);
define('DB_CHARSET', 'utf8mb4');

/**
 * Établit une connexion à la base de données
 * 
 * @return PDO Instance PDO pour interagir avec la base de données
 * @throws PDOException Si la connexion échoue
 */
function getConnection() {
    try {
        // Journalisation des paramètres de connexion
        error_log("=== Tentative de connexion PDO ===");
        error_log("Hôte: " . DB_HOST);
        error_log("Base de données: " . DB_NAME);
        error_log("Utilisateur: " . DB_USER);
        error_log("Port: " . DB_PORT);
        error_log("Charset: " . DB_CHARSET);
        
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;port=%d;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_PORT,
            DB_CHARSET
        );
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
            PDO::ATTR_TIMEOUT            => 5  // Timeout de 5 secondes
        ];
        
        error_log("DSN: " . $dsn);
        error_log("Options: " . print_r($options, true));
        
        // Essayer de se connecter
        $startTime = microtime(true);
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
        $endTime = microtime(true);
        
        error_log(sprintf("Connexion réussie en %.4f secondes", $endTime - $startTime));
        
        // Vérifier que l'objet PDO est bien créé
        if (!($pdo instanceof PDO)) {
            throw new PDOException("La création de l'objet PDO a échoué");
        }
        
        // Définir le fuseau horaire de la base de données
        $pdo->exec("SET time_zone = '-05:00'");
        
        error_log("Connexion à la base de données établie avec succès");
        return $pdo;
        
    } catch (PDOException $e) {
        // Enregistrer l'erreur dans le journal
        error_log(sprintf(
            "Erreur de connexion à la base de données: %s (code: %s)",
            $e->getMessage(),
            $e->getCode()
        ));
        
        // Lancer une exception personnalisée
        throw new PDOException(
            "Impossible de se connecter à la base de données. Veuillez réessayer plus tard.",
            (int)$e->getCode()
        );
    }
}

/**
 * Vérifie si la connexion à la base de données est établie
 * 
 * @return bool True si la connexion est établie, false sinon
 */
function isDatabaseConnected() {
    try {
        $pdo = getConnection();
        return (bool)$pdo->query('SELECT 1');
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Exécute une requête SQL et retourne le résultat
 * 
 * @param string $sql Requête SQL à exécuter
 * @param array $params Paramètres pour la requête préparée
 * @return PDOStatement Le résultat de la requête
 */
function executeQuery($sql, $params = []) {
    $pdo = getConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// Tester la connexion à la base de données au chargement du fichier
if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
    try {
        $pdo = getConnection();
        error_log("Connexion à la base de données réussie");
    } catch (PDOException $e) {
        error_log("ERREUR: " . $e->getMessage());
    }
}
?>
