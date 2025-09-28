<?php
// Inclure le fichier de configuration de la base de données
require_once __DIR__ . '/config/db.php';

try {
    // Se connecter à la base de données
    $pdo = getConnection();
    
    // Vérifier la structure de la table role
    $stmt = $pdo->query("SHOW COLUMNS FROM role");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Structure de la table 'role':</h2>";
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    // Vérifier les données dans la table role
    $stmt = $pdo->query("SELECT * FROM role");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Données dans la table 'role':</h2>";
    echo "<pre>";
    print_r($roles);
    echo "</pre>";
    
    // Tester la requête utilisée dans getUserRole
    $testId = 1; // ID d'utilisateur de test
    $sql = "SELECT r.description as role 
            FROM utilisateur u 
            JOIN role_utilisateur ur ON u.id_utilisateur = ur.id_utilisateur 
            JOIN role r ON ur.id_role = r.id_role 
            WHERE u.id_utilisateur = :userId";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $testId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>Résultat de la requête de test pour l'utilisateur ID $testId:</h2>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<div style='color:red;'>Erreur: " . $e->getMessage() . "</div>";
}
?>
