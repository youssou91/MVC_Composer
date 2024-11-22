<?php
require_once __DIR__ . '/../config/db.php';
require '../vendor/autoload.php';

use AltoRouter\Router; 
use App\Controlleur\HomeControlleur;
use App\Controlleur\ContactControlleur;
use App\Controlleur\CommandeControlleur;
use App\Controlleur\ProduitControlleur;
use App\Controlleur\CartControlleur;
use App\Controlleur\AuthControlleur;
use App\Controlleur\AdminControlleur;
use App\Controlleur\AdminProduitControlleur;
use App\Controlleur\ProfilControlleur;
use App\Controlleur\PromotionControlleur;
use App\Modele\ProduitModel;
use App\Modele\CategorieModel;
use App\Modele\CommandeModel;
use Controleur\CartController;

$pdo = getConnection();
// Création des instances
$commandeModel = new CommandeModel($pdo);
$commandeController = new CommandeControlleur($commandeModel);
$produitModel = new ProduitModel($pdo);
$categorieModel = new CategorieModel($pdo);
$produitControlleur = new ProduitControlleur($produitModel, $categorieModel);
$produitControlleur->afficherProduits();

$cartModel = new \App\Modele\CartModel($pdo);
$cartController = new \App\Controlleur\CartControlleur($cartModel);

$router = new AltoRouter();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'ajouter_au_panier') {
    $cartController = new CartController();
    $cartController->ajouter();
    exit;
}

// Routes principales
$router->map('GET', '/', 'HomeControlleur::index', 'accueil');
$router->map('GET', '/contact', 'ContactControlleur::index', 'contacter');

// Routes pour les produits
$router->map('GET', '/produits/[i:id]', 'ProduitControlleur::show', 'produit_detail');
$router->map('GET', '/produits', 'ProduitControlleur::index', 'produits');
$router->map('GET', '/produits/ajout', 'ProduitControlleur::ajouterProduit', 'ajout');
$urlAjout = $router->generate('ajout');
// Routes pour les commandes
$router->map('GET', '/commandes', 'CommandeControlleur::index', 'commandes');

// Routes pour le panier
$router->map('POST', '/cart/ajouter', 'CartControlleur::ajouter');
$router->map('GET', '/cart', 'CartControlleur::afficher');
$router->map('POST', '/cart/vider', 'CartControlleur::vider');

// Routes d'authentification
$router->map('GET|POST', '/login', 'AuthControlleur::loginForm', 'connexion');
$router->map('POST', '/login', 'AuthControlleur::login', 'traitement_connexion');
$router->map('GET', '/register', 'AuthControlleur::registerForm', 'inscription');
$router->map('POST', '/register', 'AuthControlleur::register', 'traitement_inscription');
$router->map('GET', '/logout', 'AuthControlleur::logout', 'deconnexion');

// Routes pour l'administration
$router->map('GET', '/admin', 'AdminControlleur::dashboard', 'admin_dashboard');
$router->map('POST', '/admin/produits/add', 'AdminProduitControlleur::add', 'admin_ajouter_produit');
$router->map('POST', '/admin/produits/delete/[i:id]', 'AdminProduitControlleur::delete', 'admin_supprimer_produit');

// Routes pour le profil utilisateur
$router->map('GET', '/mon_profile', 'ProfileControlleur::index', 'profile');
$router->map('GET', '/profile/edit', 'ProfilControlleur::editProfile', 'edit_profile');
$router->map('POST', '/profile/edit', 'ProfilControlleur::updateProfile', 'update_profile');
$router->map('GET', '/profile/orders', 'ProfilControlleur::orders', 'orders');
//paiement commandes

$router->map('GET', '/profile/orders/payer/[i:id]', 'ProfilControlleur::payerOrder', 'payer_order');
$router->map('GET', '/profile/orders/annuler/[i:id]', 'ProfilControlleur::annulerOrder', 'annuler_order');
$router->map('GET', '/profile/orders/valider/[i:id]', 'ProfilControlleur::validerOrder', 'valider_order');
$router->map('GET', '/profile/orders/refuser/[i:id]', 'ProfilControlleur::refuserOrder', 'refuser_order');
$router->map('GET', '/profile/orders/en_cours/[i:id]', 'ProfilControlleur::enCoursOrder', 'en_cours_order');
$router->map('GET', '/profile/orders/[i:id]', 'ProfilControlleur::orderDetail', 'order_detail');



// Routes pour les promotions
$router->map('GET', '/promotions', 'PromotionControlleur::index', 'promotions');
$router->map('GET', '/promotion/[i:id]', 'PromotionControlleur::show', 'promotion_detail');
$router->map('POST', '/promotion/add', 'PromotionControlleur::add', 'admin_ajouter_promotion');
$router->map('GET', '/promotion/add', 'PromotionControlleur::addForm', 'admin_form_ajouter_promotion');
$router->map('POST', '/promotion/delete/[i:id]', 'PromotionControlleur::delete', 'admin_supprimer_promotion');
$router->map('POST', '/promotion/edit/[i:id]', 'PromotionControlleur::edit', 'admin_editer_promotion');

// Recherche de la route correspondante
$match = $router->match();

// Débogage pour vérifier ce que renvoie $match
if($match) {
    // var_dump($match); 
    require '../static/header.php'; 

    list($controlleur, $method) = explode('::', $match['target']);
    $controlleurClass = "../src/controlleur/{$controlleur}.php";

    // Vérification si le fichier du contrôleur existe
    if(file_exists($controlleurClass)) {
        require_once $controlleurClass;
        $controlleur = "App\\Controlleur\\" . $controlleur;
        // Vérification de la classe et de la méthode
        if (class_exists($controlleur) && method_exists($controlleur, $method)) {
            
            // Injection des dépendances manuellement pour des contrôleurs spécifiques
            if ($controlleur === "App\\Controlleur\\ProduitControlleur") {
                // Instanciation spécifique pour ProduitControlleur
                $produitModel = new ProduitModel($pdo);
                $categorieModel = new CategorieModel($pdo);
                $controlleurInstance = new $controlleur($produitModel, $categorieModel);
            } elseif ($controlleur === "App\\Controlleur\\CommandeControlleur") {
                // Instanciation spécifique pour CommandeControlleur
                $commandeModel = new CommandeModel($pdo);
                $controlleurInstance = new $controlleur($commandeModel);
            } elseif     ($controlleur === "App\\Controlleur\\") {
                // Instanciation spécifique pour CartControlleur
                $produitModel = new ProduitModel($pdo);
                $categorieModel = new CategorieModel($pdo);
                $controlleurInstance = new $controlleur($produitModel, $categorieModel);

            } elseif ($controlleur === "App\\Controlleur\\CartControlleur") {
                $cartController = new $controlleur($cartModel);
                call_user_func_array([$cartController, $method], $match['params']);
            } else {
                // Gestion générique pour les autres contrôleurs
                $controlleurInstance = new $controlleur();
                call_user_func_array([$controlleurInstance, $method], $match['params']);
            }
            // Appel de la méthode du contrôleur
            call_user_func_array([$controlleurInstance, $method], $match['params']);
        } else {
            // Débogage pour voir quel contrôleur ou méthode est manquant
            var_dump($match); 
            echo "Classe ou méthode non trouvée : " . $controlleur . "::" . $method;
            http_response_code(404);
            require '../src/vue/errors/404.php';
        }
    } else {
        // Débogage pour afficher le chemin du fichier qui n'existe pas
        var_dump($match); 
        echo "Fichier du contrôleur introuvable : " . $controlleurClass;
        http_response_code(404);
        require '../src/vue/errors/404.php';
    }
    require '../static/footer.php'; 
} else {
    // Si aucune route ne correspond
    var_dump($match); 
    echo "Aucune route correspondante trouvée."; 
    http_response_code(404);
    require '../src/vue/errors/404.php';
}


?>
