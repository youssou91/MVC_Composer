<?php
// Inclure le fichier de configuration de la base de données
require_once __DIR__ . '/config/db.php';

// Afficher les erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    // Se connecter à la base de données
    $pdo = getConnection();
    
    echo "<h1>Liste des tables dans la base de données</h1>";
    
    // Récupérer la liste des tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "Aucune table trouvée dans la base de données.";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li><strong>$table</strong>";
            
            // Afficher la structure de la table
            echo "<div style='margin-left: 20px;'>";
            try {
                $stmt = $pdo->query("DESCRIBE `$table`");
                echo "<table border='1' cellpadding='5' style='margin: 10px 0;'>";
                echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Valeur par défaut</th><th>Extra</th></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                // Afficher quelques données d'exemple (limitées à 5 lignes)
                $data = $pdo->query("SELECT * FROM `$table` LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($data)) {
                    echo "<strong>Données d'exemple :</strong>";
                    echo "<pre>" . htmlspecialchars(print_r($data, true)) . "</pre>";
                }
                
            } catch (PDOException $e) {
                echo "<div style='color: red;'>Erreur lors de la description de la table: " . $e->getMessage() . "</div>";
            }
            echo "</div>";
            
            echo "</li>";
        }
        echo "</ul>";
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red;'>Erreur de connexion à la base de données: " . $e->getMessage() . "</div>";
    echo "<pre>" . print_r($e->getTrace(), true) . "</pre>";
}
?>
