<?php
// Récupération de l'ID de commande depuis l'URL
$request_uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($request_uri, '/'));

if (isset($parts[2]) && is_numeric($parts[2])) {
    $order_id = (int)$parts[2]; // L'ID de commande
} else {
    die("Aucune commande spécifiée ou URL invalide.");
}

// Simulez la récupération du prix total (par exemple depuis une base de données)
$prix_total = 100; // Exemple statique pour test
if ($prix_total === null || $prix_total === false) {
    die("Erreur lors de la récupération du prix total.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement PayPal</title>
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&components=buttons"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Paiement PayPal</h1>
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
            <div class="text-center mb-4">
                <h2 class="text-lg font-semibold text-gray-600">Montant à payer</h2>
                <p class="text-2xl font-bold text-green-600">$ <?= number_format($prix_total, 2, '.', ' ') ?></p>
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
                            value: '<?= $prix_total ?>' // Utilise le prix total dynamique
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction réussie par ' + details.payer.name.given_name);
                    window.location.href = "order_confirmation.php?orderID=" + data.orderID;
                });
            },
            onCancel: function(data) {
                alert('Transaction annulée.');
            },
            onError: function(err) {
                console.error('Erreur lors du paiement', err);
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
