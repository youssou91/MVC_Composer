<?php
include '../Head.php';
include '../../Controlleur/UserControlleur.php'; 
include '../../config.php'; 

if (!isset($_SESSION['id_utilisateur'])) {
    echo '<script>window.location.href = "connexion.php";</script>';
    exit;
}

$dbConnection = getConnection();
$userController = new UserController($dbConnection);
$userId = $_SESSION['id_utilisateur'];
$userInfo = $userController->getUserInfo($userId);
$userOrders = $userController->getUserOrders($userId);

if (isset($_POST['action'])) {
    $orderId = $_POST['order_id'];
    $action = $_POST['action'];
    switch ($action) {
        case 'traiter':
            update_commandeOrderstatut($orderId, 'En traitement');
            break;
        case 'expédier':
            update_commandeOrderstatut($orderId, 'En expedition');
            break;
        case 'annuler':
            update_commandeOrderstatut($orderId, 'Annulee');
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
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-8">
    <h1 class="text-3xl text-center font-semibold mb-5">Mon Profil</h1>
    <div class="flex flex-col lg:flex-row gap-5">
        <div class="bg-white p-6 rounded-lg shadow-md lg:w-1/3">
            <h3 class="text-xl text-center font-semibold mb-4">Informations personnelles</h3>
            <p><span class="font-semibold">Nom:</span> <?= htmlspecialchars($userInfo['nom_utilisateur']) ?></p>
            <p><span class="font-semibold">Prénom:</span> <?= htmlspecialchars($userInfo['prenom']) ?></p>
            <p><span class="font-semibold">Email:</span> <?= htmlspecialchars($userInfo['couriel']) ?></p>
            <p><span class="font-semibold">Téléphone:</span> <?= htmlspecialchars($userInfo['telephone']) ?></p>
            <h4 class="text-lg font-semibold mt-4">Adresse</h4>
            <p><span class="font-semibold">Rue:</span> <?= htmlspecialchars($userInfo['numero']).' '.htmlspecialchars($userInfo['rue']) ?></p>
            <p><span class="font-semibold">Code Postal:</span> <?= htmlspecialchars($userInfo['code_postal']) ?></p>
            <p><span class="font-semibold">Ville:</span> <?= htmlspecialchars($userInfo['ville']).', '.htmlspecialchars($userInfo['province']) ?></p>
            <p><span class="font-semibold">Pays:</span> <?= htmlspecialchars($userInfo['pays']) ?></p>

            <div class="mt-6 flex gap-4">
                <!-- Bouton Modifier les informations avec icône FontAwesome -->
                <button class="bg-yellow-500 text-white py-2 px-4 rounded-lg hover:bg-yellow-600" data-modal-target="#modalModifierProfil">
                    <i class="fas fa-user-edit"></i> Modifier infos
                </button>
    
                <!-- Bouton Modifier le mot de passe avec icône FontAwesome -->
                <button class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600" data-modal-target="#modalModifierMotDePasse">
                    <i class="fas fa-key"></i> Modifier mot de passe
                </button>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md lg:w-2/3">
            <h3 class="text-xl text-center font-semibold mb-4">Mes Commandes</h3>
            <?php if (count($userOrders) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-center border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 border-b">#</th>
                                <th class="py-2 px-4 border-b">Date</th>
                                <th class="py-2 px-4 border-b">Montant</th>
                                <th class="py-2 px-4 border-b">Statut</th>
                                <th class="py-2 px-4 border-b">Détails</th>
                                <th class="py-2 px-4 border-b">Annuler</th>
                                <th class="py-2 px-4 border-b">Paiement</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $index = 1; ?>
                            <?php foreach ($userOrders as $order): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-4"><?= $index++; ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($order['date_commande']) ?></td>
                                    <td class="py-2 px-4">$ <?= htmlspecialchars($order['prix_total']) ?></td>
                                    <td class="py-2 px-4"><?= htmlspecialchars($order['statut']) ?></td>
                                    <td class="py-2 px-4">
                                        <a href="details_commande.php?id_commande=<?= htmlspecialchars($order['id_commande']) ?>" class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600">Détails</a>
                                    </td>
                                    <td class="py-2 px-4">
                                        <button type="button" class="bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600" data-modal-target="#modalAnnulerCommande<?= htmlspecialchars($order['id_commande']) ?>">Annuler</button>
                                        <div id="modalAnnulerCommande<?= htmlspecialchars($order['id_commande']) ?>" class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
                                            <div class="bg-white rounded-lg p-6 w-96">
                                                <h5 class="text-lg font-semibold mb-4">Annulation de commande</h5>
                                                <p class="mb-4">Voulez-vous vraiment annuler cette commande ?</p>
                                                <form method="post" class="flex justify-between">
                                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id_commande']) ?>">
                                                    <button type="submit" name="action" value="annuler" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Confirmer l'annulation</button>
                                                    <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600" onclick="document.getElementById('modalAnnulerCommande<?= htmlspecialchars($order['id_commande']) ?>').classList.add('hidden')">Fermer</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4">
                                        <form action="paiement_commande.php" method="post">
                                            <input type="hidden" name="id_commande" value="<?= htmlspecialchars($order['id_commande']) ?>">
                                            <button type="submit" class="bg-green-500 text-white py-1 px-3 rounded hover:bg-green-600" <?= ($order['statut'] == 'Annulee') ? 'disabled' : '' ?>>Payer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">Aucune commande trouvée.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    // Toggle modal visibility
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            document.querySelector(modalId).classList.remove('hidden');
        });
    });
</script>
<!-- Modal Modifier les informations -->
<div id="modalModifierProfil" class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-96">
        <h5 class="text-lg font-semibold mb-4">Modifier les informations</h5>
        <form method="post" action="modifier_profil.php">
            <!-- Ajouter ici vos champs de formulaire pour modifier les informations -->
            <div class="mb-4">
                <label for="nom_utilisateur" class="block font-semibold">Nom</label>
                <input type="text" id="nom_utilisateur" name="nom_utilisateur" class="w-full border-gray-300 border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label for="prenom_utilisateur" class="block font-semibold">Prénom</label>
                <input type="text" id="prenom_utilisateur" name="prenom_utilisateur" class="w-full border-gray-300 border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label for="email_utilisateur" class="block font-semibold">Email</label>
                <input type="email" id="email_utilisateur" name="email_utilisateur" class="w-full border-gray-300 border p-2 rounded" required>
            </div>
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Enregistrer</button>
                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600" onclick="toggleModal('modalModifierProfil')">Fermer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modifier le mot de passe -->
<div id="modalModifierMotDePasse" class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg p-6 w-96">
        <h5 class="text-lg font-semibold mb-4">Modifier le mot de passe</h5>
        <form method="post" action="modifier_mot_de_passe.php">
            <div class="mb-4">
                <label for="ancien_mot_de_passe" class="block font-semibold">Ancien mot de passe</label>
                <input type="password" id="ancien_mot_de_passe" name="ancien_mot_de_passe" class="w-full border-gray-300 border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label for="nouveau_mot_de_passe" class="block font-semibold">Nouveau mot de passe</label>
                <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" class="w-full border-gray-300 border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label for="confirmation_mot_de_passe" class="block font-semibold">Confirmer le nouveau mot de passe</label>
                <input type="password" id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" class="w-full border-gray-300 border p-2 rounded" required>
            </div>
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Modifier</button>
                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600" onclick="toggleModal('modalModifierMotDePasse')">Fermer</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Fonction pour afficher et masquer les modals
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.toggle('hidden');
    }

    // Ajouter l'événement pour ouvrir les modals
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            toggleModal(modalId);
        });
    });
</script>
</body>
</html>
