<?php
namespace App\Controlleur;

require_once __DIR__ . '/../../config/db.php';

use App\Modele\CommandeModel;
use App\Modele\UserModel;
use PDOException;

class CommandeControlleur {
    private $commandeModel; 
    private $pdo;

    public function __construct(CommandeModel $commandeModel) {
        $this->commandeModel = $commandeModel;
        $this->pdo = $GLOBALS['pdo'];
    }
    
    public function index() {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier si l'utilisateur est connecté et est admin
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit();
        }
        
        // Vérifier si on filtre par utilisateur
        $userId = $_GET['user_id'] ?? null;
        
        if ($userId) {
            // Récupérer les informations de l'utilisateur
            $userModel = new UserModel($this->pdo);
            $userData = $userModel->getUserById($userId);
            
            if (!$userData) {
                $_SESSION['error'] = "Utilisateur non trouvé";
                header('Location: /admin/utilisateurs');
                exit();
            }
            
            // Récupérer les commandes de l'utilisateur
            $commandes = $this->commandeModel->getCommandesByUser($userId);
            $userName = ($userData['prenom'] ?? '') . ' ' . ($userData['nom_utilisateur'] ?? '');
            
            // Afficher la vue des commandes avec le filtre utilisateur
            require_once __DIR__ . '/../Vue/admin/commandes.php';
        } else {
            // Récupérer toutes les commandes
            $commandes = $this->commandeModel->getAllCommandes();
            require_once __DIR__ . '/../Vue/Commandes.php';
        }
    }
    
    /**
     * Affiche les commandes d'un utilisateur spécifique pour l'administration
     * 
     * @param int $user_id L'ID de l'utilisateur
     */
    public function adminCommandesUtilisateur($user_id) {
        // Vérifier si l'utilisateur est connecté et est admin
        if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit();
        }
        
        try {
            // Récupérer les informations de l'utilisateur
            $userModel = new UserModel($this->pdo);
            $userData = $userModel->getUserById($user_id);
            
            if (!$userData) {
                throw new \Exception("Utilisateur non trouvé");
            }
            
            // Récupérer les commandes de l'utilisateur
            $commandes = $this->commandeModel->getCommandesByUser($user_id);
            
            // Afficher la vue
            require_once __DIR__ . '/../Vue/admin/commandes.php';
            
        } catch (\Exception $e) {
            // Gérer l'erreur
            error_log("Erreur dans adminCommandesUtilisateur: " . $e->getMessage());
            $_SESSION['error'] = "Une erreur est survenue lors de la récupération des commandes.";
            header('Location: /admin');
            exit();
        }
    }
    
    // Méthode pour lister les commandes
    public function listCommandes() {
        return $this->commandeModel->getAllCommandes();
    }
    
    // Afficher une commande spécifique
    public function afficherCommande($id_commande) {
        try {
            $commande = $this->commandeModel->getCommandeById($id_commande);
            if ($commande) {
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode($commande);
                } else {
                    // Afficher la vue de détail de la commande
                    require_once __DIR__ . '/../Vue/commande_detail.php';
                }
            } else {
                throw new \Exception("Commande introuvable.");
            }
        } catch (\Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                http_response_code(404);
                echo json_encode(['error' => $e->getMessage()]);
            } else {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /commandes');
                exit();
            }
        }
    }
    
    // Ajouter une nouvelle commande
    public function ajouterCommande($data) {
        try {
            $id_commande = $this->commandeModel->addCommande($data);
            if ($id_commande) {
                unset($_SESSION['panier']);
                header('Location: /mon_profile');
                exit();
            } else {
                throw new \Exception("Erreur lors de l'ajout de la commande.");
            }
        } catch (\Exception $e) {
            echo "Erreur lors de l'ajout de la commande : " . $e->getMessage();
        }
    }
    
    public function afficherTotalCommande($id_commande) {
        try {
            return $this->commandeModel->getOrderTotal($id_commande);
        } catch (PDOException $e) {
            error_log("Erreur PDO dans afficherTotalCommande: " . $e->getMessage());
            return false;
        }
    }
    
    
    // Méthode pour mettre à jour une commande
    public function modifierCommande($id_commande, $action) {
        $statuts = [
            'traiter' => 'en_attente',
            'expedier' => 'expediee',
            'annuler' => 'annulee',
            'livrer' => 'livree',
            'payer' => 'payee'
        ];
        
        if (!isset($statuts[$action])) {
            throw new \Exception("Action inconnue : $action");
        }
        
        $statut = $statuts[$action];
        
        if ($this->commandeModel->updateCommande($id_commande, $statut)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            throw new \Exception("Échec de la mise à jour de la commande.");
        }
    }
}
?>