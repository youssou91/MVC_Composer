<?php
namespace App\Controlleur;
use AltoRouter; 
use App\Modele\UserModel;

class AuthControlleur {
    private $db;
    private $userModel;
    public function __construct() {
        $this->db = getConnection(); 
    }

    public function loginForm()
    {
        $router = new AltoRouter();
        $router->map('GET', '/', 'accueil', 'accueil');
        $router->map('GET', '/connexion', 'connexion', 'connexion');
        $match = $router->match();
        require_once __DIR__ . '/../Vue/auth/connexion.php'; 
    }

    public function registerForm() {
        $viewPath = $_SERVER['DOCUMENT_ROOT'] . '../../src/vue/auth/inscription.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Erreur : La vue 'connexion.php' est introuvable. Vérifiez le chemin.";
        }
    }

    public function registerUser($user) {
        try {
            error_log(print_r($user, true));
            $message = $this->addUserDB($user);
            return $message;
        } catch (Exception $e) {
            return "Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage();
        }
    }
    

    public function addUserDB($user) {
        $this->db->beginTransaction();
        try {
            $id_utilisateur = $this->addUserDB($user);
            $id_adresse = $this->insertAddress($user);
            $this->associateUserAddress($id_utilisateur, $id_adresse);
            $this->assignUserRole($id_utilisateur, 'client');
            $this->db->commit();
            return "L'utilisateur a été ajouté avec succès.";
        } catch (Exception $e) {
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
