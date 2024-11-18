<?php
session_start();

// Inclure le fichier de configuration pour obtenir la connexion PDO
include '../../config.php'; // Chemin vers votre fichier de configuration
include '../../Model/UserModel.php'; // Chemin vers le modèle utilisateur

// Obtenir la connexion à la base de données
$db = getConnection();

// Instancier UserModel avec la connexion à la base de données
$userModel = new UserModel($db);

$errorMessage = ''; // Initialisation de la variable pour le message d'erreur

if (isset($_POST['btn-connexion'])) {
    $email = $_POST['couriel'];
    $password = $_POST['mot_de_pass'];

    // Vérifier les informations de connexion de l'utilisateur
    $user = $userModel->checkUser($email, $password);

    if ($user) {
        if ($user['statut'] === 'actif') {
            $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
            $_SESSION['nom_utilisateur'] = $user['nom_utilisateur'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['loggedin'] = true;

            header("Location: ../../index.php");
            exit();
        } else {
            // Message si le compte est désactivé
            $errorMessage = 'Votre compte est désactivé. Veuillez contacter l\'administrateur.';
        }
    } else {
        // Message si les informations de connexion sont incorrectes
        $errorMessage = 'Email ou mot de passe incorrect';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Page de Connexion</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 h-screen flex items-center justify-center">
        <div class="container mx-auto max-w-md p-8 bg-white rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold text-center text-blue-700 mb-6">Page de Connexion</h1>

            <!-- Affichage du message d'erreur s'il y en a un -->
            <?php if ($errorMessage): ?>
                <div class="mb-4 p-4 text-red-700 bg-red-100 rounded">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Adresse email</label>
                    <input type="email" name="couriel" id="email" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe</label>
                    <input type="password" name="mot_de_pass" id="password" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4 text-right">
                    <a href="addUserView.php" class="text-blue-600 hover:underline">S'inscrire</a>
                </div>
                <button type="submit" name="btn-connexion" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Se connecter
                </button>
            </form>
        </div>
    </body>
</html>
