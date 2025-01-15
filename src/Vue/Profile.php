<?php
require __DIR__ . '/../../vendor/autoload.php';
use App\Controlleur\ProfileControlleur;
// Vérifiez si l'utilisateur est connecté

// Initialisez les dépendances
$dbConnection = getConnection();  
$userController = new ProfileControlleur($dbConnection);
// Récupérez l'ID de l'utilisateur
$userId = $_SESSION['id_utilisateur'];
// Récupérez les informations utilisateur
$userInfo = $userController->getUserInfo($userId);
// Récupérez les commandes de l'utilisateur
$userOrders = $userController->getUserOrders($userId);
$utilisateurEstConnecte = isset($_SESSION['id_utilisateur']) && !empty($_SESSION['id_utilisateur']);

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
    <?php if ($utilisateurEstConnecte): 
            $utilisateurId = $_SESSION['id_utilisateur']; ?>
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
                                                            <input type="hidden" name="prix_total" value="<?= htmlspecialchars($order['prix_total']); ?>">
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
                                                        <button onclick="openModal('<?= $order['id_commande']; ?>')" 
                                                            class="bg-red-500 text-white px-4 py-2 rounded"> 
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button onclick="openModal('<?= $order['id_commande']; ?>')" class="bg-gray-500 text-white py-2 px-4 rounded cursor-not-allowed" disabled>
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td> 
                                        </tr>
                                        <!-- Modal unique pour cette commande -->
                                        <div id="modalAnnulerCommande<?= $order['id_commande']; ?>" 
                                            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden">
                                            <div class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                                                <div class="flex justify-between items-center mb-4">
                                                    <h5 class="text-lg font-semibold">Confirmer l'annulation</h5>
                                                    <button onclick="closeModal('<?= $order['id_commande']; ?>')" class="text-gray-500 hover:text-gray-800">
                                                        &times;
                                                    </button>
                                                </div>
                                                <div class="mb-4">
                                                    Êtes-vous sûr de vouloir annuler la commande : <strong><?= $order['id_commande']; ?></strong> ? 
                                                    Cette action est irréversible.
                                                </div>
                                                <div class="flex justify-end gap-4">
                                                    <button onclick="closeModal('<?= $order['id_commande']; ?>')" 
                                                        class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Annuler</button>
                                                    <a href="/commande/editer/id_commande=<?= $order['id_commande']; ?>/action=annuler" 
                                                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                                        Confirmer l'annulation
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
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
        <?php else: 
        header('Location: /login'); 
    endif; ?>    
 <!-- Modal Modification Profil -->
<!-- Modal de modification du profil -->
<div id="modalModifierProfil" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg p-6 relative">
        <button class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" onclick="document.querySelector('#modalModifierProfil').classList.add('hidden');">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="text-2xl font-semibold text-center text-blue-600 mb-6">Modifier les informations du profil</h3>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Colonne 1 -->
            <div class="space-y-4">
                <div>
                    <label for="nom_utilisateur" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" id="nom_utilisateur" name="nom_utilisateur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required value="<?= htmlspecialchars($userInfo['nom_utilisateur']) ?>">
                </div>
                <div>
                    <label for="prenom_utilisateur" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" id="prenom_utilisateur" name="prenom_utilisateur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required value="<?= htmlspecialchars($userInfo['prenom']) ?>">
                </div>
                <div>
                    <label for="email_utilisateur" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email_utilisateur" name="email_utilisateur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required value="<?= htmlspecialchars($userInfo['couriel']) ?>">
                </div>
                <div>
                    <label for="telephone_utilisateur" class="block text-sm font-medium text-gray-700">Téléphone</label>
                    <input type="text" id="telephone_utilisateur" name="telephone_utilisateur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required value="<?= htmlspecialchars($userInfo['telephone']) ?>">
                </div>
            </div>
            <!-- Colonne 2 -->
            <div class="space-y-4">
                <div>
                    <label for="adresse_utilisateur" class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input type="text" id="adresse_utilisateur" name="adresse_utilisateur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required value="<?= htmlspecialchars($userInfo['rue']) ?>">
                </div>
                <div>
                    <label for="ville_utilisateur" class="block text-sm font-medium text-gray-700">Ville</label>
                    <input type="text" id="ville_utilisateur" name="ville_utilisateur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required value="<?= htmlspecialchars($userInfo['ville']) ?>">
                </div>
                <div>
                    <label for="code_postal_utilisateur" class="block text-sm font-medium text-gray-700">Code Postal</label>
                    <input type="text" id="code_postal_utilisateur" name="code_postal_utilisateur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required value="<?= htmlspecialchars($userInfo['code_postal']) ?>">
                </div>
                <div>
                    <label for="province_utilisateur" class="block text-sm font-medium text-gray-700">Province</label>
                    <input type="text" id="province_utilisateur" name="province_utilisateur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required value="<?= htmlspecialchars($userInfo['province']) ?>">
                </div>
                <div>
                    <label for="pays_utilisateur" class="block text-sm font-medium text-gray-700">Pays</label>
                    <input type="text" id="pays_utilisateur" name="pays_utilisateur" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required value="<?= htmlspecialchars($userInfo['pays']) ?>">
                </div>
            </div>
            <!-- Bouton de soumission -->
            <div class="col-span-1 md:col-span-2 flex justify-end mt-4">
                <button type="submit" name="updateProfile" class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Modification Mot de Passe -->
<div id="modalModifierMotDePasse" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-1/2">
        <h2 class="text-2xl text-center text-blue-600 font-semibold mb-4">Modifier le Mot de Passe</h2>
        <form method="POST">
            <div class="grid gap-4">
                <div>
                    <label class="block font-semibold">Ancien Mot de Passe</label>
                    <input type="password" name="ancien_mot_de_passe" 
                        class="w-full border border-gray-300 p-2 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold">Nouveau Mot de Passe</label>
                    <input type="password" name="nouveau_mot_de_passe" 
                        class="w-full border border-gray-300 p-2 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold">Confirmer le Nouveau Mot de Passe</label>
                    <input type="password" name="confirmation_mot_de_passe" 
                        class="w-full border border-gray-300 p-2 rounded-lg">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
                    onclick="document.getElementById('modalModifierMotDePasse').classList.add('hidden')">
                    Annuler
                </button>
                <button type="submit" name="updatePassword" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Modifier
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = button.getAttribute('data-modal-target');
            document.querySelector(modalId).classList.toggle('hidden');
        });
    });

    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            document.querySelector(modalId).classList.remove('hidden');
        });
    });
    
    function openModal(orderId) {
        const modal = document.getElementById(`modalAnnulerCommande${orderId}`);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('block');
        }
    }
    function closeModal(orderId) {
        const modal = document.getElementById(`modalAnnulerCommande${orderId}`);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('block');
        }
    }
</script>

</body>
</html> 