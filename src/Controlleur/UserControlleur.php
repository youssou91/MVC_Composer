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
    /**
     * Change le statut d'un utilisateur (actif/inactif)
     * 
     * @param int $userId ID de l'utilisateur
     * @param string $newStatus Nouveau statut ('actif' ou 'inactif')
     * @return array Réponse JSON
     */
    public function toggleUserStatus($userId, $newStatus) {
        header('Content-Type: application/json');
        
        try {
            error_log("=== Début de toggleUserStatus ===");
            error_log("Paramètres reçus - userId: $userId, newStatus: $newStatus");
            
            // Démarrer la session si elle n'est pas déjà démarrée
            if (session_status() === PHP_SESSION_NONE) {
                error_log("Démarrage de la session...");
                session_start();
            }
            
            error_log('Données de session: ' . print_r($_SESSION, true));
            error_log('Tentative de changement de statut - UserID: ' . $userId . ', Nouveau statut: ' . $newStatus);
            
            // Vérifier que l'utilisateur connecté est un administrateur
            if (!isset($_SESSION['id_utilisateur'])) {
                $errorMsg = 'Erreur: Aucun utilisateur connecté';
                error_log($errorMsg);
                http_response_code(403);
                return json_encode([
                    'success' => false,
                    'message' => $errorMsg,
                    'session' => $_SESSION
                ]);
            }
            
            error_log("Utilisateur connecté - ID: " . $_SESSION['id_utilisateur']);
            
            // Vérifier le rôle de l'utilisateur connecté
            $currentUserRole = $this->userModel->getUserRole($_SESSION['id_utilisateur']);
            error_log("Rôle de l'utilisateur connecté: " . $currentUserRole);
            
            if ($currentUserRole !== 'admin') {
                $errorMsg = 'Erreur: Accès non autorisé. Rôle requis: admin, Rôle actuel: ' . $currentUserRole;
                error_log($errorMsg);
                http_response_code(403);
                return json_encode([
                    'success' => false,
                    'message' => 'Accès non autorisé. Rôle admin requis',
                    'role' => $currentUserRole
                ]);
            }
            
            error_log("Vérification des autorisations réussie - utilisateur est admin");
            
            // Valider le statut
            $newStatus = strtolower($newStatus);
            error_log("Statut après normalisation: $newStatus");
            
            if (!in_array($newStatus, ['actif', 'inactif'])) {
                $errorMsg = 'Erreur: Statut invalide. Utilisez "actif" ou "inactif"';
                error_log($errorMsg);
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'message' => $errorMsg,
                    'status_received' => $newStatus
                ]);
            }
            
            error_log("Validation du statut réussie - nouveau statut: $newStatus");
            
            // Vérifier si l'utilisateur est un administrateur
            $userRole = $this->userModel->getUserRole($userId);
            error_log("Rôle de l'utilisateur $userId: " . $userRole);
            
            if ($userRole === 'admin' && strtolower($newStatus) === 'inactif') {
                $errorMsg = 'Impossible de désactiver un compte administrateur';
                error_log($errorMsg);
                http_response_code(403);
                return json_encode([
                    'success' => false,
                    'message' => $errorMsg
                ]);
            }
            
            // Mettre à jour le statut
            error_log("Appel à userModel->updateUserStatus($userId, $newStatus)");
            $success = $this->userModel->updateUserStatus($userId, $newStatus);
            
            if ($success) {
                $successMsg = 'Statut utilisateur mis à jour avec succès';
                error_log($successMsg);
                return json_encode([
                    'success' => true,
                    'message' => $successMsg,
                    'newStatus' => $newStatus
                ]);
            } else {
                $errorMsg = 'Échec de la mise à jour du statut dans la base de données';
                error_log($errorMsg);
                throw new Exception($errorMsg);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut: ' . $e->getMessage()
            ]);
        }
    }
    
    public function index() {
        try {
            error_log("=== DÉBUT UserControlleur::index() ===");
            
            // Vérifier si le modèle est bien défini
            if (!$this->userModel) {
                error_log("ERREUR: Le modèle utilisateur n'est pas initialisé");
                throw new \Exception("Erreur d'initialisation du modèle utilisateur");
            }
            
            // Récupérer les utilisateurs avec leurs commandes
            try {
                error_log("Appel à userModel->getUsersWithOrders()");
                $users = $this->userModel->getUsersWithOrders();
                error_log(sprintf("Récupération de %d utilisateurs", count($users)));
                error_log("Données brutes des utilisateurs: " . print_r($users, true));
            } catch (\Exception $e) {
                error_log("ERREUR lors de la récupération des utilisateurs: " . $e->getMessage());
                error_log("Trace: " . $e->getTraceAsString());
                throw new \Exception("Erreur lors de la récupération des utilisateurs: " . $e->getMessage());
            }
            
            // Préparer les données pour la vue
            $usersData = [];
            foreach ($users as $user) {
                try {
                    error_log("Traitement de l'utilisateur ID: " . ($user['id_utilisateur'] ?? 'inconnu'));
                    
                    // Vérifier si l'utilisateur est un administrateur
                    $isAdmin = !empty($user['role']) && (strpos($user['role'], 'admin') !== false || $user['role'] === 'admin');
                    
                    $userData = [
                        'id_utilisateur' => $user['id_utilisateur'] ?? null,
                        'nom_utilisateur' => $user['nom_utilisateur'] ?? 'Non défini',
                        'prenom' => $user['prenom'] ?? '',
                        'couriel' => $user['couriel'] ?? '',
                        'telephone' => $user['telephone'] ?? '',
                        'statut' => $user['statut'] ?? 'actif',
                        'role' => $isAdmin ? 'admin' : 'user',
                        'nb_commandes' => 0,
                        'derniere_commande' => null,
                        'montant_total' => 0
                    ];
                    
                    // Initialiser le tableau des commandes si non défini
                    if (!isset($user['commandes'])) {
                        $user['commandes'] = [];
                    }
                    
                    // Calculer le montant total et la dernière commande
                    if (!empty($user['commandes']) && is_array($user['commandes'])) {
                        $userData['nb_commandes'] = count($user['commandes']);
                        if (isset($user['commandes'][0]['date_commande'])) {
                            $userData['derniere_commande'] = $user['commandes'][0]['date_commande'];
                        }
                        
                        foreach ($user['commandes'] as $commande) {
                            if (isset($commande['prix_total'])) {
                                $userData['montant_total'] += (float)$commande['prix_total'];
                            }
                        }
                    }
                    
                    $usersData[] = $userData;
                } catch (\Exception $e) {
                    error_log("Erreur lors du traitement d'un utilisateur: " . $e->getMessage());
                    // Continuer avec l'utilisateur suivant même en cas d'erreur
                    continue;
                }
            }
            
            // Rendre les données disponibles pour la vue
            $GLOBALS['usersData'] = $usersData;
            
            // Inclure la vue
            require __DIR__ . '/../Vue/users.php';
        } catch (\Exception $e) {
            error_log("Erreur dans UserControlleur::index(): " . $e->getMessage());
            throw new \Exception("Impossible de récupérer les utilisateurs. Veuillez réessayer plus tard.");
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
            // Démarrer la session si elle n'est pas déjà démarrée
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Stocker les informations de l'utilisateur dans la session
            $_SESSION['user'] = [
                'id' => $user['id_utilisateur'],
                'email' => $user['couriel'],
                'nom' => $user['nom_utilisateur'],
                'prenom' => $user['prenom'],
                'role' => $user['role']
            ];
            
            // Stocker également les informations directement dans la racine de la session pour compatibilité
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
