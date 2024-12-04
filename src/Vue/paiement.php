<?php
require __DIR__ . '/../../vendor/autoload.php';

use App\Controlleur\CommandeControlleur;

// Initialisation des dépendances
$dbConnection = getConnection();
$commandeController = new CommandeControlleur(new \App\Modele\CommandeModel($dbConnection));

// Validation des données POST
if (!isset($_POST['id_commande']) || !isset($_POST['prix_total'])) {
    header('Location: profile.php'); // Redirection en cas d'erreur
    exit;
}

$order_id = htmlspecialchars($_POST['id_commande']);
$prix_total = htmlspecialchars($_POST['prix_total']);
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
<body class="bg-gray-200 m-5">
    <div class="container mx-auto">
        <h1 class="text-3xl text-center text-blue-600 font-semibold mb-7 mt-7">Paiement PayPal</h1>
        <div class="flex justify-center">
            <div class="bg-white rounded shadow-md p-6 mb-8">
                <div class="text-center mb-3">
                    <h2 class="text-xl font-semibold text-blue-700">Montant à payer</h2>
                    <p class="text-3xl font-extrabold text-green-500 mt-2">$<?= number_format($prix_total, 2, '.', ' ') ?></p>
                </div>
                <div id="paypal-button-container" class="mt-4 flex justify-center"></div>
            </div>
        </div>
    </div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: { value: '<?= $prix_total ?>' },
                        description: 'Paiement pour la commande #<?= $order_id ?>',
                        custom_id: '<?= $order_id ?>'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction réussie par ' + details.payer.name.given_name);
                    // Redirection vers une page de confirmation
                    window.location.href = "/profile/confirmation?id_commande=<?= $order_id ?>";
                });
            },
            onCancel: function(data) {
                alert('Transaction annulée.');
            },
            onError: function(err) {
                alert('Une erreur est survenue. Veuillez réessayer.');
                console.error('Erreur PayPal:', err);
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
