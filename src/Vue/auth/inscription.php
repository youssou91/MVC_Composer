<?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialisation des variables pour les messages
$successMessage = $_SESSION['success_message'] ?? '';
$error = $_SESSION['error'] ?? '';
$formData = $_SESSION['form_data'] ?? [];

// Nettoyage des variables de session
unset($_SESSION['success_message']);
unset($_SESSION['error']);
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inscription</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 min-h-screen flex items-center justify-center">

        <div class="container mx-auto max-w-3xl bg-white p-8 shadow-lg rounded-lg my-8">
            <!-- Afficher le message de succès -->
            <?php if (!empty($successMessage)): ?>
                <div class="p-4 mb-4 text-sm font-semibold bg-green-100 text-green-700 border border-green-200 rounded-lg">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            
            <!-- Afficher le message d'erreur -->
            <?php if (!empty($error)): ?>
                <div class="p-4 mb-4 text-sm font-semibold bg-red-100 text-red-700 border border-red-200 rounded-lg">
                    <?php echo nl2br(htmlspecialchars($error)); ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="/register" class="space-y-8">
                <!-- Formulaire de l'utilisateur comme dans votre code initial -->
                <!-- Informations Personnelles -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4 text-blue-500">Informations Personnelles</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                            <input type="text" id="nom" name="nom_utilisateur" class="mt-1 p-2 block w-full border-gray-300 rounded-lg <?php echo !empty($error) && empty($formData['nom_utilisateur']) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($formData['nom_utilisateur'] ?? ''); ?>" required>
                            <?php if (!empty($error) && empty($formData['nom_utilisateur'])): ?>
                                <p class="mt-1 text-sm text-red-600">Ce champ est requis</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                            <input type="text" id="prenom" name="prenom" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" value="<?php echo htmlspecialchars($formData['prenom'] ?? ''); ?>" required>
                        </div>
                        <div class="col-span-full">
                            <label for="datNaiss" class="block text-sm font-medium text-gray-700">Date de naissance</label>
                            <input type="date" id="datNaiss" name="datNaiss" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" value="<?php echo htmlspecialchars($formData['datNaiss'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                <!-- Coordonnées -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4 text-blue-500">Coordonnées</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                            <input type="number" id="telephone" name="telephone" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" value="<?php echo htmlspecialchars($formData['telephone'] ?? ''); ?>" required>
                        </div>
                        <div>
                            <label for="couriel" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="couriel" name="couriel" class="mt-1 p-2 block w-full border-gray-300 rounded-lg <?php echo !empty($error) && empty($formData['couriel']) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($formData['couriel'] ?? ''); ?>" required>
                            <?php if (!empty($error) && empty($formData['couriel'])): ?>
                                <p class="mt-1 text-sm text-red-600">Ce champ est requis</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                            <input type="password" id="password" name="password" class="mt-1 p-2 block w-full border-gray-300 rounded-lg <?php echo !empty($error) && empty($formData['password']) ? 'border-red-500' : ''; ?>" required>
                            <?php if (!empty($error) && empty($formData['password'])): ?>
                                <p class="mt-1 text-sm text-red-600">Ce champ est requis</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="cpassword" class="block text-sm font-medium text-gray-700">Confirmer Mot de passe</label>
                            <input type="password" id="cpassword" name="cpassword" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" required>
                        </div>
                    </div>
                </div>
                <!-- Adresse -->
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold mb-4 text-blue-500">Adresse</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="rue" class="block text-sm font-medium text-gray-700">Rue</label>
                            <input type="text" id="rue" name="rue" class="mt-1 p-2 block w-full border-gray-300 rounded-lg <?php echo !empty($error) && empty($formData['rue']) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($formData['rue'] ?? ''); ?>" required>
                            <?php if (!empty($error) && empty($formData['rue'])): ?>
                                <p class="mt-1 text-sm text-red-600">Ce champ est requis</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="numero" class="block text-sm font-medium text-gray-700">Numéro</label>
                            <input type="text" id="numero" name="numero" class="mt-1 p-2 block w-full border-gray-300 rounded-lg <?php echo !empty($error) && empty($formData['numero']) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($formData['numero'] ?? ''); ?>" required>
                            <?php if (!empty($error) && empty($formData['numero'])): ?>
                                <p class="mt-1 text-sm text-red-600">Ce champ est requis</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="ville" class="block text-sm font-medium text-gray-700">Ville</label>
                            <input type="text" id="ville" name="ville" class="mt-1 p-2 block w-full border-gray-300 rounded-lg <?php echo !empty($error) && empty($formData['ville']) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($formData['ville'] ?? ''); ?>" required>
                            <?php if (!empty($error) && empty($formData['ville'])): ?>
                                <p class="mt-1 text-sm text-red-600">Ce champ est requis</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="code_postal" class="block text-sm font-medium text-gray-700">Code Postal</label>
                            <input type="text" id="code_postal" name="code_postal" class="mt-1 p-2 block w-full border-gray-300 rounded-lg <?php echo !empty($error) && empty($formData['code_postal']) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($formData['code_postal'] ?? ''); ?>" required>
                            <?php if (!empty($error) && empty($formData['code_postal'])): ?>
                                <p class="mt-1 text-sm text-red-600">Ce champ est requis</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="province" class="block text-sm font-medium text-gray-700">Province</label>
                            <select id="province" name="province" class="mt-1 p-2 block w-full border-gray-300 rounded-lg <?php echo !empty($error) && empty($formData['province']) ? 'border-red-500' : ''; ?>" required>
                                <option value="">Sélectionnez une province</option>
                                <option value="QC" <?php echo (isset($formData['province']) && $formData['province'] === 'QC') ? 'selected' : ''; ?>>Québec</option>
                                <option value="ON" <?php echo (isset($formData['province']) && $formData['province'] === 'ON') ? 'selected' : ''; ?>>Ontario</option>
                                <option value="BC" <?php echo (isset($formData['province']) && $formData['province'] === 'BC') ? 'selected' : ''; ?>>Colombie-Britannique</option>
                                <option value="AB" <?php echo (isset($formData['province']) && $formData['province'] === 'AB') ? 'selected' : ''; ?>>Alberta</option>
                                <option value="MB" <?php echo (isset($formData['province']) && $formData['province'] === 'MB') ? 'selected' : ''; ?>>Manitoba</option>
                                <option value="SK" <?php echo (isset($formData['province']) && $formData['province'] === 'SK') ? 'selected' : ''; ?>>Saskatchewan</option>
                                <option value="NS" <?php echo (isset($formData['province']) && $formData['province'] === 'NS') ? 'selected' : ''; ?>>Nouvelle-Écosse</option>
                                <option value="NB" <?php echo (isset($formData['province']) && $formData['province'] === 'NB') ? 'selected' : ''; ?>>Nouveau-Brunswick</option>
                                <option value="NL" <?php echo (isset($formData['province']) && $formData['province'] === 'NL') ? 'selected' : ''; ?>>Terre-Neuve-et-Labrador</option>
                                <option value="PE" <?php echo (isset($formData['province']) && $formData['province'] === 'PE') ? 'selected' : ''; ?>>Île-du-Prince-Édouard</option>
                                <option value="NT" <?php echo (isset($formData['province']) && $formData['province'] === 'NT') ? 'selected' : ''; ?>>Territoires du Nord-Ouest</option>
                                <option value="NU" <?php echo (isset($formData['province']) && $formData['province'] === 'NU') ? 'selected' : ''; ?>>Nunavut</option>
                                <option value="YT" <?php echo (isset($formData['province']) && $formData['province'] === 'YT') ? 'selected' : ''; ?>>Yukon</option>
                            </select>
                            <?php if (!empty($error) && empty($formData['province'])): ?>
                                <p class="mt-1 text-sm text-red-600">Ce champ est requis</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="pays" class="block text-sm font-medium text-gray-700">Pays</label>
                            <input type="text" id="pays" name="pays" class="mt-1 p-2 block w-full border-gray-300 rounded-lg" value="<?php echo htmlspecialchars($formData['pays'] ?? 'Canada'); ?>" required>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <button type="submit" name="addUser" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md">Soumettre</button>
                </div>
            </form>
        </div>
    </body>
</html>
