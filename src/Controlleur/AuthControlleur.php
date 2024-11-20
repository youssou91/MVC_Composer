<?php
namespace App\Controlleur;
use AltoRouter; // Importer AltoRouter

class AuthControlleur {
    
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

    public function register() {
        // Traiter les données d'inscription (ex: validation, création d'un utilisateur)
        // Rediriger vers une autre page en cas de succès
    }

    public function logout() {
        // Déconnecter l'utilisateur
        // Rediriger vers la page d'accueil
        session_destroy();
        require_once __DIR__ . '/../Vue/auth/deconnexion.php'; 
    }
}
?>
