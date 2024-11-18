<?php
namespace App\Controlleur;

class AuthControlleur {
    
    public function loginForm() {
        // Afficher le formulaire de connexion
        require '../src/vue/auth/login.php';
    }

    public function login() {
        // Traiter les données de connexion (ex: vérifier les identifiants)
        // Rediriger vers une autre page en cas de succès
    }

    public function registerForm() {
        // Afficher le formulaire d'inscription
        require '../src/vue/auth/register.php';
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
