<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One-Shop</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="<?= $router->generate('accueil'); ?>" class="text-2xl font-bold text-blue-600">YAHLI-SHOP</a>
            
            <!-- Toggle Menu (for mobile) -->
            <button id="menu-toggle" class="md:hidden focus:outline-none">
                <div class="w-6 h-1 bg-gray-700 mb-1"></div>
                <div class="w-6 h-1 bg-gray-700 mb-1"></div>
                <div class="w-6 h-1 bg-gray-700"></div>
            </button>

            <!-- Navigation -->
            <nav id="nav-menu" class="hidden md:flex space-x-6">
                <a href="<?= $router->generate('accueil'); ?>" class="text-gray-700 hover:text-blue-600">Accueil</a>
                <a href="<?= $router->generate('contacter'); ?>" class="text-gray-700 hover:text-blue-600">Contact</a>
                
                <!-- Menu Admin (conditionnel en PHP) -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="<?= $router->generate('admin_dashboard'); ?>" class="text-gray-700 hover:text-blue-600">Dashboard Admin</a>
                    <a href="<?= $router->generate('admin_gestion_produits'); ?>" class="text-gray-700 hover:text-blue-600">Produits</a>
                    <a href="<?= $router->generate('commandes'); ?>" class="text-gray-700 hover:text-blue-600">Commandes</a>
                    <a href="<?= $router->generate('promotions'); ?>" class="text-gray-700 hover:text-blue-600">Promotions</a> <!-- Lien Promotions -->
                <?php endif; ?>
            </nav>

            <!-- User Links -->
            <div class="hidden md:flex space-x-4">
                <?php if (isset($_SESSION['id_utilisateur'])): ?>
                    <a href="<?= $router->generate('profile'); ?>" class="text-gray-700 hover:text-blue-600">Mon Profil</a>
                    <a href="<?= $router->generate('deconnexion'); ?>" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        Déconnexion
                    </a>
                <?php else: ?>
                    <a href="<?= $router->generate('connexion'); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Connexion
                    </a>
                    <a href="<?= $router->generate('inscription'); ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Inscription
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        // Menu Toggle
        document.getElementById('menu-toggle').addEventListener('click', () => {
            const navMenu = document.getElementById('nav-menu');
            navMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
