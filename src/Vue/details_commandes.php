<?php
// include 'header.php'; 

if (!isset($_GET['id_commande'])) {
    die("ID de commande manquant.");
}

$id_commande = intval($_GET['id_commande']);
// $conn = connexionDB(); 

// Récupérer les détails de la commande
$sql_commande = "SELECT c.id_commande, c.date_commande, c.prix_total, u.nom_utilisateur, u.prenom
                 FROM commande c
                 JOIN utilisateur u ON c.id_utilisateur = u.id_utilisateur
                 WHERE c.id_commande = ?";
$stmt_commande = mysqli_prepare($conn, $sql_commande);
mysqli_stmt_bind_param($stmt_commande, "i", $id_commande);
mysqli_stmt_execute($stmt_commande);
$result_commande = mysqli_stmt_get_result($stmt_commande);

if ($result_commande && mysqli_num_rows($result_commande) > 0) {
    $commande = mysqli_fetch_assoc($result_commande);
} else {
    die("Commande non trouvée.");
}

// Récupérer les produits de la commande
$sql_produits = "SELECT pc.quantite, p.nom, p.prix_unitaire
                 FROM produit_commande pc
                 JOIN produits p ON pc.id_produit = p.id_produit
                 WHERE pc.id_commande = ?";
$stmt_produits = mysqli_prepare($conn, $sql_produits);
mysqli_stmt_bind_param($stmt_produits, "i", $id_commande);
mysqli_stmt_execute($stmt_produits);
$result_produits = mysqli_stmt_get_result($stmt_produits);
?>
<div class="container">
    <h2 class="text-center">Détails de la Commande #<?= $commande['id_commande'] ?></h2>
    <p><strong>Date de la commande:</strong> <?= $commande['date_commande'] ?></p>
    <p><strong>Prix total:</strong> $<?= number_format($commande['prix_total'], 2) ?></p>
    <p><strong>Nom utilisateur:</strong> <?= htmlspecialchars($commande['nom_utilisateur']).' '. htmlspecialchars($commande['prenom']) ?></p>
    
    <h3>Produits commandés</h3>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nom produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Prix total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result_produits) > 0) {
                while ($produit = mysqli_fetch_assoc($result_produits)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($produit['nom']) . "</td>";
                    echo "<td>" . $produit['quantite'] . "</td>";
                    echo "<td>$" . number_format($produit['prix_unitaire'], 2) . "</td>";
                    echo "<td>$" . number_format($produit['quantite'] * $produit['prix_unitaire'], 2) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>Aucun produit trouvé pour cette commande</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
