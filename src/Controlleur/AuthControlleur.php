<?php
namespace App\Controlleur;
use AltoRouter; // Importer AltoRouter
use App\Modele\UserModel;

class AuthControlleur {
    private $db;
    private $userModel;
    public function __construct() {
        $this->db = getConnection(); 
        // $this->userModel = new UserModel($dbConnection);
    }

    

    public function loginForm()
    {
        // Initialisation du routeur
        $router = new AltoRouter();

        // Définition des routes
        $router->map('GET', '/', 'accueil', 'accueil');
        $router->map('GET', '/connexion', 'connexion', 'connexion');

        // Récupérer la route actuelle
        $match = $router->match();

        // Vous pouvez maintenant passer l'instance du routeur à la vue
        require_once __DIR__ . '/../Vue/auth/connexion.php'; // Exemple de chemin vers la vue
    }
    

    // public function login() {
    //     // Traiter les données de connexion (ex: vérifier les identifiants)
    //     // Rediriger vers une autre page en cas de succès
    // }

    public function registerForm() {
        $viewPath = $_SERVER['DOCUMENT_ROOT'] . '../../src/vue/auth/inscription.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Erreur : La vue 'connexion.php' est introuvable. Vérifiez le chemin.";
        }
    }

    // Méthode pour enregistrer un nouvel utilisateur

    public function registerUser($user) {
        try {
            // Valider les données de l'utilisateur avant l'ajout
            // $this->validateUserData($user['couriel'], $user['password'], $user['cpassword'], $user['datNaiss']);

            // Ajouter l'utilisateur à la base de données
            $message = $this->addUserDB($user);

            // Si l'ajout est réussi, retournez le message de succès
            return $message;
            
        } catch (Exception $e) {
            // En cas d'erreur, afficher un message d'erreur
            return "Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage();
        }
    }

    public function addUserDB($user) {
        // Démarrer une transaction
        $this->db->beginTransaction();

        try {
            // Insérer l'utilisateur, l'adresse, et les associer
            $id_utilisateur = $this->insertUser($user);
            $id_adresse = $this->insertAddress($user);
            $this->associateUserAddress($id_utilisateur, $id_adresse);
            $this->assignUserRole($id_utilisateur, 'client');

            // Commit de la transaction
            $this->db->commit();
            return "L'utilisateur a été ajouté avec succès.";
        } catch (Exception $e) {
            // Rollback si une erreur se produit
            $this->db->rollBack();
            return "Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage();
        }
    }

    public function logout() {
        // session_start();
        session_unset();
        session_destroy();
        header('Location: /login'); 
        exit();
    }
}

?>
