<?php
// Afficher toutes les erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Afficher les erreurs de session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier la connexion à la base de données
require_once __DIR__ . '/config/db.php';
try {
    $pdo = getConnection();
    echo "Connexion à la base de données réussie!<br>";
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Tester l'insertion d'un utilisateur
try {
    $userData = [
        'nom_utilisateur' => 'test_user',
        'prenom' => 'Test',
        'couriel' => 'test@example.com',
        'password' => 'Test123!',
        'telephone' => '1234567890',
        'datNaiss' => '1990-01-01',
        'rue' => '123 Test St',
        'ville' => 'Testville',
        'code_postal' => 'H1A 1A1',
        'pays' => 'Canada',
        'numero' => '123',
        'province' => 'QC'
    ];

    $userModel = new App\Modele\UserModel($pdo);
    $result = $userModel->addUserDB($userData);
    echo "Insertion réussie: $result<br>";
} catch (Exception $e) {
    echo "Erreur lors de l'insertion: " . $e->getMessage() . "<br>";
}

// Vérifier les tables nécessaires
$tables = ['utilisateur', 'adresse', 'utilisateur_adresse', 'role', 'role_utilisateur'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "Table $table: $count enregistrements<br>";
    } catch (PDOException $e) {
        echo "Erreur avec la table $table: " . $e->getMessage() . "<br>";
    }
}

// Vérifier les rôles
try {
    $stmt = $pdo->query("SELECT * FROM role");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Rôles disponibles: " . print_r($roles, true) . "<br>";
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des rôles: " . $e->getMessage() . "<br>";
}
?>
