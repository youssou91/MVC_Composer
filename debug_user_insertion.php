<?php
/**
 * Script de débogage pour tester l'insertion d'utilisateur
 * 
 * Ce script permet de tester le processus d'inscription d'un utilisateur
 * en contournant le contrôleur pour un débogage plus facile.
 */

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session
session_start();

// Définir le fuseau horaire
date_default_timezone_set('America/Toronto');

// Définir le mode débogage
define('DEBUG_MODE', true);

// Inclure l'autoloader de Composer
require_once __DIR__ . '/vendor/autoload.php';

// Inclure le fichier de configuration de la base de données
require_once __DIR__ . '/config/database.php';

// Inclure le modèle utilisateur
require_once __DIR__ . '/src/Modele/UserModel.php';

// Fonction pour afficher les en-têtes de débogage
function debugHeader($title) {
    echo "<div style='background-color: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 5px solid #007bff;'>";
    echo "<h3 style='margin: 0; color: #007bff;'>$title</h3>";
    echo "</div>";
}

// Fonction pour afficher les données de débogage
function debugData($data, $title = 'Débogage') {
    echo "<div style='background-color: #f8f9fa; padding: 15px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;'>";
    echo "<h4 style='margin-top: 0; color: #6c757d;'>$title</h4>";
    echo "<pre style='margin: 0; font-family: monospace;'>";
    if (is_string($data)) {
        echo htmlspecialchars($data);
    } else {
        print_r($data);
    }
    echo "</pre>";
    echo "</div>";
}

// Obtenir la connexion à la base de données
try {
    debugHeader("Connexion à la base de données");
    $db = getConnection();
    echo "<p style='color: green;'>Connexion à la base de données réussie!</p>";
    
    // Afficher les informations de connexion
    $dbInfo = [
        'Base de données' => DB_NAME,
        'Hôte' => DB_HOST,
        'Port' => DB_PORT,
        'Utilisateur' => DB_USER,
        'Charset' => DB_CHARSET
    ];
    debugData($dbInfo, 'Configuration de la base de données');
    
} catch (PDOException $e) {
    die("<div style='color: red; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;'>" .
        "<h3>Erreur de connexion à la base de données</h3>" . 
        "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>" .
        "<p><strong>Code:</strong> " . $e->getCode() . "</p>" .
        "<p><strong>Fichier:</strong> " . $e->getFile() . " (ligne " . $e->getLine() . ")</p>" .
        "</div>");
}

// Créer une instance du modèle utilisateur
try {
    debugHeader("Initialisation du modèle utilisateur");
    $userModel = new App\Modele\UserModel($db);
    echo "<p style='color: green;'>Modèle utilisateur initialisé avec succès!</p>";
} catch (Exception $e) {
    die("<div style='color: red; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;'>" .
        "<h3>Erreur lors de l'initialisation du modèle utilisateur</h3>" . 
        "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>" .
        "<p><strong>Fichier:</strong> " . $e->getFile() . " (ligne " . $e->getLine() . ")</p>" .
        "</div>");
}

// Données de test pour l'utilisateur
$testUser = [
    'nom_utilisateur' => 'TestUser',
    'prenom' => 'Test',
    'couriel' => 'test' . time() . '@example.com',
    'password' => 'Test123!',
    'telephone' => '1234567890',
    'datNaiss' => '1990-01-01',
    'rue' => '123 Test Street',
    'ville' => 'Montreal',
    'code_postal' => 'H1A 1A1',
    'pays' => 'Canada',
    'numero' => '123',
    'province' => 'QC'
];

echo "<h1>Test d'insertion d'utilisateur</h1>";
echo "<h2>Données de test:</h2>";
echo "<pre>";
print_r($testUser);
echo "</pre>";

try {
    // Tester l'insertion de l'utilisateur
    echo "<h2>Résultat de l'insertion:</h2>";
    $result = $userModel->addUserDB($testUser);
    echo "<p style='color: green;'>Succès: $result</p>";
    
    // Afficher les utilisateurs dans la base de données
    echo "<h2>Utilisateurs dans la base de données:</h2>";
    $stmt = $db->query("SELECT id_utilisateur, nom_utilisateur, prenom, couriel FROM utilisateur ORDER BY id_utilisateur DESC LIMIT 5");
    $users = $stmt->fetchAll();
    echo "<pre>";
    print_r($users);
    echo "</pre>";
    
    // Afficher les adresses
    echo "<h2>Dernières adresses:</h2>";
    $stmt = $db->query("SELECT * FROM adresse ORDER BY id_adresse DESC LIMIT 5");
    $addresses = $stmt->fetchAll();
    echo "<pre>";
    print_r($addresses);
    echo "</pre>";
    
    // Afficher les associations utilisateur-adresse
    echo "<h2>Associations utilisateur-adresse:</h2>";
    $stmt = $db->query("SELECT * FROM utilisateur_adresse ORDER BY id_utilisateur DESC LIMIT 5");
    $userAddresses = $stmt->fetchAll();
    echo "<pre>";
    print_r($userAddresses);
    echo "</pre>";
    
    // Afficher les rôles des utilisateurs
    echo "<h2>Rôles des utilisateurs:</h2>";
    $stmt = $db->query("
        SELECT u.id_utilisateur, u.nom_utilisateur, r.description as role 
        FROM utilisateur u
        JOIN role_utilisateur ru ON u.id_utilisateur = ru.id_utilisateur
        JOIN role r ON ru.id_role = r.id_role
        ORDER BY u.id_utilisateur DESC 
        LIMIT 5
    ");
    $userRoles = $stmt->fetchAll();
    echo "<pre>";
    print_r($userRoles);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
    
    // Afficher les rôles disponibles
    echo "<h2>Rôles disponibles dans la base de données:</h2>";
    try {
        $stmt = $db->query("SELECT * FROM role");
        $roles = $stmt->fetchAll();
        echo "<pre>";
        print_r($roles);
        echo "</pre>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>Impossible de récupérer les rôles: " . $e->getMessage() . "</p>";
    }
}

// Afficher les erreurs PDO
$errorInfo = $db->errorInfo();
if ($errorInfo[0] !== '00000') {
    echo "<h2>Erreur PDO:</h2>";
    echo "<pre>";
    print_r($errorInfo);
    echo "</pre>";
}
?>

<h2>Test terminé</h2>
<p>Vérifiez les journaux d'erreurs PHP pour plus de détails.</p>
