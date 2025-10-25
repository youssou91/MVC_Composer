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
        try {
            // Démarrer la session si elle n'est pas déjà démarrée
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Vérifier si l'utilisateur est connecté et est admin
            if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'admin') {
                header('Location: /login');
                exit();
            }
            
            return $this->commandeModel->getAllCommandes();
        } catch (\Exception $e) {
            error_log("Erreur dans listCommandes: " . $e->getMessage());
            return [];
        }
    }
    
    // Afficher une commande spécifique
    public function afficherCommande($id_commande) {
        // Activer l'affichage des erreurs
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        // Définir le type de contenu en JSON
        header('Content-Type: application/json');
        
        try {
            // Démarrer la session si elle n'est pas déjà démarrée
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['id_utilisateur'])) {
                throw new \Exception("Vous devez être connecté pour voir les détails d'une commande.");
            }
            
            // Récupérer la commande
            $commande = $this->commandeModel->getCommandeById($id_commande);
            
            if (!$commande) {
                throw new \Exception("Commande introuvable.");
            }
            
            // Vérifier que l'utilisateur est soit admin, soit le propriétaire de la commande
            $estAdmin = ($_SESSION['role'] ?? '') === 'admin';
            $estProprietaire = ($_SESSION['id_utilisateur'] == $commande['id_utilisateur']);
            
            if (!$estAdmin && !$estProprietaire) {
                throw new \Exception("Vous n'êtes pas autorisé à voir cette commande.");
            }
            
            // Préparer la réponse avec les données imbriquées dans 'commande'
            $response = [
                'success' => true,
                'commande' => [
                    'id_commande' => $commande['id_commande'],
                    'date_commande' => $commande['date_commande'],
                    'statut' => $commande['statut'],
                    'sous_total' => $commande['sous_total'] ?? 0,
                    'frais_livraison' => $commande['frais_livraison'] ?? 0,
                    'total' => $commande['total'] ?? 0,
                    'client' => [
                        'nom_complet' => trim(($commande['prenom'] ?? '') . ' ' . ($commande['nom_utilisateur'] ?? '')),
                        'email' => $commande['email'] ?? 'non-specifie@example.com',
                        'telephone' => $commande['telephone'] ?? ''
                    ],
                    'adresse_livraison' => [
                        'ligne1' => ($commande['numero'] ?? '') . ' ' . ($commande['rue'] ?? ''),
                        'code_postal' => $commande['code_postal'] ?? '',
                        'ville' => $commande['ville'] ?? '',
                        'pays' => $commande['pays'] ?? 'Canada'
                    ],
                    'articles' => $commande['articles'] ?? []
                ]
            ];
            
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch (\Exception $e) {
            error_log('Erreur dans afficherCommande: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            // Envoyer une réponse d'erreur détaillée
            header('HTTP/1.1 500 Internal Server Error');
            $errorResponse = [
                'success' => false,
                'error' => 'Erreur lors de la récupération de la commande avec ID ' . $id_commande,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            
            echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit();
        }
    }
    
    /**
     * Ajoute une nouvelle commande
     * 
     * @param array $data Les données de la commande
     * @return int L'ID de la commande créée
     * @throws \Exception En cas d'erreur lors de l'ajout
     */
    public function ajouterCommande($data) {
        try {
            $id_commande = $this->commandeModel->addCommande($data);
            if ($id_commande) {
                unset($_SESSION['panier']);
                return $id_commande;
            } else {
                throw new \Exception("Erreur lors de l'ajout de la commande.");
            }
        } catch (\Exception $e) {
            error_log("Erreur dans ajouterCommande: " . $e->getMessage());
            throw $e;
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
    
    
    /**
     * Met à jour le statut d'une commande
     * 
     * @param int $id_commande L'ID de la commande à mettre à jour
     * @param string $action L'action à effectuer (traiter, expedier, livrer, annuler, payer)
     * @throws \Exception Si l'action est inconnue ou en cas d'échec de la mise à jour
     */
    public function modifierCommande($id_commande, $action) {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Vérifier le jeton CSRF
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['_token']) || $_POST['_token'] !== ($_SESSION['csrf_token'] ?? ''))) {
            $_SESSION['error'] = "Jeton de sécurité invalide.";
            header('Location: /commandes');
            exit();
        }
        
        // Définition des statuts possibles
        $statuts = [
            'traiter' => [
                'nouveau_statut' => 'En traitement',
                'message_succes' => 'La commande a été marquée comme étant en traitement.',
                'message_erreur' => 'Impossible de traiter cette commande.'
            ],
            'expedier' => [
                'nouveau_statut' => 'En expédition',
                'message_succes' => 'La commande a été marquée comme expédiée.',
                'message_erreur' => 'Impossible de marquer cette commande comme expédiée.'
            ],
            'livrer' => [
                'nouveau_statut' => 'Livrée',
                'message_succes' => 'La commande a été marquée comme livrée.',
                'message_erreur' => 'Impossible de marquer cette commande comme livrée.'
            ],
            'annuler' => [
                'nouveau_statut' => 'Annulée',
                'message_succes' => 'La commande a été annulée avec succès.',
                'message_erreur' => 'Impossible d\'annuler cette commande.'
            ],
            'payer' => [
                'nouveau_statut' => 'Payée',
                'message_succes' => 'La commande a été marquée comme payée.',
                'message_erreur' => 'Impossible de marquer cette commande comme payée.'
            ]
        ];
        
        // Vérifier si l'action est valide
        if (!isset($statuts[$action])) {
            $_SESSION['error'] = "Action non autorisée : $action";
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/commandes'));
            exit();
        }
        
        try {
            // Récupérer la commande actuelle pour vérifier les transitions d'état valides
            $commande = $this->commandeModel->getCommandeById($id_commande);
            if (!$commande) {
                throw new \Exception("Commande introuvable.");
            }
            
            // Vérifier les transitions d'état valides (optionnel, selon vos besoins)
            $statut_actuel = $commande['statut'] ?? '';
            
            // Mettre à jour le statut de la commande
            if ($this->commandeModel->updateCommande($id_commande, $statuts[$action]['nouveau_statut'])) {
                // Journaliser l'action
                error_log("Commande #$id_commande : statut changé de '$statut_actuel' à '{$statuts[$action]['nouveau_statut']}' par l'utilisateur #" . ($_SESSION['id_utilisateur'] ?? 'inconnu'));
                
                // Définir le message de succès
                $_SESSION['success'] = $statuts[$action]['message_succes'];
                
                // Rediriger vers la page précédente ou la liste des commandes
                $redirect_url = $_SERVER['HTTP_REFERER'] ?? '/commandes';
                
                // Si c'est une requête AJAX, retourner une réponse JSON
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => $statuts[$action]['message_succes'],
                        'new_status' => $statuts[$action]['nouveau_statut']
                    ]);
                    exit();
                }
                
                // Redirection standard
                header("Location: $redirect_url");
                exit();
            } else {
                throw new \Exception($statuts[$action]['message_erreur']);
            }
            
        } catch (\Exception $e) {
            // Journaliser l'erreur
            error_log("Erreur lors de la mise à jour de la commande #$id_commande : " . $e->getMessage());
            
            // Définir le message d'erreur
            $_SESSION['error'] = $e->getMessage();
            
            // Si c'est une requête AJAX, retourner une réponse JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // Journaliser l'erreur complète pour le débogage
                error_log('Erreur dans afficherCommande: ' . $e->getMessage());
                error_log('Stack trace: ' . $e->getTraceAsString());
                
                // Envoyer une réponse d'erreur détaillée
                header('HTTP/1.1 500 Internal Server Error');
                $errorResponse = [
                    'success' => false,
                    'error' => 'Erreur lors de la récupération de la commande avec ID ' . $id_commande,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString())
                ];
                
                echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit();
            }
            
            // Rediriger en cas d'erreur non-AJAX
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/commandes'));
            exit();
        }
    }
}
?>