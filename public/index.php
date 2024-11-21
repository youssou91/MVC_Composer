<?php
require_once __DIR__ . '/../config/db.php';
// require_once '../config/db.php';
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

// Connexion à la base de données
$pdo = getConnection(); // Assurez-vous que cette fonction retourne une instance PDO

// Création des instances
$commandeModel = new CommandeModel($pdo);
$commandeController = new CommandeControlleur($commandeModel);
// $commandeController->index();
$produitModel = new ProduitModel($pdo);
$categorieModel = new CategorieModel($pdo);
$produitControlleur = new ProduitControlleur($produitModel, $categorieModel);
$produitControlleur->afficherProduits();
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
$router->map('GET', '/produitCommandeControlleurs', 'ProduitControlleur::index', 'produits');

// Routes pour les commandes
$router->map('GET', '/commandes', 'CommandeControlleur::index', 'commandes');

// Routes pour le panier
$router->map('GET', '/panier', 'CartControlleur::index', 'panier');
$router->map('POST', '/panier/ajouter', 'CartControlleur::ajouter', 'ajouter_panier');  
$router->map('POST', '/panier/mettre-a-jour', 'CartControlleur::mettreAJour', 'mettre_a_jour_panier');  
$router->map('POST', '/panier/supprimer', 'CartControlleur::supprimer', 'supprimer_panier');  
$router->map('POST', '/panier/vider', 'CartControlleur::vider', 'vider_panier'); 


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
$router->map('GET', '/profile/orders/[i:id]', 'ProfilControlleur::orderDetail', 'order_detail');



// Routes pour les promotions
$router->map('GET', '/promotions', 'PromotionControlleur::index', 'promotions');

// Recherche de la route correspondante
$match = $router->match();

// Débogage pour vérifier ce que renvoie $match
if ($match) {
    var_dump($match); // Affiche les résultats de la correspondance
    require '../static/header.php'; // Chargement du header

    list($controlleur, $method) = explode('::', $match['target']);
    $controlleurClass = "../src/controlleur/{$controlleur}.php";

    // Vérification si le fichier du contrôleur existe
    if (file_exists($controlleurClass)) {
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
            } elseif ($controlleur === "App\\Controlleur\\") {
                // Instanciation spécifique pour CommandeControlleur
                $commandeModel = new CommandeModel($pdo);
                $controlleurInstance = new $controlleur($commandeModel);
            } elseif ($controlleur === "App\\Controlleur\\") {
                // Instanciation spécifique pour CartControlleur
                //pour le panier 
                $produitModel = new ProduitModel($pdo);
                $categorieModel = new CategorieModel($pdo);
                $controlleurInstance = new $controlleur($produitModel, $categorieModel);

            } else {
                // Instanciation générique
                $controlleurInstance = new $controlleur();
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

    require '../static/footer.php'; // Chargement du footer
} else {
    // Si aucune route ne correspond
    var_dump($match); 
    echo "Aucune route correspondante trouvée."; // Débogage pour voir si le match échoue
    http_response_code(404);
    require '../src/vue/errors/404.php';
}


?>
