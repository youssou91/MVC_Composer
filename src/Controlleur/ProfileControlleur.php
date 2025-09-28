<?php
namespace App\Controlleur;
use App\Modele\UserModel;
require_once __DIR__ . '/../../config/db.php';

class ProfileControlleur {
    private $db;
    public function __construct() {
        $this->db = getConnection(); 
    }

    public function index() {
        require_once __DIR__ . '/../Vue/Profile.php';
    }
    
    public function updateProfile($data) {
        $userModel = new UserModel($this->db);
        $user = $userModel->getUserById($data['id']);
        
        if ($user) {
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setEmail($data['email']);
            $user->setPassword($data['password']);
            
            $userModel->updateUser($user);
            header('Location: /dashboard/profile');
        } else {
            header('Location: /dashboard/profile?error=User not found');
        }
    }
    
    public function deleteProfile($id) {
        $userModel = new UserModel($this->db);
        $user = $userModel->getUserById($id);
        
        if ($user) {
            $userModel->deleteUser($user);
            header('Location: /dashboard/logout');
        } else {
            header('Location: /dashboard/profile?error=User not found');
        }
    }
    
    public function getUserOrders($orderId) {
        $userModel = new UserModel($this->db);
        $orders = $userModel->getUserOrders($orderId);
        return $orders;
    }
    
    public function getUserInfo($id) {
        $userModel = new UserModel($this->db);
        $user = $userModel->getUserInfo($id);
        if ($user) {
            return $user;
        } else {
            return null;
        }
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////
    //fonction pour appaler la page de paiement
    
    public function payOrder($id_commande) {
        $userModel = new UserModel($this->db);
        require_once __DIR__ . '/../Vue/Paiement.php'; // Chemin vers la vue Paiement
        
    }
    //detailsOrder
    public function getOrderDetails($id_commande) {
        $userModel = new UserModel($this->db);
        require_once __DIR__ . '/../Vue/details_commande.php'; // Chemin vers la vue Paiement
    }
    //fonction pour changer le status de la commande
    public function changeOrderStatus($id_commande, $status) {
        $userModel = new UserModel($this->db);
        $userModel->changeOrderStatus($id_commande, $status);
    }
    
    /**
     * Affiche les détails d'un utilisateur
     * @param int $id ID de l'utilisateur
     */
    public function showUser($id) {
        $userModel = new UserModel($this->db);
        $userData = $userModel->getUserInfo($id);
        
        if ($userData) {
            // Convertir le tableau associatif en objet
            $user = (object) $userData;
            require_once __DIR__ . '/../Vue/user_profile.php';
        } else {
            header('HTTP/1.0 404 Not Found');
            echo 'Utilisateur non trouvé';
        }
    }
    
    /**
     * Affiche les commandes d'un utilisateur
     * @param int $id ID de l'utilisateur
     */
    public function userOrders($id) {
        $userModel = new UserModel($this->db);
        $userData = $userModel->getUserInfo($id);
        
        if ($userData) {
            // Convertir le tableau associatif en objet
            $user = (object) $userData;
            $orders = $userModel->getUserOrders($id);
            require_once __DIR__ . '/../Vue/user_orders.php';
        } else {
            header('HTTP/1.0 404 Not Found');
            echo 'Utilisateur non trouvé';
        }
    }
    
    /**
     * Affiche le formulaire de modification d'un utilisateur
     * @param int $id ID de l'utilisateur
     */
    public function editUserForm($id) {
        $userModel = new UserModel($this->db);
        $userData = $userModel->getUserInfo($id);
        
        if ($userData) {
            // Convertir le tableau associatif en objet
            $user = (object) $userData;
            require_once __DIR__ . '/../Vue/edit_user.php';
        } else {
            header('HTTP/1.0 404 Not Found');
            echo 'Utilisateur non trouvé';
        }
    }
}
?>
