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
use App\Modele\CartModel;

$pdo = getConnection();
// Création des instances des modèles
$commandeModel = new CommandeModel($pdo);
$commandeController = new CommandeControlleur($commandeModel);
$produitModel = new ProduitModel($pdo);
$categorieModel = new CategorieModel($pdo);
$produitControlleur = new ProduitControlleur($produitModel, $categorieModel);
$cartModel = new CartModel($pdo);
$cartController = new CartControlleur($cartModel);

// Routeur AltoRouter
$router = new AltoRouter();

// Routes principales
$router->map('GET', '/', 'HomeControlleur::index', 'accueil');
$router->map('GET', '/contact', 'ContactControlleur::index', 'contacter');

// Routes pour les produits
$router->map('GET', '/produits/[i:id]', 'ProduitControlleur::show', 'produit_detail');
$router->map('GET', '/produits', 'ProduitControlleur::index', 'produits');
$router->map('GET', '/produits/ajout', 'ProduitControlleur::afficheForm', 'ajout');
$router->map('POST', '/produit/ajouterProduit', 'ProduitControlleur::ajouterProduit', 'ajouterProduit');
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
$router->map('GET', '/mon_profile', 'ProfilControlleur::index', 'profile');
$router->map('GET', '/profile/edit', 'ProfilControlleur::editProfile', 'edit_profile');
$router->map('POST', '/profile/edit', 'ProfilControlleur::updateProfile', 'update_profile');
$router->map('GET', '/profile/orders', 'ProfilControlleur::orders', 'orders');

// Routes pour les promotions
$router->map('GET', '/promotions', 'PromotionControlleur::index', 'promotions');
$router->map('GET', '/promotion/[i:id]', 'PromotionControlleur::show', 'promotion_detail');
$router->map('POST', '/promotion/add', 'PromotionControlleur::add', 'admin_ajouter_promotion');
$router->map('GET', '/promotion/add', 'PromotionControlleur::addForm', 'admin_form_ajouter_promotion');
$router->map('POST', '/promotion/delete/[i:id]', 'PromotionControlleur::delete', 'admin_supprimer_promotion');
$router->map('POST', '/promotion/edit/[i:id]', 'PromotionControlleur::edit', 'admin_editer_promotion');

// Vérification des routes


// Active l'affichage des erreurs en mode développement
error_reporting(E_ALL);
ini_set('display_errors', 1);

$match = $router->match();

if ($match) {
    require '../static/header.php';

    list($controlleur, $method) = explode('::', $match['target']);
    $controlleurClass = "../src/controlleur/{$controlleur}.php";

    // Vérifier si le fichier du contrôleur existe
    if (file_exists($controlleurClass)) {
        require_once $controlleurClass;
        $controlleur = "App\\Controlleur\\" . $controlleur;

        // Vérifier si la classe du contrôleur existe
        if (class_exists($controlleur)) {
            switch ($controlleur) {
                case "App\\Controlleur\\ProduitControlleur":
                    $produitModel = new ProduitModel($pdo);
                    $categorieModel = new CategorieModel($pdo);
                    $controlleurInstance = new $controlleur($produitModel, $categorieModel);
                    break;
                case "App\\Controlleur\\CommandeControlleur":
                    $commandeModel = new CommandeModel($pdo);
                    $controlleurInstance = new $controlleur($commandeModel);
                    break;
                case "App\\Controlleur\\CartControlleur":
                    $cartModel = new CartModel($pdo);
                    $controlleurInstance = new $controlleur($cartModel);
                    break;
                default:
                    $controlleurInstance = new $controlleur();
                    break;
            }

            // Vérifier si la méthode existe dans le contrôleur
            if (method_exists($controlleurInstance, $method)) {
                // Ajouter les données POST/GET aux paramètres
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $match['params'] = array_merge($match['params'], [$_POST]);
                }
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $match['params'] = array_merge($match['params'], [$_GET]);
                }

                // Validation des paramètres requis
                $reflection = new ReflectionMethod($controlleurInstance, $method);
                $parameters = $reflection->getParameters();

                // Vérifier si le nombre de paramètres est suffisant
                if (count($parameters) > count($match['params'])) {
                    handleError("Nombre de paramètres insuffisants pour appeler la méthode : $method", 400);
                }

                // Appeler la méthode du contrôleur avec les paramètres
                call_user_func_array([$controlleurInstance, $method], $match['params']);
            } else {
                handleError("Méthode non trouvée : $method dans le contrôleur $controlleur");
            }
        } else {
            handleError("Classe non trouvée : $controlleur");
        }
    } else {
        handleError("Fichier du contrôleur introuvable : $controlleurClass");
    }

    require '../static/footer.php';
} else {
    handleError("Aucune route correspondante trouvée.");
}


// Fonction pour gérer les erreurs et afficher un message générique
function handleError($errstr, $errno = 500, $errfile = '', $errline = 0) {
    // Générer un identifiant unique pour l'erreur
    $errorId = uniqid('error_', true);

    // Enregistrer l'erreur dans le log avec tous les détails
    error_log("Erreur [$errno] : $errstr dans $errfile à la ligne $errline | ID : $errorId");

    // Afficher le message générique à l'utilisateur avec l'ID de l'erreur
    echo "Une erreur interne est survenue. Veuillez réessayer plus tard. Si l'erreur persiste, veuillez citer le code d'erreur : $errorId.";
    
    exit;
}

// Définir cette fonction pour intercepter toutes les erreurs
set_error_handler("handleError");


?>


?>
