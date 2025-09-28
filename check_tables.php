<?php
// Inclure le fichier de configuration de la base de données
require_once __DIR__ . '/config/db.php';

// Fonction pour afficher les tables de la base de données
function checkDatabaseTables($pdo) {
    echo "<h2>Tables dans la base de données</h2>";
    
    try {
        // Récupérer la liste des tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "<p>Aucune table trouvée dans la base de données.</p>";
        } else {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table";
                
                // Afficher la structure de la table
                echo "<div style='margin-left: 20px;'>";
                try {
                    $desc = $pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
                    echo "<pre>" . print_r($desc, true) . "</pre>";
                } catch (PDOException $e) {
                    echo "<div style='color: red;'>Erreur lors de la description de la table: " . $e->getMessage() . "</div>";
                }
                echo "</div>";
                
                echo "</li>";
            }
            echo "</ul>";
        }
        
    } catch (PDOException $e) {
        echo "<div style='color: red;'>Erreur lors de la récupération des tables: " . $e->getMessage() . "</div>";
    }
}

// Afficher les informations de connexion
echo "<h1>Vérification de la base de données</h1>";
echo "<h3>Informations de connexion:</h3>";
echo "<ul>";
echo "<li>Hôte: " . DB_HOST . "</li>";
echo "<li>Base de données: " . DB_NAME . "</li>";
echo "<li>Utilisateur: " . DB_USER . "</li>";
echo "<li>Port: " . DB_PORT . "</li>";

try {
    // Se connecter à la base de données
    $pdo = getConnection();
    echo "<li>Statut: <span style='color: green;'>Connecté avec succès</span></li>";
    
    // Vérifier les tables
    checkDatabaseTables($pdo);
    
} catch (PDOException $e) {
    echo "<li>Statut: <span style='color: red;'>Échec de la connexion: " . $e->getMessage() . "</span></li>";
}

echo "</ul>";
?>
