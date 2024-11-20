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
        // $produitModel = new ProduitModel($this->db);
        // $products = $produitModel->getAllProduits(); 
        require_once '../src/vue/Profile.php';
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

    //getuserinfos
    public function getUserInfo($id) {
        $userModel = new UserModel($this->db);
        $user = $userModel->getUserInfo($id);
        
        if ($user) {
            return $user;
        } else {
            return null;
        }
    }
    //getUserOrders
    public function getUserOrders($userId) {
        $userModel = new UserModel($this->db);
        $orders = $userModel->getUserOrders($userId);
        
        if ($orders) {
            return $orders;
        } else {
            return null;
        }
    }
}
?>
