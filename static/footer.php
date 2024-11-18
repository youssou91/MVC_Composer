<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Responsive</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- About Us -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">À Propos</h4>
                    <p class="text-gray-400">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in ultricies nunc. Nulla facilisi.
                    </p>
                </div>
                <!-- Contact Us -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contactez-nous</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white">Email : 123@example.com</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Téléphone : 123-456-7890</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Adresse : 123 Main St, City, State, Zip</a></li>
                    </ul>
                </div>
                <!-- Follow Us -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Suivez-nous</h4>
                    <ul class="flex space-x-4">
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M22 12.07a10 10 0 1 0-11.44 9.91v-7H7v-3h3.56V9.5a5 5 0 0 1 5.15-5.5h3.14v3h-2a1.44 1.44 0 0 0-1.61 1.56v1.94H18l-.56 3h-2.94v7A10 10 0 0 0 22 12.07z"></path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M8.29 20.25c7.55 0 11.68-6.26 11.68-11.68 0-.18 0-.35-.01-.53A8.36 8.36 0 0 0 22 5.92a8.19 8.19 0 0 1-2.35.64 4.1 4.1 0 0 0 1.8-2.26 8.2 8.2 0 0 1-2.6.99 4.1 4.1 0 0 0-7 3.74 11.65 11.65 0 0 1-8.46-4.29 4.07 4.07 0 0 0 1.27 5.47A4.08 4.08 0 0 1 2.8 9.71v.05a4.1 4.1 0 0 0 3.29 4.02 4.09 4.09 0 0 1-1.85.07 4.1 4.1 0 0 0 3.83 2.85A8.23 8.23 0 0 1 2 18.41a11.62 11.62 0 0 0 6.29 1.84"></path>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <svg class="w-6 h-6 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path d="M12 2.04c-5.5 0-10 4.48-10 10a10 10 0 0 0 13.56 9.6v-6.8h-3.42V12h3.42V9.67c0-3.41 2-5.28 5.08-5.28 1.48 0 2.92.26 2.92.26v3.23h-1.64c-1.62 0-2.12 1.02-2.12 2.06v2.34h3.56l-.57 3.44h-2.99v6.8A10 10 0 0 0 22 12c0-5.52-4.5-9.96-10-9.96z"></path>
                                </svg>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-8 text-gray-500 text-sm">
                &copy; 2024 Votre Entreprise. Tous droits réservés.
            </div>
        </div>
    </footer>
</body>
</html>
