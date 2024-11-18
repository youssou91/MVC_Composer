<h1>Nos produits</h1>
<ul>
    <li><a href="/products/1">Produit 1</a></li>
    <li><a href="/products/2">Produit 2</a></li>
    <li><a href="/products/3">Produit 3</a></li>
    <h1>Nos produits</h1>
    <ul>
        <?php foreach ($products as $product): ?>
            <li>
                <a href="/products/<?= $product['id_produit'] ?>">
                    <?= htmlspecialchars($product['nom']) ?> - <?= htmlspecialchars($product['prix']) ?> €
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

</ul>