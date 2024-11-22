<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1>Mon Panier</h1>

    <?php if (!empty($panier)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix Unitaire (€)</th>
                    <th>Quantité</th>
                    <th>Total (€)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panier as $item) : ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nom']) ?></td>
                        <td><?= htmlspecialchars(number_format($item['prix'], 2)) ?></td>
                        <td><?= htmlspecialchars($item['quantite']) ?></td>
                        <td><?= htmlspecialchars(number_format($item['total'], 2)) ?></td>
                        <td>
                            <form action="/cart/modifier" method="POST" style="display:inline;">
                                <input type="hidden" name="produit_id" value="<?= $item['produit_id'] ?>">
                                <input type="number" name="quantite" value="<?= $item['quantite'] ?>" min="1" style="width: 50px;">
                                <button type="submit">Modifier</button>
                            </form>
                            <form action="/cart/supprimer" method="POST" style="display:inline;">
                                <input type="hidden" name="produit_id" value="<?= $item['produit_id'] ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total Général</th>
                    <th colspan="2">
                        <?= htmlspecialchars(number_format(array_sum(array_column($panier, 'total')), 2)) ?> €
                    </th>
                </tr>
            </tfoot>
        </table>
    <?php else : ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>

    <a href="/produits" style="margin-top: 20px; display: inline-block;">Continuer vos achats</a>
</body>
</html>
