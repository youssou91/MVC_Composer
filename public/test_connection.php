<?php
// Remonter d'un niveau pour inclure le fichier de configuration
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/db.php';

// Désactiver la mise en cache pour le débogage
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test de connexion à la base de données</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .success { color: green; }
        .error { color: red; border: 1px solid red; padding: 10px; margin: 10px 0; }
        .info { background-color: #f0f0f0; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Test de connexion à la base de données</h1>
    
    <div class="info">
        <h3>Paramètres de connexion :</h3>
        <ul>
            <li>Hôte : <?php echo htmlspecialchars(DB_HOST); ?></li>
            <li>Port : <?php echo DB_PORT; ?></li>
            <li>Base de données : <?php echo htmlspecialchars(DB_NAME); ?></li>
            <li>Utilisateur : <?php echo htmlspecialchars(DB_USER); ?></li>
        </ul>
    </div>

    <?php
    try {
        echo "<h3>Tentative de connexion...</h3>";
        $pdo = getConnection();
        
        // Si on arrive ici, la connexion a réussi
        echo "<div class='success'>";
        echo "<h3>✅ Connexion réussie à la base de données !</h3>";
        
        // Afficher des informations sur la base de données
        $stmt = $pdo->query("SELECT DATABASE() as dbname, USER() as user, VERSION() as version");
        $info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h4>Informations de connexion :</h4>";
        echo "<ul>";
        echo "<li>Base de données : " . htmlspecialchars($info['dbname']) . "</li>";
        echo "<li>Utilisateur : " . htmlspecialchars($info['user']) . "</li>";
        echo "<li>Version MySQL : " . htmlspecialchars($info['version']) . "</li>";
        echo "</ul>";
        echo "</div>";
        
    } catch (PDOException $e) {
        // En cas d'erreur, afficher le message d'erreur
        echo "<div class='error'>";
        echo "<h3>❌ Erreur de connexion :</h3>";
        echo "<p><strong>Message :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><strong>Code d'erreur :</strong> " . $e->getCode() . "</p>";
        
        // Vérifier si le service MySQL est en cours d'exécution
        echo "<h4>Vérification du service MySQL :</h4>";
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            exec('tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL', $output, $return_var);
            if ($return_var === 0 && count($output) > 1) {
                echo "<p style='color: green;'>✅ Le service MySQL semble fonctionner.</p>";
            } else {
                echo "<p style='color: red;'>❌ Le service MySQL ne semble pas fonctionner. Essayez de le démarrer depuis XAMPP.</p>";
            }
        } else {
            echo "<p>Vérification du service MySQL non disponible sur ce système d'exploitation.</p>";
        }
        
        echo "</div>";
    }
    ?>
    
    <div class="info">
        <h3>Informations supplémentaires :</h3>
        <ul>
            <li>PHP Version : <?php echo phpversion(); ?></li>
            <li>Extensions PDO chargées : <?php echo extension_loaded('pdo') ? 'Oui' : 'Non'; ?></li>
            <li>Extension PDO_MySQL chargée : <?php echo extension_loaded('pdo_mysql') ? 'Oui' : 'Non'; ?></li>
        </ul>
    </div>
</body>
</html>
