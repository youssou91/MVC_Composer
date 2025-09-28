<?php
use App\Modele\UserModel;
use App\Controlleur\UserControlleur;

// Vérifier si l'utilisateur est connecté
// session_start();
$utilisateurEstConnecte = isset($_SESSION['id_utilisateur']) && !empty($_SESSION['id_utilisateur']);

// Initialiser les données des utilisateurs
$usersData = $GLOBALS['usersData'] ?? [];

// Vérifier les autorisations
if (!$utilisateurEstConnecte || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <title>Liste des utilisateurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <?php if ($utilisateurEstConnecte && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="container mx-auto py-8 px-4">
            <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Liste des Utilisateurs</h1>
            
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            
            <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
                <table id="usersTable" class="min-w-full">
                    <thead class="bg-blue-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Utilisateur</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Commandes</th>
                            <th class="px-4 py-2 text-left">Dernière commande</th>
                            <th class="px-4 py-2 text-left">Total dépensé</th>
                            <th class="px-4 py-2 text-left">Statut</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usersData)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">Aucun utilisateur trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usersData as $index => $user): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= $index + 1; ?></td>
                                    <td class="px-4 py-2">
                                        <?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom_utilisateur'] ?? '')); ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?= htmlspecialchars($user['couriel'] ?? ''); ?>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <?= $user['nb_commandes'] ?: 'Aucune'; ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <?= $user['derniere_commande'] ? date('d/m/Y', strtotime($user['derniere_commande'])) : 'Aucune'; ?>
                                    </td>
                                    <td class="px-4 py-2 font-medium">
                                        <?= number_format($user['montant_total'], 2) ?> $
                                    </td>
                                    <td class="px-4 py-2">
                                        <?php 
                                        $status = $user['statut'] ?? 'Inactif';
                                        $statusClass = 'bg-gray-200 text-gray-800';
                                        
                                        if ($status === 'Actif') {
                                            $statusClass = 'bg-green-200 text-green-800';
                                        } elseif ($status === 'Inactif') {
                                            $statusClass = 'bg-red-200 text-red-800';
                                        } elseif ($status === 'Suspendu') {
                                            $statusClass = 'bg-yellow-200 text-yellow-800';
                                        }
                                        ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusClass; ?>">
                                            <?= htmlspecialchars($status); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            <?php if (!isset($user['role']) || $user['role'] !== 'admin'): // Ne pas afficher le bouton pour les administrateurs ?>
                                            <button 
                                                onclick="toggleUserStatus(<?= $user['id_utilisateur'] ?>, '<?= strtolower($user['statut'] ?? 'inactif') === 'actif' ? 'inactif' : 'actif' ?>', this)"
                                                class="flex items-center px-3 py-1 text-sm rounded <?= (strtolower($user['statut'] ?? 'inactif') === 'actif' ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200') ?>"
                                                title="<?= (strtolower($user['statut'] ?? 'inactif') === 'actif' ? 'Désactiver' : 'Activer') ?> l'utilisateur"
                                            >
                                                <?php if (strtolower($user['statut'] ?? 'inactif') === 'actif'): ?>
                                                    <i class="fas fa-user-slash mr-1"></i> 
                                                <?php else: ?>
                                                    <i class="fas fa-user-check mr-1"></i> 
                                                <?php endif; ?>
                                            </button>
                                            <?php endif; ?>
                                            <button onclick="showUserDetails(<?= htmlspecialchars(json_encode($user)) ?>)" 
                                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center justify-center"
                                               title="Voir le profil">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Configuration de DataTable avec les colonnes françaises
                $('#usersTable').DataTable({
                    responsive: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json',
                        search: 'Rechercher :',
                        lengthMenu: 'Afficher _MENU_ utilisateurs par page',
                        zeroRecords: 'Aucun utilisateur trouvé',
                        info: 'Affichage de _START_ à _END_ sur _TOTAL_ utilisateurs',
                        infoEmpty: 'Aucun utilisateur disponible',
                        infoFiltered: '(filtré à partir de _MAX_ utilisateurs au total)',
                        paginate: {
                            first: 'Premier',
                            last: 'Dernier',
                            next: 'Suivant',
                            previous: 'Précédent'
                        }
                    },
                    order: [[1, 'asc']], // Trier par nom par défaut
                    columnDefs: [
                        { orderable: false, targets: [0, 7] }, // Désactiver le tri sur les colonnes # et Actions
                        { type: 'date-eu', targets: 4 } // Type de tri pour la date
                    ],
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100]
                });
            });
        </script>

        <!-- Modale pour afficher les détails de l'utilisateur -->
        <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800" id="userModalTitle">Détails de l'utilisateur</h3>
                        <button onclick="closeModal('userModal')" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                    <div id="userModalContent">
                        <!-- Le contenu sera chargé dynamiquement ici -->
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Fonction pour afficher les commandes d'un utilisateur
            function showUserOrders(userId) {
                // Mettre à jour le titre de la modale
                document.getElementById('userModalTitle').textContent = 'Commandes de l\'utilisateur';
                
                // Afficher un indicateur de chargement
                document.getElementById('userModalContent').innerHTML = `
                    <div class="flex justify-center items-center p-10">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                        <span class="ml-3 text-gray-700">Chargement des commandes...</span>
                    </div>`;
                
                // Afficher la modale
                document.getElementById('userModal').classList.remove('hidden');
                
                // Désactiver temporairement les extensions comme MetaMask
                const originalEthereum = window.ethereum;
                Object.defineProperty(window, 'ethereum', { value: undefined });
                
                // URL de l'API
                const apiUrl = `/api/utilisateur/${userId}/commandes`;
                console.log('Tentative de récupération des commandes depuis:', apiUrl);
                
                // Récupérer les commandes via AJAX avec gestion d'erreur améliorée
                fetch(apiUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                })
                .then(async response => {
                    console.log('Réponse reçue, statut:', response.status);
                    const responseText = await response.text();
                    
                    // Essayer de parser le JSON
                    let responseData;
                    try {
                        responseData = responseText ? JSON.parse(responseText) : null;
                    } catch (e) {
                        console.error('Erreur lors du parsing JSON:', e);
                        console.error('Réponse brute du serveur:', responseText);
                        throw new Error('Format de réponse invalide du serveur');
                    }
                    
                    // Vérifier si la réponse est une erreur
                    if (!response.ok) {
                        console.error('Erreur serveur:', response.status, response.statusText);
                        console.error('Détails:', responseData);
                        throw new Error(responseData?.error || `Erreur ${response.status}: ${response.statusText}`);
                    }
                    
                    // Vérifier que la réponse contient bien des données
                    if (!responseData || !responseData.success) {
                        throw new Error(responseData?.error || 'Réponse du serveur invalide');
                    }
                    
                    // Retourner les données de la réponse
                    return responseData.data || [];
                })
                    .then(commandes => {
                        // Restaurer l'objet ethereum
                        if (originalEthereum) {
                            Object.defineProperty(window, 'ethereum', { value: originalEthereum });
                        }
                        
                        if (!commandes || commandes.length === 0) {
                            document.getElementById('userModalContent').innerHTML = `
                                <div class="p-6 text-center">
                                    <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune commande trouvée</h3>
                                    <p class="text-gray-500">Cet utilisateur n'a pas encore passé de commande.</p>
                                </div>`;
                            return;
                        }
                        
                        // Construire le tableau des commandes
                        let html = `
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Commande</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">`;
                        
                        commandes.forEach(commande => {
                            const statusClass = {
                                'en_attente': 'bg-yellow-100 text-yellow-800',
                                'payee': 'bg-green-100 text-green-800',
                                'expediee': 'bg-blue-100 text-blue-800',
                                'livree': 'bg-purple-100 text-purple-800',
                                'annulee': 'bg-red-100 text-red-800'
                            }[commande.statut] || 'bg-gray-100 text-gray-800';
                            
                            const statusText = {
                                'en_attente': 'En attente',
                                'payee': 'Payée',
                                'expediee': 'Expédiée',
                                'livree': 'Livrée',
                                'annulee': 'Annulée'
                            }[commande.statut] || commande.statut;
                            
                            html += `
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#${commande.id_commande}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(commande.date_commande).toLocaleDateString('fr-FR')}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                            ${statusText}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        ${parseFloat(commande.prix_total).toFixed(2)} $
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="/commande/${commande.id_commande}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                    </td>
                                </tr>`;
                        });
                        
                        html += `
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-right">
                                <a href="/admin/commandes?user_id=${userId}" class="text-sm text-blue-600 hover:text-blue-800">
                                    Voir toutes les commandes <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>`;
                        
                        document.getElementById('userModalContent').innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        
                        // Restaurer l'objet ethereum en cas d'erreur
                        if (originalEthereum) {
                            Object.defineProperty(window, 'ethereum', { value: originalEthereum });
                        }
                        
                        let errorMessage = 'Une erreur est survenue lors du chargement des commandes. Veuillez réessayer.';
                        
                        // Afficher un message d'erreur plus détaillé en mode développement
                        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                            errorMessage += `<br><br><small class="text-xs text-gray-500">${error.message || error}</small>`;
                        }
                        
                        document.getElementById('userModalContent').innerHTML = `
                            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700">
                                            ${errorMessage}
                                        </p>
                                        <div class="mt-2">
                                            <button onclick="window.location.reload()" class="text-xs text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-sync-alt mr-1"></i> Rafraîchir la page
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    });
            }
            
            // Fonction pour basculer le statut d'un utilisateur (actif/inactif)
            function toggleUserStatus(userId, newStatus, buttonElement) {
                console.log('toggleUserStatus appelée avec userId:', userId, ', newStatus:', newStatus);
                
                if (!confirm(`Êtes-vous sûr de vouloir ${newStatus === 'actif' ? 'activer' : 'désactiver'} cet utilisateur ?`)) {
                    console.log('Opération annulée par l\'utilisateur');
                    return;
                }
                
                // Désactiver le bouton pendant la requête
                if (buttonElement) {
                    buttonElement.disabled = true;
                    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Chargement...';
                }

                const url = `/api/user/${userId}/status/${newStatus}`;
                console.log('Envoi de la requête à:', url);
                
                // Récupérer le jeton CSRF du meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                })
                .then(async response => {
                    console.log('Réponse reçue, statut:', response.status);
                    
                    // Essayer de parser la réponse JSON
                    let data;
                    try {
                        const text = await response.text();
                        console.log('Réponse brute:', text);
                        data = text ? JSON.parse(text) : {};
                        console.log('Données de la réponse:', data);
                    } catch (e) {
                        console.error('Erreur lors du parsing de la réponse JSON:', e);
                        throw new Error('Réponse invalide du serveur');
                    }
                    
                    // Réactiver le bouton
                    if (buttonElement) {
                        buttonElement.disabled = false;
                        // Mettre à jour l'icône en fonction du nouveau statut
                        const icon = data.est_actif 
                            ? '<i class="fas fa-user-check"></i>'
                            : '<i class="fas fa-user-slash"></i>';
                        buttonElement.innerHTML = icon;
                        // Mettre à jour l'événement onclick
                        buttonElement.onclick = () => toggleUserStatus(
                            userId, 
                            data.est_actif ? 'inactif' : 'actif', 
                            buttonElement
                        );
                    }
                    
                    if (!response.ok) {
                        const errorMsg = data && data.message 
                            ? data.message 
                            : `Erreur HTTP ${response.status}: ${response.statusText}`;
                        throw new Error(errorMsg);
                    }
                    
                    if (data && data.success) {
                        // Afficher un message de succès
                        const toast = document.createElement('div');
                        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                        toast.textContent = data.message || 'Statut mis à jour avec succès';
                        document.body.appendChild(toast);
                        
                        // Recharger immédiatement la page
                        window.location.reload();
                    } else {
                        throw new Error(data && data.message || 'Une erreur inconnue est survenue');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la requête:', error);
                    
                    // Afficher un message d'erreur
                    const toast = document.createElement('div');
                    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg';
                    toast.textContent = error.message || 'Une erreur est survenue lors de la mise à jour du statut';
                    document.body.appendChild(toast);
                    
                    // Supprimer le message après 5 secondes
                    setTimeout(() => toast.remove(), 5000);
                    
                    // Réactiver le bouton en cas d'erreur
                    if (buttonElement) {
                        buttonElement.disabled = false;
                        const icon = newStatus === 'actif' 
                            ? '<i class="fas fa-user-check mr-1"></i> Activer'
                            : '<i class="fas fa-user-slash mr-1"></i> Désactiver';
                        buttonElement.innerHTML = icon;
                    }
                });
            }
            
            // Fonction pour afficher les détails d'un utilisateur
            function showUserDetails(user) {
                // Mettre à jour le titre de la modale
                document.getElementById('userModalTitle').textContent = 'Profil de ' + (user.prenom || '') + ' ' + (user.nom_utilisateur || '');
                
                // Construire le contenu HTML
                let content = `
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-gray-800">Informations personnelles</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div>
                                    <p class="text-sm text-gray-500">Nom complet</p>
                                    <p class="font-medium text-gray-900">${user.prenom || ''} ${user.nom_utilisateur || ''}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="font-medium text-gray-900">${user.couriel || 'Non renseigné'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Téléphone</p>
                                    <p class="font-medium text-gray-900">${user.telephone || 'Non renseigné'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Statut du compte</p>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusClass(user.statut)}">
                                        ${user.statut || 'Inactif'}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Date d'inscription</p>
                                    <p class="font-medium text-gray-900">${user.date_inscription ? new Date(user.date_inscription).toLocaleDateString('fr-FR') : 'Non disponible'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Nombre de commandes</p>
                                    <p class="font-medium text-gray-900">${user.nb_commandes || 0}</p>
                                </div>
                            </div>
                            
                    </div>
                `;
                
                // Mettre à jour le contenu de la modale
                document.getElementById('userModalContent').innerHTML = content;
                
                // Afficher la modale
                document.getElementById('userModal').classList.remove('hidden');
            }
            
            // Fonction utilitaire pour obtenir la classe CSS du statut
            function getStatusClass(status) {
                switch(status) {
                    case 'Actif': return 'bg-green-100 text-green-800';
                    case 'Inactif': return 'bg-red-100 text-red-800';
                    case 'Suspendu': return 'bg-yellow-100 text-yellow-800';
                    default: return 'bg-gray-100 text-gray-800';
                }
            }
            
            // Fonction pour fermer une modale
            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
            }
            
            // Fermer la modale en cliquant en dehors
            window.onclick = function(event) {
                const modal = document.getElementById('userModal');
                if (event.target === modal) {
                    closeModal('userModal');
                }
            }
        </script>
    <?php else: ?>
        <?php 
        if (!$utilisateurEstConnecte) {
            header('Location: /login');
        } else {
            header('Location: /');
        }
        exit();
        ?>
    <?php endif; ?>
</body>
</html>
