<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Responsive</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-10">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 text-center md:text-left">
                <!-- About Us -->
                <div>
                    <h4 class="text-xl font-semibold mb-4">À Propos</h4>
                    <p class="text-gray-400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in ultricies nunc. Nulla facilisi.</p>
                </div>

                <!-- Contact Us -->
                <div>
                    <h4 class="text-xl font-semibold mb-4">Contactez-nous</h4>
                    <ul class="space-y-2">
                        <li><a href="mailto:123@example.com" class="text-gray-400 hover:text-white">Email: 123@example.com</a></li>
                        <li><a href="tel:+1234567890" class="text-gray-400 hover:text-white">Téléphone: 123-456-7890</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white">Adresse: 123 Main St, City, State, Zip</a></li>
                    </ul>
                </div>

                <!-- Follow Us -->
                <div>
                    <h4 class="text-xl font-semibold mb-4">Suivez-nous</h4>
                    <ul class="flex justify-center space-x-6">
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="fab fa-facebook-f w-8 h-8"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="fab fa-twitter w-8 h-8"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="fab fa-linkedin-in w-8 h-8"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="fab fa-instagram w-8 h-8"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="fab fa-youtube w-8 h-8"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i class="fab fa-github w-8 h-8"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center mt-8 text-gray-500 text-sm">
                &copy; 2024 Votre Entreprise. Tous droits réservés.
            </div>
        </div>
    </footer>

</body>

</html>
