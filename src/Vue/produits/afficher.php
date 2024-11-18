<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>  
        <div class="container">
            <h1>Hello, World!</h1>
            <p>This is a simple webpage.</p>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. 
                Rerum ex odio voluptatibus debitis autem, dolorem amet nihil aspernatur maiores,
                fuga, vitae ullam porro quo nostrum mollitia qui quaerat enim ipsam
            </p>
            <h1><?= htmlspecialchars($product['nom']) ?></h1>
            <p>Prix : <?= htmlspecialchars($product['prix']) ?> €</p>
            <p>Description : <?= htmlspecialchars($product['description']) ?></p>
            <a href="/cart/add?id=<?= $product['id'] ?>">Ajouter au panier</a>
        </div>
    </body>
</html>