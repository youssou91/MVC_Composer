<?php
require __DIR__ . '/../../vendor/autoload.php';

use App\Controlleur\CommandeControlleur;

// Initialisez les dépendances
$dbConnection = getConnection();  

// Vérifier si l'ID de la commande est bien envoyé via POST
if (isset($_POST['id_commande'])) {
    $order_id = $_POST['id_commande']; // Récupérer l'ID de la commande
    // print_r($order_id);
    // Instancier le modèle CommandeModel en passant la connexion PDO
    $commandeModel = new \App\Modele\CommandeModel($dbConnection);

    // Créer le contrôleur CommandeControlleur et lui passer l'objet CommandeModel
    $commandeController = new CommandeControlleur($commandeModel);

    // Appeler la méthode afficherTotalCommande
    $prix_total = $commandeController->afficherTotalCommande($order_id);
    // Appeler l'action pour afficher le total
    if ($prix_total === null || $prix_total === false) {
        die("Erreur lors de la récupération du prix total.");
    }
} else {
    die("Aucune commande spécifiée.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement PayPal</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AWW6GZJg_ShlBU7L34BaliLIpxsvWrKKEVzKCOUBKUXMX2wapM7rcA-SlpYwQ4Nr5i7-aliEssT-gF4N&components=buttons"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Paiement PayPal</h1>
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
            <div class="text-center mb-4">
                <h2 class="text-lg font-semibold text-gray-600">Montant à payer</h2>
                <p class="text-2xl font-bold text-green-600">$<?= number_format($prix_total, 2, '.', ' ') ?></p>
            </div>
            <div id="paypal-button-container" class="mt-4"></div>
        </div>
    </div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= $prix_total ?>'
                        },
                        description: 'Paiement pour la commande #<?= $order_id ?>',
                        custom_id: '<?= $order_id ?>'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction réussie par ' + details.payer.name.given_name);
                    // Redirection vers une page de confirmation après paiement réussi
                    window.location.href = "/profile/confirmation?id_commande=<?= $order_id ?>";
                });
            },
            onCancel: function(data) {
                alert('Transaction annulée.');
            },
            onError: function(err) {
                alert('Une erreur est survenue lors du paiement.');
                console.error('Erreur :', err);
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
