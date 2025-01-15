<?php
// Inclure la configuration et la connexion à la base de données
use app\Controlleur\UserControlleur;
// Créez une instance de connexion à la base de données
$dbConnection = getConnection(); // Appel de la fonction getConnection() pour obtenir la connexion PDO
// Créez une instance du contrôleur avec la connexion à la base de données
// Variable pour stocker les messages
$message = '';
$messageType = '';
// Si le formulaire est soumis
// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = [
        'nom_utilisateur' => $_POST['nom_utilisateur'],
        'prenom' => $_POST['prenom'],
        'datNaiss' => $_POST['datNaiss'],
        'couriel' => $_POST['couriel'],
        'password' => $_POST['password'],
        'cpassword' => $_POST['cpassword'],
        'telephone' => $_POST['telephone'],
        'rue' => $_POST['rue'],
        'ville' => $_POST['ville'],
        'code_postal' => $_POST['code_postal'],
        'pays' => $_POST['pays'],
        'numero' => $_POST['numero'],
        'province' => $_POST['province'],
    ];

    // Appeler la méthode d'enregistrement et récupérer la réponse
    $result = $userController->registerUser($user);

    // Définir le message et le type en fonction du résultat
    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
    } else {
        $message = $result['message'];
        $messageType = 'error';
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inscription</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 min-h-screen flex items-center justify-center">

        <div class="container mx-auto max-w-3xl bg-white p-8 shadow-lg rounded-lg my-8">
            <!-- Afficher le message de succès ou d'erreur -->
            <?php if (!empty($message)): ?>
                <div class="p-4 mb-4 text-sm font-semibold 
                            <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?> 
                            border 
                            <?php echo $messageType === 'success' ? 'border-green-200' : 'border-red-200'; ?> 
                            rounded-lg">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form method="POST"  action="/inscription" class="space-y-8">
                <!-- Formulaire de l'utilisateur comme dans votre code initial -->
                <!-- Informations Personnelles -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4 text-blue-500">Informations Personnelles</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                            <input type="text" id="nom" name="nom_utilisateur" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div class="col-span-full">
                            <label for="datNaiss" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                            <input type="date" id="datNaiss" name="datNaiss" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                    </div>
                </div>
                <!-- Coordonnées -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4 text-blue-500">Coordonnées</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                            <input type="number" id="telephone" name="telephone" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="couriel" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="couriel" name="couriel" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                            <input type="password" id="password" name="password" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="cpassword" class="block text-sm font-medium text-gray-700">Confirmer Mot de passe</label>
                            <input type="password" id="cpassword" name="cpassword" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                    </div>
                </div>
                <!-- Adresse -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4 text-blue-500">Adresse</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="rue" class="block text-sm font-medium text-gray-700">Rue</label>
                            <input type="text" id="rue" name="rue" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="numero" class="block text-sm font-medium text-gray-700">Numéro</label>
                            <input type="text" id="numero" name="numero" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="ville" class="block text-sm font-medium text-gray-700">Ville</label>
                            <input type="text" id="ville" name="ville" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="code_postal" class="block text-sm font-medium text-gray-700">Code Postal</label>
                            <input type="text" id="code_postal" name="code_postal" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="province" class="block text-sm font-medium text-gray-700">Province</label>
                            <input type="text" id="province" name="province" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label for="pays" class="block text-sm font-medium text-gray-700">Pays</label>
                            <input type="text" id="pays" name="pays" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" value="Canada" required>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <button type="submit" name="addUser" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md">Soumettre</button>
                </div>
            </form>
        </div>
    </body>
</html>
