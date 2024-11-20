<?php
include_once(__DIR__ . '/../../static/header.php'); // Exemple de chemin basé sur la structure

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
    $idProduit = $_GET['id'];
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
    echo '<script>window.location.href = "accueil.php";</script>';
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
    echo '<script>window.location.href = "accueil.php";</script>';
}

// Supprimer un produit du panier
if (isset($_POST['remove'])) {
    $idProduit = $_POST['id_produit'];
    unset($_SESSION['cart'][$idProduit]);
    echo '<script>window.location.href = "accueil.php";</script>';
}

// Vider le panier
if (isset($_POST['empty_cart'])) {
    unset($_SESSION['cart']);
    echo '<script>window.location.href = "accueil.php";</script>';
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
        <div class="container mx-auto">
            <h2 class="text-center text-2xl font-bold my-4">Ma boutique</h2>
            
            <!-- Section du Panier -->
            <div class="bg-white rounded shadow-md p-4 mb-4">
                <h3 class="text-2xl font-bold text-center mb-4">Mon Panier</h3>
                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <ul class="list-disc pl-5">
                        <?php 
                        $somme = 0;
                        foreach ($_SESSION['cart'] as $idProduit => $produit):
                            $nom = $produit['nom'];
                            $prixReduit = $produit['prix_unitaire'];
                            $quantite = $produit['quantite'];
                            $somme += $prixReduit * $quantite;
                        ?>
                            <div class="produit">
                                <h3><?= $nom ?></h3>
                                <p>Prix : <?= $prixReduit ?> €</p>
                                <p>Quantité : <?= $quantite ?></p>
                                <form method="post">
                                    <input type="hidden" name="id_produit" value="<?= $idProduit; ?>">
                                    <button type="submit" name="remove" class="text-red-500 hover:text-red-700">Supprimer</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </ul>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="font-bold text-lg">Total: <?= number_format($somme, 2); ?>€</span>
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
                    SELECT p.*, i.chemin_image, pr.valeur AS promo_valeur, pr.type AS promo_type
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

                if ($produits):
                    foreach ($produits as $produit):
                        $idProduit = $produit['id_produit'];
                        $nom = htmlspecialchars($produit['nom']);
                        $prix = $produit['prix_unitaire'];
                        $cheminImage = !empty($produit['chemin_image']) ? htmlspecialchars($produit['chemin_image']) : 'images/default-product.jpg';

                        // Calcul du prix réduit en cas de promotion
                        $promoType = $produit['promo_type'];
                        $promoValeur = $produit['promo_valeur'];
                        $prixReduit = $prix;

                        if ($promoType === 'pourcentage') {
                            $prixReduit = $prix - ($prix * $promoValeur / 100);
                        } elseif ($promoType === 'fixe') {
                            $prixReduit = $prix - $promoValeur;
                        }
                        ?>
                        <div class="bg-white rounded-lg shadow-md p-4">
                            <img class="w-full h-48 object-cover rounded mb-2" 
                                src="<?= $cheminImage; ?>" 
                                alt="<?= $nom; ?>">
                            <h5 class="text-lg font-bold text-center"><?= $nom; ?></h5>
                            <p class="text-center">
                                Prix: 
                                <?php if ($prixReduit < $prix): ?>
                                    <span class="line-through text-red-500"><?= number_format($prix, 2); ?>€</span>
                                    <span class="text-green-500"><?= number_format($prixReduit, 2); ?>€</span>
                                <?php else: ?>
                                    <span class="text-green-500"><?= number_format($prixReduit, 2); ?>€</span>
                                <?php endif; ?>
                            </p>
                            <form method="post">
                                <input type="hidden" name="nom" value="<?= $nom; ?>">
                                <input type="hidden" name="prix_unitaire" value="<?= $prixReduit; ?>">
                                <input type="number" name="quantite" min="1" value="1" class="w-full p-2 border rounded mb-2">
                                <button type="submit" name="add" class="bg-blue-500 text-white p-2 rounded w-full">Ajouter au panier</button>
                            </form>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </body>
</html>
