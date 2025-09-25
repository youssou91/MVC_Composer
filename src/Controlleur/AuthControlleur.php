<?php
namespace App\Controlleur;
use AltoRouter; 
use App\Modele\UserModel;
use Exception;

class AuthControlleur {
    private $db;
    
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

    /**
     * Gère à la fois l'affichage du formulaire d'inscription et le traitement de la soumission
     */
    public function register() {
        // Démarrer la session si ce n'est pas déjà fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Si c'est une soumission de formulaire (méthode POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                error_log("Tentative d'inscription - Données reçues: " . print_r($_POST, true));
                
                // Récupération et validation des données du formulaire
                $userData = [
                    'nom_utilisateur' => trim($_POST['nom_utilisateur'] ?? ''),
                    'prenom' => trim($_POST['prenom'] ?? ''),
                    'couriel' => trim($_POST['couriel'] ?? ''),
                    'password' => $_POST['password'] ?? '',
                    'telephone' => trim($_POST['telephone'] ?? ''),
                    'datNaiss' => $_POST['datNaiss'] ?? '',
                    'rue' => trim($_POST['rue'] ?? ''),
                    'ville' => trim($_POST['ville'] ?? ''),
                    'code_postal' => trim($_POST['code_postal'] ?? ''),
                    'pays' => trim($_POST['pays'] ?? 'Canada'),
                    'numero' => trim($_POST['numero'] ?? ''),
                    'province' => trim($_POST['province'] ?? '')
                ];

                // Validation des données utilisateur
                $requiredFields = [
                    'nom_utilisateur' => 'Le nom d\'utilisateur est requis',
                    'couriel' => 'L\'adresse email est requise',
                    'password' => 'Le mot de passe est requis',
                    'rue' => 'L\'adresse est requise',
                    'ville' => 'La ville est requise',
                    'code_postal' => 'Le code postal est requis',
                    'province' => 'La province est requise',
                    'numero' => 'Le numéro de rue est requis'
                ];
                
                $errors = [];
                foreach ($requiredFields as $field => $errorMessage) {
                    if (empty($userData[$field])) {
                        $errors[] = $errorMessage;
                    }
                }
                
                // Validation de l'email
                if (!filter_var($userData['couriel'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'L\'adresse email n\'est pas valide';
                }
                
                if (!empty($errors)) {
                    throw new Exception(implode("\n", $errors));
                }
                
                // Validation du mot de passe
                if (strlen($userData['password']) < 8) {
                    throw new Exception('Le mot de passe doit contenir au moins 8 caractères');
                }
                
                // Création de l'utilisateur
                $userModel = new UserModel($this->db);
                $result = $userModel->addUserDB($userData);
                
                // Redirection vers la page de connexion avec un message de succès
                $_SESSION['success_message'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                header('Location: /login');
                exit();
                
            } catch (Exception $e) {
                // En cas d'erreur, on réaffiche le formulaire avec les erreurs
                error_log("Erreur lors de l'inscription: " . $e->getMessage());
                $error = $e->getMessage();
                
                // On conserve les données saisies pour les réafficher dans le formulaire
                $formData = $_POST;
                
                // Stocker le message d'erreur dans la session pour l'afficher après redirection
                $_SESSION['error'] = $error;
                $_SESSION['form_data'] = $formData;
                
                // Rediriger vers la même page pour éviter la soumission multiple du formulaire
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
        
        // Affichage du formulaire d'inscription (pour les requêtes GET ou en cas d'erreur de validation)
        $viewPath = dirname(__DIR__, 2) . '/src/Vue/auth/inscription.php';
        $viewPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $viewPath);
        
        if (file_exists($viewPath)) {
            // Variables disponibles dans la vue
            $title = 'Inscription';
            $error = $error ?? null;
            $formData = $formData ?? [];
            
            // Inclure la vue
            require $viewPath;
        } else {
            // Gestion d'erreur si le fichier de vue n'existe pas
            die("Erreur : Impossible de charger le formulaire d'inscription. Le fichier est introuvable.");
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /login'); 
        exit();
    }
}

?>
