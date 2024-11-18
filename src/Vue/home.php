<?php
include '../MVC/Vue/Head.php';
// Définition des routes avec les chemins de fichiers correspondants

function estConnecte() {
    return isset($_SESSION['user_id']); // Vérifiez que la variable de session de l'utilisateur connecté existe
}
// Redirection conditionnelle lors de la commande
if (isset($_POST['commander'])) {
    if (estConnecte()) {
        // Redirection vers le profil de l'utilisateur si connecté
        header("Location: ../MVC/Vue/Users/Profile.php");
    } else {
        // Redirection vers la page de connexion si non connecté
        header("Location: ../MVC/Vue/Users/Connexion.php");
    }
    exit;
}
// Récupération de l'URI demandée
$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($uri, PHP_URL_PATH); // Supprime les éventuels paramètres de requête

// Vérification si l'URI correspond à une route définie
if (array_key_exists($uri, $routes)) {
    require $routes[$uri];
} else {
    // Gestion de la page 404 si la route n'existe pas
    require '../MVC/Vue/404.php';
}
/////////////////////////////////////////////////////////////////////////////////
//  session_start(); // Assurez-vous de démarrer la session au début
// Connexion à la base de données avec PDO
try {
    $connect = new PDO('mysql:host=localhost;dbname=cours343', 'root', '');
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
// Ajouter des produits au panier
if (isset($_POST['add'])) {
    $idProduit = $_GET['id'];
    $nomProduit = $_POST['nom'];
    $prixUnitaire = (float) $_POST['prix_unitaire'];
    $quantite = (int) $_POST['quantite'];
    if (isset($_SESSION['cart'])) {
        $item_array_id = array_column($_SESSION['cart'], "id_produit");
        if (in_array($idProduit, $item_array_id)) {
            // Mise à jour de la quantité si le produit est déjà dans le panier
            foreach ($_SESSION['cart'] as $key => $value) {
                if ($value['id_produit'] == $idProduit) {
                    $nouvelleQuantite = $value['quantite'] + $quantite;
                    // Vérifier la disponibilité en stock
                    $query = "SELECT quantite FROM Produits WHERE id_produit = :idProduit";
                    $stmt = $connect->prepare($query);
                    $stmt->execute([':idProduit' => $idProduit]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($nouvelleQuantite <= $row['quantite']) {
                        $_SESSION['cart'][$key]['quantite'] = $nouvelleQuantite;
                        $_SESSION['cart'][$key]['prix_unitaire'] = $prixUnitaire; // Mettre à jour le prix unitaire
                    } else {
                        echo '<script>alert("Quantité demandée non disponible en stock")</script>';
                    }
                    break;
                }
            }
        } else {
            // Ajout d'un nouveau produit au panier
            $item_array = array(
                'id_produit' => $idProduit,
                'nom' => $nomProduit,
                'prix_unitaire' => $prixUnitaire,
                'quantite' => $quantite
            );
            $_SESSION['cart'][] = $item_array;
        }
    } else {
        // Création du panier et ajout du produit
        $item_array = array(
            'id_produit' => $idProduit,
            'nom' => $nomProduit,
            'prix_unitaire' => $prixUnitaire,
            'quantite' => $quantite
        );
        $_SESSION['cart'][0] = $item_array;
    }
    echo '<script>window.location="index.php"</script>';
}
// Vider le panier
if (isset($_POST['empty_cart'])) {
    unset($_SESSION['cart']);
    echo '<script>window.location="index.php"</script>';
}
// Supprimer un produit du panier
// Vérifier si la requête est un POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si le bouton "Modifier" a été cliqué
    if (isset($_POST['update'])) {
        $idProduit = $_POST['id_produit'];
        $quantite = (int)$_POST['quantite'];
        // Mettre à jour la quantité dans le panier
        if ($quantite > 0) {
            $_SESSION['cart'][$idProduit]['quantite'] = $quantite;
        }
    }
    // Si le bouton "Retirer" a été cliqué
    if (isset($_POST['remove'])) {
        $idProduit = $_POST['id_produit'];
        // Retirer le produit du panier
        unset($_SESSION['cart'][$idProduit]);
    }
    // Si le bouton "Vider le panier" a été cliqué
    if (isset($_POST['empty_cart'])) {
        // Vider le panier
        unset($_SESSION['cart']);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma boutique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> <!-- Inclure Tailwind CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-200 m-5">
    <!-- <div class="container m-5"> -->
        <div class="container mx-auto">
            <h2 class="text-center text-2xl font-bold my-4">Ma boutique</h2>
            <!-- Section du Panier -->
            <div class="bg-white rounded shadow-md p-4 mb-4">
                <h3 class="text-2xl font-bold text-center mb-4">Mon Panier</h3>
                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <ul class="list-disc pl-5">
                        <?php 
                        $somme = 0; // Initialiser la somme pour le total
                        foreach ($_SESSION['cart'] as $key => $item): 
                            $prixTotal = $item['prix_unitaire'] * $item['quantite'];
                            $somme += $prixTotal; // Ajouter à la somme totale
                        ?>
                            <li class="flex justify-between items-center mb-2 p-2 border-b border-gray-300">
                                <span class="flex-1 text-left"><?= htmlspecialchars($item['nom']); ?> (<?= $item['quantite']; ?>)</span>
                                <span class="font-bold"><?= number_format($prixTotal, 2); ?>$</span>
                                <div class="flex items-center ml-4">
                                    <form method="post" action="index.php" class="flex items-center">
                                        <input type="hidden" name="id_produit" value="<?= $key; ?>">
                                        <input type="number" name="quantite" value="<?= $item['quantite']; ?>" min="1" class="w-16 border rounded mx-2 p-1 text-center" />
                                        <!-- Bouton Modifier avec icône -->
                                        <button type="submit" name="update" class="text-blue-500 hover:text-blue-700">
                                            <i class="fas fa-edit"></i> <!-- Icône d'édition -->
                                        </button>
                                    </form>
                                    <form method="post" action="index.php" class="ml-2">
                                        <input type="hidden" name="id_produit" value="<?= $key; ?>">
                                        <!-- Bouton Retirer avec icône -->
                                        <button type="submit" name="remove" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash-alt"></i> <!-- Icône de poubelle -->
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <!-- <div class="mt-4 flex justify-between items-center">
                        <span class="font-bold text-lg">Total: <?= number_format($somme, 2); ?>$</span>
                        <form method="post" action="produit_commande.php">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Commander</button>
                        </form>
                    </div> -->
                    <div class="mt-4 flex justify-between items-center">
                        <span class="font-bold text-lg">Total: <?= number_format($somme, 2); ?>$</span>
                        <!-- Bouton Commander conditionnel avec vérification de connexion -->
                        <form method="post" action="index.php">
                            <button type="submit" name="commander" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                Commander
                            </button>
                        </form>
                    </div>
                    <form method="post" action="index.php" class="mt-2">
                        <button type="submit" name="empty_cart" class="text-red-500 hover:text-red-700">Vider le panier</button>
                    </form>
                <?php else: ?>
                    <p class="text-center">Aucun produit dans le panier.</p>
                <?php endif; ?>
            </div>
            <!-- Section des Produits -->
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php
                    // Requête pour récupérer les produits avec les promotions
                    $query = "
                        SELECT p.*, i.chemin_image, pr.valeur, pr.type
                        FROM Produits p
                        LEFT JOIN image i ON p.id_produit = i.id_produit
                        LEFT JOIN ProduitPromotion pp ON p.id_produit = pp.id_produit
                        LEFT JOIN Promotions pr ON pp.id_promotion = pr.id_promotion
                        WHERE p.quantite > 0
                        AND (pr.date_debut IS NULL OR pr.date_debut <= CURDATE())
                        AND (pr.date_fin IS NULL OR pr.date_fin >= CURDATE());
                    ";
                    $stmt = $connect->prepare($query);
                    $stmt->execute();
                    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($produits) {
                        foreach ($produits as $row) {
                            $idProduit = $row['id_produit'];
                            $nom = $row['nom'];
                            $prix = $row['prix_unitaire'];
                            $promoType = $row['type'];
                            $promoValeur = $row['valeur'];
                            $prixReduit = $prix;
                            // Calcul du prix réduit
                            if ($promoType && $promoValeur) {
                                $prixReduit = $prix - ($prix * $promoValeur / 100);
                            }
                            ?>
                            <div class="bg-white rounded shadow-md p-4">
                                <img class="w-full h-32 object-cover rounded" src="<?= isset($row['chemin_image']) && !empty($row['chemin_image']) ? $row['chemin_image'] : 'upload_images/Image 001.jpeg'; ?>" alt="<?= htmlspecialchars($nom); ?>" onclick="openModal('<?= htmlspecialchars($nom); ?>', <?= $prixReduit; ?>, <?= $row['id_produit']; ?>)">
                                <h5 class="text-center mt-2 font-bold"><?= htmlspecialchars($nom); ?></h5>
                                <p class="text-center">
                                    Prix: 
                                    <?php if ($promoType && $promoValeur): ?>
                                        <span class="line-through text-red-500"><?= number_format($prix, 2); ?>$</span>
                                        <span class="text-green-500"><?= number_format($prixReduit, 2); ?>$</span>
                                    <?php else: ?>
                                        <?= number_format($prix, 2); ?>$
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php }
                    } else {
                        echo '<p>Aucun produit disponible.</p>';
                    }
                ?>
            </div>
        </div>
        <!-- Modal -->
        <div id="productModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex justify-center items-center">
            <div class="bg-white rounded-lg shadow-lg p-4 w-11/12 max-w-md">
                <h2 id="modalProductName" class="text-xl font-bold mb-4"></h2>
                <p class="mb-4">Prix: <span id="modalProductPrice" class="font-semibold"></span></p>
                <form method="post" action="index.php" id="modalForm">
                    <input type="hidden" name="nom" id="modalNom">
                    <input type="hidden" name="prix_unitaire" id="modalPrixUnitaire">
                    <label for="quantite" class="block mb-2">Quantité:</label>
                    <input type="number" name="quantite" id="modalQuantite" value="1" min="1" class="border rounded w-full p-2 mb-4">
                    <button type="submit" name="add" class="bg-blue-500 text-white rounded py-2 w-full">Ajouter au panier</button>
                    <button type="button" onclick="closeModal()" class="mt-4 text-red-500">Fermer</button>
                </form>
            </div>
        </div>
        <script>
            function openModal(nom, prix, idProduit) {
                $('#modalProductName').text(nom);
                $('#modalProductPrice').text(prix.toFixed(2) + '$');
                $('#modalNom').val(nom);
                $('#modalPrixUnitaire').val(prix);
                $('#modalForm').attr('action', 'index.php?id=' + idProduit);
                $('#productModal').removeClass('hidden');
            }
            function closeModal() {
                $('#productModal').addClass('hidden');
            }
        </script>
    </body>
</html>
