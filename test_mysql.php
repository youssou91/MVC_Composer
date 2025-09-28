<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de connexion PDO à MySQL</h2>";

// Paramètres de connexion
$host = '127.0.0.1';
$dbname = 'cours343';
$username = 'root';
$password = '';
$port = 3306;
$charset = 'utf8mb4';

// Afficher les paramètres de connexion
echo "<h3>Paramètres de connexion :</h3>";
echo "<ul>";
echo "<li>Hôte : " . htmlspecialchars($host) . "</li>";
echo "<li>Base de données : " . htmlspecialchars($dbname) . "</li>";
echo "<li>Utilisateur : " . htmlspecialchars($username) . "</li>";
echo "<li>Port : " . htmlspecialchars($port) . "</li>";
echo "<li>Charset : " . htmlspecialchars($charset) . "</li>";
echo "</ul>";

try {
    // Créer la chaîne DSN
    $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=$charset";
    
    // Options PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    echo "<p>Connexion avec DSN : " . htmlspecialchars($dsn) . "</p>";
    
    // Se connecter à la base de données
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "<p style='color: green; font-weight: bold;'>✅ Connexion PDO réussie !</p>";
    
    // Tester une requête simple
    $stmt = $pdo->query('SELECT VERSION() AS version');
    $version = $stmt->fetch();
    echo "<p>Version de MySQL : " . htmlspecialchars($version['version']) . "</p>";
    
    // Vérifier les tables existantes
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tables dans la base de données :</h3>";
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Aucune table trouvée dans la base de données.</p>";
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red; background-color: #ffeeee; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "<h3>❌ Erreur PDO :</h3>";
    echo "<p><strong>Message :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Code d'erreur :</strong> " . htmlspecialchars($e->getCode()) . "</p>";
    
    // Afficher plus d'informations sur l'erreur si disponible
    if (isset($pdo)) {
        $errorInfo = $pdo->errorInfo();
        if (!empty($errorInfo)) {
            echo "<h4>Informations d'erreur PDO :</h4>";
            echo "<pre>" . print_r($errorInfo, true) . "</pre>";
        }
    }
    
    echo "</div>";
}

echo "<p><a href='index.php'>Retour à l'application</a></p>";
?>
