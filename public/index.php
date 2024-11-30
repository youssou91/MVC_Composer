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
use App\Controlleur\ProfileControlleur;
use App\Controlleur\PromotionControlleur;
use App\Modele\ProduitModel;
use App\Modele\CategorieModel;
use App\Modele\CommandeModel;
use App\Modele\CartModel;
use App\Modele\PanierModele;


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
$router->map('POST', '/produits/ajouterProduit', 'ProduitControlleur::ajouterProduit', 'ajouterProduit');
//
$router->map('GET', '/produits/modifierProduit=[i:id]', 'ProduitControlleur::recupererProduit', 'modifier');
$router->map('GET', '/produits/editerProduit=[i:id]', 'ProduitControlleur::updateProduit', 'editer');
$router->map('GET', '/produits/supprimer=[i:id]', 'ProduitControlleur::supprimerProduit', 'supprimer');


// Définir le routage pour l'action d'ajout de produit au panier
$router->map('POST', '/produits/panier', 'HomeControlleur::ajouterProduit', 'ajouterProduitPanier');
$router->map('POST', '/produits/supprimer/[i:id]', function($id) {
    (new HomeControlleur())->gererPanier($id);
}, 'supprimerProduitPanier');
$router->map('POST', '/produits/supprimer', function() {
    (new HomeControlleur())->gererPanier();
}, 'viderPanier');

// Routes pour les commandes
$router->map('GET', '/commandes', 'CommandeControlleur::index', 'commandes');
$router->map('POST', '/commande', 'CommandeControlleur::ajouterCommande', 'commande');


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
$router->map('GET', '/profile/edit', 'ProfileControlleur::editProfile', 'edit_profile');
$router->map('POST', '/profile/edit', 'ProfileControlleur::updateProfile', 'update_profile');
///
$router->map('GET', '/profile/paiement/[i:id_commande]', 'ProfileControlleur::payOrder', 'paiement');
$router->map('GET', '/profile/details/[i:id_commande]', 'ProfileControlleur::getOrderDetails', 'details');
$router->map('GET', '/profile/annuler/[i:id_commande]', 'ProfileControlleur::changeOrderStatus', 'annuler');


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

// Vérification des routes
$match = $router->match();
// Vérifier si une route correspond
if ($match) {
    require '../static/header.php';

    // Vérifier si la cible est une fonction anonyme ou une méthode de classe
    if (is_callable($match['target'])) {
        call_user_func_array($match['target'], $match['params']);
    } else {
        // Extraire le contrôleur et la méthode
        list($controlleur, $method) = explode('::', $match['target']);
        $controlleurClass = "../src/controlleur/{$controlleur}.php";

        // Vérifier si le fichier du contrôleur existe
        if (file_exists($controlleurClass)) {
            require_once $controlleurClass;
            $controlleur = "App\\Controlleur\\" . $controlleur;

            // Vérifier si la classe du contrôleur existe
            if (class_exists($controlleur)) {
                // Instancier la classe du contrôleur en fonction de ses dépendances
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

                if (method_exists($controlleurInstance, $method)) {
                    // Récupérer les paramètres de l'URL
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $match['params'] = array_merge($match['params'], [$_POST]);
                    }
                    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                        // Vérifier si un paramètre est passé via l'URL sous forme "nom=valeur"
                        $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
                        if ($queryString) { // Vérifier si une chaîne de requête existe
                            parse_str($queryString, $queryParams);
                            $match['params'] = array_merge($match['params'], $queryParams);
                        }
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
    }

    // Inclure le pied de page
    require '../static/footer.php';
} else {
    // Gestion des erreurs si aucune route ne correspond
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
