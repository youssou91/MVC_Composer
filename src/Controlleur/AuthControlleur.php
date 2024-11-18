<?php
namespace App\Controlleur;

class AuthControlleur {
    
    public function loginForm() {
        $viewPath = $_SERVER['DOCUMENT_ROOT'] . '../../src/vue/auth/connexion.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Erreur : La vue 'connexion.php' est introuvable. Vérifiez le chemin.";
        }
    }
    

    public function login() {
        // Traiter les données de connexion (ex: vérifier les identifiants)
        // Rediriger vers une autre page en cas de succès
    }

    public function registerForm() {
        $viewPath = $_SERVER['DOCUMENT_ROOT'] . '../../src/vue/auth/inscription.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Erreur : La vue 'connexion.php' est introuvable. Vérifiez le chemin.";
        }
    }

    public function register() {
        // Traiter les données d'inscription (ex: validation, création d'un utilisateur)
        // Rediriger vers une autre page en cas de succès
    }

    public function logout() {
        // Déconnecter l'utilisateur
        // Rediriger vers la page d'accueil
    }
}
?>
