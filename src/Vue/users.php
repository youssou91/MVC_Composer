<?php
use app\Controlleur\UserControlleur;
use app\Model\UserModel;
use AltoRouter\Router;
$dbConnection = getConnection();
// Créez une instance du modèle
$userModel = new UserModel($dbConnection); 

// Passez l'instance du modèle au contrôleur
$userController = new UserControlleur($userModel);

// Appelez la méthode du contrôleur
$userController->getUsers();

?>