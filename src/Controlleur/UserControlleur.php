<?php

namespace app\Controlleur;
use App\Modele\UserModel;
use Exception;

class UserControlleur {
    private $userModel;
    private $db;
    
    // Le constructeur accepte maintenant directement un UserModel
    public function __construct($userModel) {
        $this->userModel = $userModel;
    }
    public function index() {
        try {
            $users = $this->userModel->getAllUsers();
            require __DIR__ . '/../vue/users.php';
        } catch (\Exception $e) {
            echo "Erreur lors de la récupération des utilisateurs : " . $e->getMessage();
        }
    }

    // Méthode pour enregistrer un nouvel utilisateur
    public function registerUser($user) {
        try {
            // Valider les données de l'utilisateur
            $this->userModel->validateUserData(
                $user['couriel'], 
                $user['password'], 
                $user['cpassword'], 
                $user['datNaiss']
            );

            // Ajouter l'utilisateur dans la base de données
            $result = $this->userModel->addUserDB($user);

            if ($result === true) {
                // Renvoie un message de succès uniquement
                return ['success' => true, 'message' => 'L\'utilisateur a été ajouté avec succès !'];
            } else {
                // Retourne l'erreur si l'ajout échoue
                return ['success' => false, 'message' => $result];
            }
        } catch (Exception $e) {
            // Gérer les exceptions
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }

   
    // Method to fetch all users
    public function listAllUsers() {
        return $this->userModel->getAllUsers();
    }

    // Method to get user information by user ID
    public function getUserInfo($id_utilisateur) {
        return $this->userModel->getUserInfo($id_utilisateur);
    }
    public function profile() {
        // Assurez-vous que l'utilisateur est connecté
        if (!isset($_SESSION['id_utilisateur'])) {
            header("Location: connexion.php");
            exit;
        }

        // Récupération de l'ID de l'utilisateur depuis la session
        $userId = $_SESSION['id_utilisateur'];

        // Appel de la fonction getUserInfo du modèle
        $userInfo = $this->userModel->getUserInfo($userId);

        // Passer les informations de l'utilisateur à la vue
        include '../Vue/Profile.php';
    }

    // Method to authenticate user (login)
    public function loginUser($email, $password) {
        $user = $this->userModel->checkUser($email, $password);
        
        if ($user) {
            // Store user session or token
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['role'] = $user['role'];
            return true;
        } else {
            return false;
        }
    }

    // Method to display the registration form (optional)
    public function showRegistrationForm() {
        include 'views/register.php'; // Assumes you have a view for registration
    }

    // Method to display user login form (optional)
    public function showLoginForm() {
        include 'views/login.php'; // Assumes you have a view for login
    }

    // Method to handle the logout (optional)
    public function logoutUser() {
        session_destroy();
        header('Location: index.php'); // Redirect to home page after logout
    }

    // Fonction pour récupérer les commandes d'un utilisateur
    public function getUserOrders($userId) {
        // Appeler la fonction getUserCommandWithStatus en passant la connexion PDO
        return $this->userModel->getUserCommandWithStatus($userId);
    }
}

?>
