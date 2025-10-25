<?php
use App\Modele\CommandeModel;
use App\Controlleur\CommandeControlleur;

$pdo = getConnection(); 
$commandeModel = new CommandeModel($pdo);
$commandeController = new CommandeControlleur($commandeModel);
$commandeController->index();
$utilisateurEstConnecte = isset($_SESSION['id_utilisateur']) && !empty($_SESSION['id_utilisateur']);

$orders = $commandeController->listCommandes();
$index = 1;
?>

<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Liste des commandes</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="bg-gray-50 font-sans">
        <?php if ($utilisateurEstConnecte): 
            $utilisateurId = $_SESSION['id_utilisateur']; ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div class="container mx-auto py-8 px-4">
                    <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Liste des commandes</h1>
                    <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
                        <table id="ordersTable" class="min-w-full">
                            <thead class="bg-blue-100 text-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left">#</th>
                                    <th class="px-4 py-2 text-left">Utilisateur</th>
                                    <th class="px-4 py-2 text-left">Date</th>
                                    <th class="px-4 py-2 text-left">Prix total</th>
                                    <th class="px-4 py-2 text-left">Statut</th>
                                    <th class="px-4 py-2 text-left">Actions</th>
                            </thead>
                            <tbody>
                            <?php foreach ($orders as $index => $order): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2"><?=  $index + 1; ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($order['prenom'] . ' ' . $order['nom_utilisateur']); ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($order['date_commande']); ?></td>
                                    <td class="px-4 py-2"><?= number_format(htmlspecialchars($order['prix_total']), 2); ?> $</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 rounded-full 
                                            <?php 
                                                // Application des couleurs en fonction du statut
                                                $statut = $order['statut'];
                                                if ($statut === 'En attente' || $statut === 'en_attente') {
                                                    echo 'bg-yellow-200 text-yellow-800'; 
                                                } elseif ($statut === 'En traitement' || $statut === 'en_traitement') {
                                                    echo 'bg-orange-200 text-orange-800'; 
                                                } elseif (in_array($statut, ['En expédition', 'en_expedition', 'En expÃ©dition'])) {
                                                    echo 'bg-blue-200 text-blue-800'; 
                                                } elseif (in_array($statut, ['Livrée', 'livree', 'LivrÃ©e'])) {
                                                    echo 'bg-green-200 text-green-800'; 
                                                } elseif (in_array($statut, ['Annulée', 'annulee', 'AnnulÃ©e'])) {
                                                    echo 'bg-red-200 text-red-800'; 
                                                } elseif (in_array($statut, ['Payée', 'payee', 'PayÃ©e'])) {
                                                    echo 'bg-purple-200 text-purple-800'; 
                                                }
                                            ?>">
                                            <?= htmlspecialchars($order['statut']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex space-x-2">
                                            <!-- Bouton pour traiter la commande -->
                                            <?php 
                                                $statut = strtolower(trim($order['statut']));
                                                $peutTraiter = ($statut === 'en attente' || $statut === 'en_attente');
                                            ?>
                                            <form method="POST" action="/commande/<?= $order['id_commande']; ?>/modifier/traiter" class="inline">
                                                <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                                                <button type="submit" 
                                                    class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 flex items-center justify-center transition-all duration-200" 
                                                    <?= !$peutTraiter ? 'disabled style="opacity: 0.6; cursor: not-allowed;"' : ''; ?>
                                                    title="Traiter la commande">
                                                    <i class="bi bi-gear-fill"></i>
                                                </button>
                                            </form>

                                            <!-- Bouton pour expédier la commande -->
                                            <?php 
                                                $peutExpedier = ($statut === 'en traitement' || $statut === 'en_traitement');
                                            ?>
                                            <form method="POST" action="/commande/<?= $order['id_commande']; ?>/modifier/expedier" class="inline">
                                                <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                                                <button type="submit" 
                                                    class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 flex items-center justify-center transition-all duration-200"
                                                    <?= !$peutExpedier ? 'disabled style="opacity: 0.6; cursor: not-allowed;"' : ''; ?>
                                                    title="Expédier la commande">
                                                    <i class="bi bi-truck"></i>
                                                </button>
                                            </form>

                                            <!-- Bouton pour marquer comme livrée -->
                                            <?php 
                                                $peutLivrer = ($statut === 'en expédition' || $statut === 'en_expedition' || $statut === 'en expedition');
                                            ?>
                                            <form method="POST" action="/commande/<?= $order['id_commande']; ?>/modifier/livrer" class="inline">
                                                <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                                                <button type="submit" 
                                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center justify-center transition-all duration-200"
                                                    <?= !$peutLivrer ? 'disabled style="opacity: 0.6; cursor: not-allowed;"' : ''; ?>
                                                    title="Marquer comme livrée">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </button>
                                            </form>

                                            <!-- Bouton pour annuler la commande -->
                                            <?php 
                                                $statut = strtolower($order['statut']);
                                                $statutsNonAnnulables = ['livrée', 'livree', 'payée', 'payee', 'annulée', 'annulee'];
                                                $estAnnulable = true;
                                                foreach ($statutsNonAnnulables as $s) {
                                                    if (strpos($statut, $s) !== false) {
                                                        $estAnnulable = false;
                                                        break;
                                                    }
                                                }
                                            ?>
                                            <button type="button" 
                                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 flex items-center justify-center transition-all duration-200"
                                                <?= !$estAnnulable ? 'disabled style="opacity: 0.6; cursor: not-allowed;"' : ''; ?>
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalAnnulerCommande<?= $order['id_commande']; ?>"
                                                title="Annuler la commande"
                                                <?= !$estAnnulable ? 'aria-disabled="true"' : ''; ?>>
                                                <i class="bi bi-x-circle-fill"></i>
                                                <span class="ml-1 hidden md:inline"></span>
                                            </button>

                                            <!-- Bouton pour voir les détails -->
                                            <button type="button" 
                                                class="px-3 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 flex items-center justify-center transition-all duration-200"
                                                onclick="afficherDetailsCommande(<?= $order['id_commande']; ?>)"
                                                title="Voir les détails">
                                                <i class="bi bi-eye-fill"></i>
                                                <span class="ml-1 hidden md:inline"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Modal de confirmation pour l'annulation -->
                                <div class="modal fade" id="modalAnnulerCommande<?= $order['id_commande']; ?>" tabindex="-1" aria-labelledby="modalAnnulerCommandeLabel<?= $order['id_commande']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-red-50 border-b border-red-100">
                                                <h5 class="modal-title text-red-800 font-semibold" id="modalAnnulerCommandeLabel<?= $order['id_commande']; ?>">
                                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmer l'annulation
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                            </div>
                                            <div class="modal-body py-4 px-4">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0 mt-1">
                                                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                                            <i class="bi bi-exclamation-lg text-red-600"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <h3 class="text-lg font-medium text-gray-900">Annuler la commande #<?= $order['id_commande']; ?></h3>
                                                        <div class="mt-2 text-sm text-gray-600">
                                                            <p>ÃŠtes-vous sÃ»r de vouloir annuler cette commande ? Cette action est irrÃ©versible.</p>
                                                            <?php if ($order['statut'] === 'En expÃ©dition' || $order['statut'] === 'En cours de livraison'): ?>
                                                                <div class="mt-3 p-3 bg-yellow-50 text-yellow-700 rounded-md text-sm">
                                                                    <i class="bi bi-info-circle-fill me-1"></i> Cette commande est dÃ©jÃ  en cours de prÃ©paration. Veuillez contacter le service client si nÃ©cessaire.
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                                                <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors duration-200 flex items-center" data-bs-dismiss="modal">
                                                    <i class="bi bi-x-lg mr-1"></i> Annuler
                                                </button>
                                                <form method="POST" action="/commande/<?= $order['id_commande']; ?>/modifier/annuler" class="inline">
                                                    <input type="hidden" name="_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">
                                                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200 flex items-center">
                                                        <i class="bi bi-x-circle-fill mr-1"></i> Confirmer l'annulation
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: 
            header('Location: /'); 
        endif; ?>   
        <?php else: 
            header('Location: /login'); 
        endif; ?>                                   
        <!-- Modal pour les dÃ©tails de la commande -->
        <!-- Modal pour les dÃ©tails de la commande -->
        <div class="modal fade" id="detailsCommandeModal" tabindex="-1" aria-labelledby="detailsCommandeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-blue-50 border-b border-blue-100">
                        <h5 class="modal-title text-blue-800 font-semibold" id="detailsCommandeModalLabel">
                            <i class="bi bi-receipt me-2"></i>DÃ©tails de la commande #<span id="modalCommandeId"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="p-6">
                            <!-- En-tÃªte de la commande -->
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                                <div>
                                    <p class="text-gray-600" id="modalCommandeDate"></p>
                                </div>
                                <div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium" id="modalCommandeStatut"></span>
                                </div>
                            </div>

                            <!-- Informations client et livraison -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Informations client</h3>
                                    <div class="space-y-2" id="modalClientInfo">
                                        <!-- Rempli par JavaScript -->
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Adresse de livraison</h3>
                                    <div class="space-y-1" id="modalAdresseLivraison">
                                        <!-- Rempli par JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <!-- DÃ©tails des articles -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-3">DÃ©tails de la commande</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="py-2 px-4 text-left">Produit</th>
                                                <th class="py-2 px-4 text-center">Prix unitaire</th>
                                                <th class="py-2 px-4 text-center">QuantitÃ©</th>
                                                <th class="py-2 px-4 text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200" id="modalArticlesListe">
                                            <!-- Rempli par JavaScript -->
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="3" class="py-3 px-4 text-right font-semibold">Sous-total :</td>
                                                <td class="py-3 px-4 text-right" id="modalSousTotal"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="py-3 px-4 text-right font-semibold">Frais de livraison :</td>
                                                <td class="py-3 px-4 text-right" id="modalFraisLivraison"></td>
                                            </tr>
                                            <tr class="border-t border-gray-200">
                                                <td colspan="3" class="py-3 px-4 text-right font-bold text-lg">Total :</td>
                                                <td class="py-3 px-4 text-right font-bold text-lg text-blue-600" id="modalTotal"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <i class="bi bi-info-circle mr-1"></i> Pour toute question, contactez notre service client
                        </div>
                        <div class="space-x-2">
                            <button type="button" class="px-4 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-100 transition-colors duration-200 flex items-center" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg mr-1"></i> Fermer
                            </button>
                            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200 flex items-center" id="btnImprimerCommande">
                                <i class="bi bi-printer-fill mr-1"></i> Imprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function () {
                // Initialisation de DataTables
                $('#ordersTable').DataTable({
                    responsive: true, // Rend la table responsive
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                    }
                });

                // Initialisation des tooltips Bootstrap
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });

            // Fonction pour afficher les détails d'une commande
            function afficherDetailsCommande(idCommande) {
                // Afficher un indicateur de chargement
                const modalBody = document.querySelector('#detailsCommandeModal .modal-body');
                modalBody.innerHTML = `
                    <div class="flex flex-col items-center justify-center p-8 text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mb-4"></div>
                        <p class="text-gray-600">Chargement des détails de la commande...</p>
                    </div>
                `;
                
                // Afficher le modal
                const modal = new bootstrap.Modal(document.getElementById('detailsCommandeModal'));
                modal.show();
                
                // Mettre à jour le numéro de commande immédiatement
                document.getElementById('modalCommandeId').textContent = idCommande;
                
                // Récupérer les détails de la commande via AJAX
                fetch(`/commande/${idCommande}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            let error = 'Erreur inconnue';
                            try {
                                const data = JSON.parse(text);
                                error = data.message || 'Erreur lors de la récupération des détails de la commande';
                            } catch (e) {
                                error = text || 'Erreur lors de la récupération des détails de la commande';
                            }
                            throw new Error(error);
                        });
                    }
                    return response.json();
                })
                .then(commande => {
                    // Mettre Ã  jour les informations de la commande
                    document.getElementById('modalCommandeId').textContent = commande.id_commande;
                    // Mettre à jour les informations de base
                    document.getElementById('modalCommandeDate').textContent = `Passée le ${new Date(commande.commande.date_commande).toLocaleString('fr-FR', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}`;
                    
                    // Mettre Ã  jour le statut avec la bonne classe CSS
                    // Mettre à jour le statut
                    const statutElement = document.getElementById('modalCommandeStatut');
                    const statutClass = getStatutClass(commande.commande.statut);
                    statutElement.innerHTML = `
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statutClass}">
                            <span class="w-2 h-2 rounded-full ${statutClass.includes('bg-') ? statutClass : 'bg-gray-400'} mr-2"></span>
                            ${commande.commande.statut}
                        </span>
                    `;
                    
                    // Mettre à jour les informations client
                    const clientInfo = `
                        <p class="font-medium">${escapeHtml(commande.commande.client.nom_complet || 'Non spécifié')}</p>
                        <p class="text-gray-600">${escapeHtml(commande.commande.client.email || '')}</p>
                        <p class="text-gray-600">${escapeHtml(commande.commande.client.telephone || '') || 'Non spécifié'}</p>
                    `;
                    document.getElementById('modalClientInfo').innerHTML = clientInfo;
                    
                    // Mettre à jour l'adresse de livraison
                    const adresseHtml = `
                        <p class="font-medium">${escapeHtml(commande.commande.adresse_livraison.ligne1 || 'Adresse non spécifiée')}</p>
                        <p>${escapeHtml(commande.commande.adresse_livraison.code_postal || '')} ${escapeHtml(commande.commande.adresse_livraison.ville || '')}</p>
                        <p>${escapeHtml(commande.commande.adresse_livraison.pays || '')}</p>
                    `;
                    document.getElementById('modalAdresseLivraison').innerHTML = adresseHtml;
                    
                    // Articles de la commande
                    let articlesHtml = '';
                    let sousTotal = 0;
                    
                    // Mettre à jour les articles de la commande
                    if (commande.commande.articles && commande.commande.articles.length > 0) {
                        commande.commande.articles.forEach(article => {
                            const prixUnitaire = parseFloat(article.prix_unitaire || 0);
                            const quantite = parseInt(article.quantite || 1);
                            const totalLigne = prixUnitaire * quantite;
                            sousTotal += totalLigne;
                            
                            articlesHtml += `
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center">
                                            ${article.image_url ? `<img src="${escapeHtml(article.image_url)}" alt="${escapeHtml(article.nom || '')}" class="w-16 h-16 object-cover rounded mr-4">` : ''}
                                            <div>
                                                <p class="font-medium text-gray-800">${escapeHtml(article.nom || 'Produit sans nom')}</p>
                                                ${article.reference ? `<p class="text-sm text-gray-500">RÃ©f. ${escapeHtml(article.reference)}</p>` : ''}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">${prixUnitaire.toFixed(2).replace('.', ',')} â‚¬</td>
                                </tr>
                            `;
                        });
                    } else {
                        articlesHtml = '<tr><td colspan="4" class="py-4 text-center text-gray-500">Aucun article trouvé pour cette commande</td></tr>';
                    }
                    
                    document.getElementById('modalArticlesListe').innerHTML = articlesHtml;
                    
                    // Mettre à jour les totaux
                    const fraisLivraison = parseFloat(commande.commande.frais_livraison || 0);
                    const total = parseFloat(commande.commande.total || (sousTotal + fraisLivraison));
                    
                    // Formater les montants
                    const formatMontant = (montant) => {
                        return montant.toFixed(2).replace('.', ',') + ' €';
                    };
                    
                    // Mettre à jour l'affichage des totaux
                    document.getElementById('modalSousTotal').textContent = formatMontant(sousTotal);
                    document.getElementById('modalFraisLivraison').textContent = 
                        fraisLivraison > 0 ? formatMontant(fraisLivraison) : 'Gratuit';
                    document.getElementById('modalTotal').textContent = formatMontant(total);
                    
                    // Gestionnaire d'événement pour le bouton d'impression
                    document.getElementById('btnImprimerCommande').onclick = () => {
                        window.print();
                    };
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    modalBody.innerHTML = `
                        <div class="p-8 text-center">
                            <div class="text-red-500 text-4xl mb-4">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Erreur lors du chargement des détails</h3>
                            <p class="text-gray-600">${error.message || 'Une erreur est survenue lors de la récupération des détails de la commande.'}</p>
                            <button type="button" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600" onclick="afficherDetailsCommande(${idCommande})">
                                <i class="bi bi-arrow-clockwise mr-1"></i> Réessayer
                            </button>
                        </div>
                    `;
                });
            }

            // Fonction utilitaire pour échapper le HTML
            function escapeHtml(unsafe) {
                if (typeof unsafe !== 'string') return unsafe;
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Fonction pour obtenir la classe CSS en fonction du statut
            function getStatutClass(statut) {
                const statutLower = String(statut || '').toLowerCase();
                
                switch(statutLower) {
                    case 'en attente':
                        return 'bg-yellow-100 text-yellow-800';
                    case 'en traitement':
                        return 'bg-orange-100 text-orange-800';
                    case 'en expédition':
                    case 'en expedition':
                        return 'bg-blue-100 text-blue-800';
                    case 'livrée':
                    case 'livree':
                        return 'bg-green-100 text-green-800';
                    case 'annulée':
                    case 'annulee':
                        return 'bg-red-100 text-red-800';
                    case 'payée':
                    case 'payee':
                        return 'bg-purple-100 text-purple-800';
                    default:
                        return 'bg-gray-100 text-gray-800';
                }
                if (statutLower.includes('en attente')) return 'bg-yellow-100 text-yellow-800';
                if (statutLower.includes('traitement')) return 'bg-blue-100 text-blue-800';
                if (statutLower.includes('expÃ©dition') || statutLower.includes('expedition')) return 'bg-indigo-100 text-indigo-800';
                if (statutLower.includes('livrÃ©') || statutLower.includes('livre')) return 'bg-green-100 text-green-800';
                if (statutLower.includes('annulÃ©') || statutLower.includes('annule')) return 'bg-red-100 text-red-800';
                if (statutLower.includes('payÃ©') || statutLower.includes('paye')) return 'bg-purple-100 text-purple-800';
                return 'bg-gray-100 text-gray-800';
            }
        </script>
    </body>
</html>



