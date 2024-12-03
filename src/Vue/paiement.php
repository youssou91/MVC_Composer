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




<script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
    </script>
</html>

 <!-- Modal -->
 <div id="modal-<?= $idProduit ?>" 
    class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-8 relative">
        <!-- Bouton de fermeture -->
        <button onclick="closeModal('modal-<?= $idProduit ?>')" 
                class="absolute top-4 right-4 text-gray-600 hover:text-red-600 text-2xl">
            ✕
        </button>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <img src="/public/<?= $cheminImage ?>" class="w-full h-auto rounded-lg shadow-md">
            <div>
                <h2 class="text-2xl font-bold mb-4"><?= $nom ?></h2>
                <p class="text-gray-700 mb-2">
                    <strong>Prix :</strong> <?= number_format($prix, 2) ?> $
                </p>
                <?php if ($promoType): ?>
                    <p class="text-green-500 mb-4">
                        <strong>Prix réduit :</strong> <?= number_format($prixReduit, 2) ?> $
                    </p>
                <?php endif; ?>
                <p class="text-gray-700 mb-2">
                    <strong>Stock Disponible :</strong> <?= $quantiteStock ?> 
                </p>
                <div>
                    <p class="text-gray-600 mb-4"><strong>Couleurs disponibles</strong></p>
                    <div class="mt-2 grid grid-cols-3 gap-4">
                        <?php
                        // Tableau des couleurs avec leurs classes Tailwind
                        $couleurs = [
                            'Rouge' => 'bg-red-500',
                            'Bleu' => 'bg-blue-500',
                            'Vert' => 'bg-green-500',
                            'Noir' => 'bg-black text-white',
                            'Blanc' => 'bg-white border-gray-300 text-gray-700',
                            'Gris' => 'bg-gray-500 text-white',
                            'Jaune' => 'bg-yellow-500',
                            'Rose' => 'bg-pink-500',
                            'Marron' => 'bg-amber-700'
                        ];

                        // Vérifier si la chaîne des couleurs est bien définie et la décoder
                        if (isset($produit['couleurs']) && !empty($produit['couleurs'])) {
                            $couleursProduit = json_decode($produit['couleurs'], true);  // Décode en tableau

                            // Vérifier si le résultat du json_decode est bien un tableau
                            if (is_array($couleursProduit)) {
                                foreach ($couleursProduit as $couleur) {
                                    // Vérifier que la couleur existe dans le tableau $couleurs
                                    if (array_key_exists($couleur, $couleurs)) {
                                        $classeCouleur = $couleurs[$couleur]; // Récupérer la classe Tailwind correspondante
                                        echo "
                                        <span class='inline-block px-4 py-2 $classeCouleur text-white rounded-md shadow-md'>
                                            $couleur
                                        </span>";
                                    }
                                }
                            } else {
                                // Si json_decode échoue, afficher un message d'erreur ou ignorer
                                echo "<p>Erreur de format des couleurs disponibles.</p>";
                            }
                        } else {
                            echo "<p>Aucune couleur disponible.</p>";
                        }
                        ?>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">
                    <strong>Description :</strong> <?= $descripton ?> 
                </p>
            </div>
        </div>
    </div>
</div>