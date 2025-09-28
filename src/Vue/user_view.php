<?php
// Inclure l'en-tête
require __DIR__ . '/../static/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <!-- En-tête de la carte -->
        <div class="bg-blue-600 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Détails de l'utilisateur</h1>
        </div>
        
        <!-- Corps de la carte -->
        <div class="p-6">
            <!-- Informations principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations personnelles</h2>
                    <div class="space-y-2">
                        <p><span class="font-medium text-gray-700">Nom d'utilisateur :</span> <?= htmlspecialchars($user['nom_utilisateur'] ?? 'Non spécifié') ?></p>
                        <p><span class="font-medium text-gray-700">Prénom :</span> <?= htmlspecialchars($user['prenom'] ?? 'Non spécifié') ?></p>
                        <p><span class="font-medium text-gray-700">Nom :</span> <?= htmlspecialchars($user['nom'] ?? 'Non spécifié') ?></p>
                        <p><span class="font-medium text-gray-700">Email :</span> <?= htmlspecialchars($user['couriel'] ?? 'Non spécifié') ?></p>
                        <p><span class="font-medium text-gray-700">Téléphone :</span> <?= htmlspecialchars($user['telephone'] ?? 'Non spécifié') ?></p>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Informations du compte</h2>
                    <div class="space-y-2">
                        <p>
                            <span class="font-medium text-gray-700">Rôle :</span> 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= ($user['role'] ?? 'user') === 'admin' ? 'purple' : 'blue' ?>-100 text-<?= ($user['role'] ?? 'user') === 'admin' ? 'purple' : 'blue' ?>-800">
                                <?= ($user['role'] ?? 'user') === 'admin' ? 'Administrateur' : 'Utilisateur' ?>
                            </span>
                        </p>
                        <p>
                            <span class="font-medium text-gray-700">Statut :</span> 
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= ($user['actif'] ?? 0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ($user['actif'] ?? 0) ? 'Actif' : 'Inactif' ?>
                            </span>
                        </p>
                        <p><span class="font-medium text-gray-700">Date de création :</span> 
                            <?= isset($user['date_creation']) ? date('d/m/Y H:i', strtotime($user['date_creation'])) : 'Non spécifiée' ?>
                        </p>
                        <?php if (isset($user['derniere_connexion']) && !empty($user['derniere_connexion'])): ?>
                        <p><span class="font-medium text-gray-700">Dernière connexion :</span> 
                            <?= date('d/m/Y H:i', strtotime($user['derniere_connexion'])) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                <a href="/utilisateurs" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
                </a>
                <?php if (($_SESSION['role'] ?? '') === 'admin' || ($_SESSION['id_utilisateur'] ?? 0) == ($user['id_utilisateur'] ?? 0)): ?>
                <a href="/utilisateur/edit/<?= $user['id_utilisateur'] ?>" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <?php endif; ?>
                
                <?php if (($_SESSION['role'] ?? '') === 'admin' && ($_SESSION['id_utilisateur'] ?? 0) != ($user['id_utilisateur'] ?? 0)): ?>
                <button onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) { 
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/utilisateur/delete/<?= $user['id_utilisateur'] ?>';
                    
                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfInput);
                    
                    var methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition">
                    <i class="fas fa-trash-alt mr-2"></i>Supprimer
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le pied de page
require __DIR__ . '/../static/footer.php';
?>
