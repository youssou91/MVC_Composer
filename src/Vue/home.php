<?php
include_once(__DIR__ . '/../../static/header.php'); // Exemple de chemin basé sur la structure

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

try {
    $connect = new PDO('mysql:host=localhost;dbname=cours343', 'root', '');
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialisation du panier si nécessaire
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ajouter un produit au panier
if (isset($_POST['add'])) {
    $idProduit = $_GET['id'];  // ID du produit (transmis via l'URL)
    $nomProduit = $_POST['nom'];
    $prixUnitaire = (float) $_POST['prix_unitaire'];
    $quantite = (int) $_POST['quantite'];

    // Vérifier si le produit existe déjà dans le panier
    if (isset($_SESSION['cart'][$idProduit])) {
        // Mise à jour de la quantité
        $nouvelleQuantite = $_SESSION['cart'][$idProduit]['quantite'] + $quantite;

        // Vérifier la disponibilité en stock
        $query = "SELECT quantite FROM Produits WHERE id_produit = :idProduit";
        $stmt = $connect->prepare($query);
        $stmt->execute([':idProduit' => $idProduit]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($nouvelleQuantite <= $row['quantite']) {
            $_SESSION['cart'][$idProduit]['quantite'] = $nouvelleQuantite;
        } else {
            echo '<script>alert("Quantité demandée non disponible en stock")</script>';
        }
    } else {
        // Ajouter un nouveau produit
        $_SESSION['cart'][$idProduit] = [
            'id_produit' => $idProduit,
            'nom' => $nomProduit,
            'prix_unitaire' => $prixUnitaire,
            'quantite' => $quantite
        ];
    }
    
    // Redirection vers la page d'accueil ou une autre page après l'ajout
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Mettre à jour la quantité d'un produit
if (isset($_POST['update'])) {
    $idProduit = $_POST['id_produit'];
    $nouvelleQuantite = (int) $_POST['quantite'];

    if ($nouvelleQuantite > 0) {
        // Vérifier la disponibilité en stock
        $query = "SELECT quantite FROM Produits WHERE id_produit = :idProduit";
        $stmt = $connect->prepare($query);
        $stmt->execute([':idProduit' => $idProduit]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($nouvelleQuantite <= $row['quantite']) {
            $_SESSION['cart'][$idProduit]['quantite'] = $nouvelleQuantite;
        } else {
            echo '<script>alert("Quantité demandée non disponible en stock")</script>';
        }
    }
    // Redirection vers la même page après la mise à jour
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Supprimer un produit du panier
if (isset($_POST['remove'])) {
    $idProduit = $_POST['id_produit'];
    unset($_SESSION['cart'][$idProduit]);
    // Redirection vers la même page après la suppression
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Vider le panier
if (isset($_POST['empty_cart'])) {
    unset($_SESSION['cart']);
    // Redirection vers la même page après avoir vidé le panier
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Calculer le total du panier
$somme = 0;
foreach ($_SESSION['cart'] as $produit) {
    $somme += $produit['prix_unitaire'] * $produit['quantite'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma boutique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-200 m-5">
    <div class="container mx-auto">
        <h2 class="text-center text-2xl text-blue-600 font-bold my-4">Ma boutique</h2>

        <!-- Section du Panier -->
        <div class="bg-white rounded shadow-md p-4 mb-4">
            <h3 class="text-2xl font-bold text-blue-600 text-center mb-4">Mon Panier</h3>
            <?php if (count($_SESSION['cart']) > 0): ?>
                <ul class="list-disc pl-5">
                    <?php foreach ($_SESSION['cart'] as $idProduit => $produit): ?>
                        <div class="produit">
                            <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                            <p>Prix : <?= number_format($produit['prix_unitaire'], 2) ?> $</p>
                            <p>Quantité : <?= $produit['quantite'] ?></p>
                            <form method="post">
                                <input type="hidden" name="id_produit" value="<?= $idProduit; ?>">
                                <button type="submit" name="remove" class="text-red-500 hover:text-red-700">Supprimer</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </ul>
                <div class="mt-4 flex justify-between items-center">
                    <span class="font-bold text-lg">Total: <?= number_format($somme, 2); ?> $</span>
                    <form method="post">
                        <button type="submit" name="empty_cart" class="text-red-500 hover:text-red-700">Vider le panier</button>
                    </form>
                </div>
            <?php else: ?>
                <p class="text-center">Aucun produit dans le panier.</p>
            <?php endif; ?>
        </div>
        <!-- Section des Produits -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php
            $query = "
                SELECT p.*, pr.valeur AS promo_valeur, pr.type AS promo_type
                FROM Produits p
                LEFT JOIN ProduitPromotion pp ON p.id_produit = pp.id_produit
                LEFT JOIN Promotions pr ON pp.id_promotion = pr.id_promotion
                WHERE p.quantite > 0
                AND (pr.date_debut IS NULL OR pr.date_debut <= CURDATE())
                AND (pr.date_fin IS NULL OR pr.date_fin >= CURDATE());
            ";
            $stmt = $connect->prepare($query);
            $stmt->execute();
            $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($produits):
                foreach ($produits as $produit):
                    $idProduit = $produit['id_produit'];
                    $nom = htmlspecialchars($produit['nom']);
                    $prix = $produit['prix_unitaire'];
                    $cheminImage = !empty($produit['chemin_image']) ? htmlspecialchars($produit['chemin_image']) : 'public/uploads/1732401040_react.png';  // Corrected image path
                    $promoType = $produit['promo_type'];
                    $promoValeur = $produit['promo_valeur'];
                    $prixReduit = $prix;
                    if ($promoType === 'pourcentage') {
                        $prixReduit = $prix - ($prix * $promoValeur / 100);
                    } elseif ($promoType === 'fixe') {
                        $prixReduit = max(0, $prix - $promoValeur);
                    }
                    ?>
                    <div class="border rounded shadow-lg p-4 bg-white">
                        <img src="/public/<?= htmlspecialchars($produit['chemin_image']); ?>" class="w-full h-48 object-cover mb-4"> <!-- Display image -->
                        <h3 class="text-xl font-bold"><?= $nom ?></h3>
                        <p class="text-gray-600">Prix : <?= number_format($prixReduit, 2) ?> €</p>
                        <form method="post" class="mt-2">
                            <input type="hidden" name="nom" value="<?= $nom; ?>">
                            <input type="hidden" name="prix_unitaire" value="<?= $prixReduit; ?>">
                            <input type="number" name="quantite" min="1" value="1" class="border p-2 rounded">
                            <button type="submit" name="add" class="bg-blue-600 text-white p-2 rounded mt-2 w-full">Ajouter au panier</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
