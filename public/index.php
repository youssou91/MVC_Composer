<?php
echo "Fichier db.php inclus !<br>";
require_once '../config/db.php';
require '../vendor/autoload.php';

use AltoRouter\Router; 
use App\Controlleur\HomeControlleur;
use App\Controlleur\ContactControlleur;
use App\Controlleur\ProduitControlleur;
use App\Controlleur\CartControlleur;
use App\Controlleur\AuthControlleur;
use App\Controlleur\AdminControlleur;
use App\Controlleur\AdminProduitControlleur;
use App\Controlleur\UserController;
use App\Controlleur\ProfilControlleur;
use App\Controlleur\PromotionControlleur; 

// Initialisation du routeur
$router = new AltoRouter();

// require_once '../config/db.php'; // Assurez-vous que le chemin est correct

try {
    // Appel à la fonction getConnection pour établir la connexion
    $pdo = getConnection();
    echo "Connexion réussie!";
} catch (\PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
}

// Routes principales
$router->map('GET', '/', 'HomeControlleur::index', 'accueil'); // Accueil
$router->map('GET', '/contact', 'ContactControlleur::index', 'contacter'); // Contacter

// Routes pour les produits
$router->map('GET', '/produits', 'ProduitControlleur::index', 'produits');
$router->map('GET', '/produits/[i:id]', 'ProduitControlleur::show', 'produit_detail');

// Routes pour le panier
$router->map('GET', '/cart', 'CartControlleur::index', 'panier');
$router->map('POST', '/cart/add', 'CartControlleur::add', 'ajouter_au_panier');
$router->map('POST', '/cart/remove/[i:id]', 'CartControlleur::remove', 'supprimer_du_panier');

// Routes d'authentification
$router->map('GET', '/login', 'AuthControlleur::loginForm', 'connexion');
$router->map('POST', '/login', 'AuthControlleur::login', 'traitement_connexion');
$router->map('GET', '/register', 'AuthControlleur::registerForm', 'inscription');
$router->map('POST', '/register', 'AuthControlleur::register', 'traitement_inscription');
$router->map('GET', '/logout', 'AuthControlleur::logout', 'deconnexion');

// Routes pour l'administration
$router->map('GET', '/admin', 'AdminControlleur::dashboard', 'admin_dashboard');
$router->map('GET', '/admin/produits', 'AdminProduitControlleur::index', 'admin_gestion_produits');
$router->map('POST', '/admin/produits/add', 'AdminProduitControlleur::add', 'admin_ajouter_produit');
$router->map('POST', '/admin/produits/delete/[i:id]', 'AdminProduitControlleur::delete', 'admin_supprimer_produit');

// Nouvelle route pour les promotions
$router->map('GET', '/promotions', 'PromotionControlleur::index', 'promotions');

// Recherche de la route correspondante
// Route matching
$match = $router->match();
// Si une route correspond, on l'exécute
if ($match) {
    // Chargement du header
    require '../static/header.php';
    // Extraction du contrôleur et de la méthode
    list($controlleur, $method) = explode('::', $match['target']);
    // Vérification de l'existence du fichier du contrôleur
    $controlleurClass = "../src/controlleur/{$controlleur}.php"; 
    // var_dump("Vérification de l'existence du fichier du contrôleur : ".$controlleurClass);
    if (file_exists($controlleurClass)) {
        require_once $controlleurClass;
        // Vérification de la classe et de la méthode
        // if (class_exists($controlleur) && method_exists($controlleur, $method)) {
        //     $controlleurInstance = new $controlleur();
        //     call_user_func_array([$controlleurInstance, $method], $match['params']);
        // } else {
        //     // Si la classe ou la méthode n'existe pas
        //     http_response_code(404);
        //     var_dump("Vérification de l'existence du fichier 1".$controlleurClass);
        //     require '../src/vue/errors/404.php';
        // }
        // if (class_exists($controlleur) && method_exists($controlleur, $method)) {
        //     var_dump("Classe trouvée : $controlleur, Méthode trouvée : $method");
        //     $controlleurInstance = new $controlleur();
        //     call_user_func_array([$controlleurInstance, $method], $match['params']);
        // } else {
        //     http_response_code(404);
        //     var_dump("Classe ou méthode non trouvée. Classe : $controlleur, Méthode : $method");
        //     require '../src/vue/errors/404.php';
        // }
        $controlleur = "App\\Controlleur\\" . $controlleur;
        if (class_exists($controlleur) && method_exists($controlleur, $method)) {
            var_dump("Classe trouvée : $controlleur, Méthode trouvée : $method");
            $controlleurInstance = new $controlleur();
            call_user_func_array([$controlleurInstance, $method], $match['params']);
        } else {
            http_response_code(404);
            var_dump("Classe ou méthode non trouvée. Classe : $controlleur, Méthode : $method");
            if (class_exists($controlleur)) {
                var_dump("Méthodes disponibles : ", get_class_methods($controlleur));
            }
            require '../src/vue/errors/404.php';
        }        
    } else {
        http_response_code(404);
        var_dump("Vérification de l'existence du fichier 2".$controlleurClass);
        require '../src/vue/errors/404.php';
    }
    
    // Chargement du footer
    require '../static/footer.php';
} else {
    // Si aucune route ne correspond
    http_response_code(404);
    var_dump("Vérification de l'existence du fichier 3".$controlleurClass);
    require '../src/vue/errors/404.php';
}

?>
