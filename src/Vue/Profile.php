<?php
require __DIR__ . '/../../vendor/autoload.php';

use App\Controlleur\ProfileControlleur;

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    echo '<script>window.location.href = "connexion.php";</script>';
    exit;
}

// Initialisez les dépendances
$dbConnection = getConnection();  
$userController = new ProfileControlleur($dbConnection);

// Récupérez l'ID de l'utilisateur
$userId = $_SESSION['id_utilisateur'];

// Récupérez les informations utilisateur
$userInfo = $userController->getUserInfo($userId);

// Récupérez les commandes de l'utilisateur
$userOrders = $userController->getUserOrders($userId);

// Stockez les commandes dans la session pour les rendre accessibles
$_SESSION['orders'] = $userOrders;

// Traitement du formulaire de mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateProfile'])) {
    $nom = $_POST['nom_utilisateur'];
    $prenom = $_POST['prenom_utilisateur'];
    $email = $_POST['email_utilisateur'];
    $telephone = $_POST['telephone_utilisateur'];
    $adresse = $_POST['adresse_utilisateur'];
    $ville = $_POST['ville_utilisateur'];
    $codePostal = $_POST['code_postal_utilisateur'];
    $province = $_POST['province_utilisateur'];
    $pays = $_POST['pays_utilisateur'];
    $userController->updateUserInfo($userId, $nom, $prenom, $email, $telephone, $adresse, $ville, $codePostal, $province, $pays);
    header('Location: profile.php');  
    exit;
}

// Traitement du changement de mot de passe
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updatePassword'])) {
    $ancienMotDePasse = $_POST['ancien_mot_de_passe'];
    $nouveauMotDePasse = $_POST['nouveau_mot_de_passe'];
    $confirmationMotDePasse = $_POST['confirmation_mot_de_passe'];

    if ($nouveauMotDePasse === $confirmationMotDePasse) {
        if ($userController->updatePassword($userId, $ancienMotDePasse, $nouveauMotDePasse)) {
            echo "Mot de passe mis à jour avec succès!";
        } else {
            echo "L'ancien mot de passe est incorrect.";
        }
    } else {
        echo "Les nouveaux mots de passe ne correspondent pas.";
    }
}

// Gestion des actions sur les commandes
if (isset($_POST['action'])) {
    $orderId = $_POST['order_id'];
    $action = $_POST['action'];
    switch ($action) {
        case 'traiter':
            $userController->updateOrderStatus($orderId, 'En traitement');
            break;
        case 'expédier':
            $userController->updateOrderStatus($orderId, 'En expédition');
            break;
        case 'annuler':
            $userController->updateOrderStatus($orderId, 'Annulée');
            break;
    }
    echo '<script>window.location.href = "profile.php";</script>';
    exit;
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-8">
    <h1 class="text-3xl text-center text-blue-600 font-semibold mb-5">Mon Profil</h1>
    <div class="flex flex-col lg:flex-row gap-5">
        <div class="bg-white p-6 rounded-lg shadow-md lg:w-1/3">
            <h3 class="text-xl text-center text-blue-600 font-semibold mb-4">Informations personnelles</h3>
            <p><span class="font-semibold">Nom:</span> <?= htmlspecialchars($userInfo['nom_utilisateur']) ?></p>
            <p><span class="font-semibold">Prénom:</span> <?= htmlspecialchars($userInfo['prenom']) ?></p>
            <p><span class="font-semibold">Email:</span> <?= htmlspecialchars($userInfo['couriel']) ?></p>
            <p><span class="font-semibold">Téléphone:</span> <?= htmlspecialchars($userInfo['telephone']) ?></p>
            <h4 class="text-lg font-semibold text-blue-600 mt-4">Adresse</h4>
            <p><span class="font-semibold">Rue:</span> <?= htmlspecialchars($userInfo['numero']).' '.htmlspecialchars($userInfo['rue']) ?></p>
            <p><span class="font-semibold">Code Postal:</span> <?= htmlspecialchars($userInfo['code_postal']) ?></p>
            <p><span class="font-semibold">Ville:</span> <?= htmlspecialchars($userInfo['ville']).', '.htmlspecialchars($userInfo['province']) ?></p>
            <p><span class="font-semibold">Pays:</span> <?= htmlspecialchars($userInfo['pays']) ?></p>
            <div class="mt-6 flex gap-4">
                <button class="bg-yellow-500 text-white py-2 px-4 rounded-lg hover:bg-yellow-600" data-modal-target="#modalModifierProfil">
                    <i class="fas fa-user-edit"></i>
                </button>
                <button class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600" data-modal-target="#modalModifierMotDePasse">
                    <i class="fas fa-key"></i>
                </button>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md lg:w-2/3">
            <h3 class="text-xl text-center text-blue-600 font-semibold mb-4">Mes Commandes</h3>
            <?php if (is_array($userOrders) && count($userOrders) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-center border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 border-b">#</th>
                                <th class="py-2 px-4 border-b">Date</th>
                                <th class="py-2 px-4 border-b">Montant</th>
                                <th class="py-2 px-4 border-b">Statut</th>
                                <th class="py-2 px-4 border-b">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $index = 1; ?>
                            <?php foreach ($userOrders as $order): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4"><?= $index++; ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($order['date_commande']) ?></td>
                                    <td class="px-4 py-2"><?= number_format(htmlspecialchars($order['prix_total']), 2); ?> $</td>
                                    <td class="py-2 px-4">
                                        <span class="px-2 py-1 rounded-full 
                                            <?php 
                                                // Application des couleurs en fonction du statut
                                                if ($order['statut'] == 'En attente') {
                                                    echo 'bg-yellow-200 text-yellow-800'; 
                                                } elseif ($order['statut'] == 'En traitement') {
                                                    echo 'bg-orange-200 text-orange-800'; 
                                                } elseif ($order['statut'] == 'En expédition') {
                                                    echo 'bg-green-200 text-green-800'; 
                                                } elseif ($order['statut'] == 'Livrée') {
                                                    echo 'bg-blue-200 text-blue-800'; 
                                                } elseif ($order['statut'] == 'Annulée') {
                                                    echo 'bg-red-200 text-red-800'; 
                                                } elseif ($order['statut'] == 'Payée') {
                                                    echo 'bg-purple-200 text-purple-800'; 
                                                }
                                            ?>">
                                            <?= htmlspecialchars($order['statut']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex space-x-2">
                                            <!-- Détails -->
                                            <a href="/profile/details/<?= $order['id_commande'] ?>" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                                                <i class="fas fa-info-circle"></i>
                                            </a>

                                            <!-- Paiement -->                                    
                                            <?php if ($order['statut'] != 'Livrée' && $order['statut'] != 'Annulée' && $order['statut'] != 'En expédition'): ?>
                                                <form method="post" action="/profile/paiement/<?= $order['id_commande'] ?>">
                                                    <input type="hidden" name="id_commande" value="<?= htmlspecialchars($order['id_commande']) ?>">
                                                    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">
                                                        <i class="fas fa-credit-card"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <a href="#" class="bg-gray-500 text-white py-2 px-4 rounded cursor-not-allowed" disabled>
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            <?php endif; ?>
                                            <!-- Annulation -->
                                            <?php if ($order['statut'] == 'En attente' || $order['statut'] == 'En traitement'): ?>
                                                <a href="/profile/annuler/<?= $order['id_commande'] ?>" class="bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="#" class="bg-gray-500 text-white py-2 px-4 rounded cursor-not-allowed" disabled>
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td> 
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Aucune commande trouvée.</p>
            <?php endif; ?>
        </div>

    </div>
</div>
<script>
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = button.getAttribute('data-modal-target');
            document.querySelector(modalId).classList.toggle('hidden');
        });
    });
</script>
</body>
</html> 