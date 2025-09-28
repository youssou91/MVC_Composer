<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure le fichier de configuration de la base de données
require_once __DIR__ . '/../config/db.php';

try {
    echo "<h2>Test de connexion à la base de données</h2>";
    
    // Afficher les paramètres de connexion
    echo "<h3>Paramètres de connexion :</h3>";
    echo "<ul>";
    echo "<li>Hôte : " . DB_HOST . "</li>";
    echo "<li>Utilisateur : " . DB_USER . "</li>";
    echo "<li>Base de données : " . DB_NAME . "</li>";
    echo "<li>Port : " . DB_PORT . "</li>";
    echo "</ul>";
    
    // Tester la connexion
    echo "<h3>Test de connexion :</h3>";
    $pdo = getConnection();
    
    if ($pdo instanceof PDO) {
        echo "<p style='color: green;'>✅ Connexion réussie !</p>";
        
        // Tester une requête simple
        $stmt = $pdo->query("SELECT DATABASE() AS db_name, USER() AS user, VERSION() AS version");
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Informations de connexion :</h3>";
        echo "<ul>";
        echo "<li>Base de données connectée : " . htmlspecialchars($info['db_name']) . "</li>";
        echo "<li>Utilisateur : " . htmlspecialchars($info['user']) . "</li>";
        echo "<li>Version de MySQL : " . htmlspecialchars($info['version']) . "</li>";
        echo "</ul>";
        
        // Vérifier si la table utilisateur existe
        $tables = $pdo->query("SHOW TABLES LIKE 'utilisateur'")->fetchAll();
        if (count($tables) > 0) {
            echo "<p style='color: green;'>✅ La table 'utilisateur' existe.</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ La table 'utilisateur' n'existe pas.</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ La connexion a échoué.</p>";
    }
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Erreur :</h3>";
    echo "<p><strong>Message :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Fichier :</strong> " . htmlspecialchars($e->getFile()) . " (ligne " . $e->getLine() . ")</p>";
    
    // Afficher les informations de débogage supplémentaires
    if (isset($pdo) && $pdo instanceof PDO) {
        echo "<h3>Informations de débogage PDO :</h3>";
        echo "<pre>" . print_r($pdo->errorInfo(), true) . "</pre>";
    }
}

echo "<p><a href='index.php'>Retour à l'application</a></p>";
?>
