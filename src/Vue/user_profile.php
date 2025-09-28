<?php include __DIR__ . '/../../static/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>Profil de <?= htmlspecialchars(($user->prenom ?? '') . ' ' . ($user->nom ?? '')) ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-user-circle fa-5x"></i>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="/utilisateur/<?= $user->id_utilisateur ?>/modifier" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Modifier le profil
                                </a>
                                <a href="/utilisateur/<?= $user->id_utilisateur ?>/commandes" class="btn btn-outline-primary">
                                    <i class="fas fa-shopping-cart"></i> Voir les commandes
                                </a>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-striped">
                                <tr>
                                    <th>Nom complet</th>
                                    <td><?= htmlspecialchars(($user->prenom ?? '') . ' ' . ($user->nom ?? '')) ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?= htmlspecialchars($user->couriel ?? '') ?></td>
                                </tr>
                                <tr>
                                    <th>Rôle</th>
                                    <td><?= htmlspecialchars($user->role ?? 'client') ?></td>
                                </tr>
                                <tr>
                                    <th>Date d'inscription</th>
                                    <td><?= !empty($user->date_inscription) ? date('d/m/Y', strtotime($user->date_inscription)) : 'Non spécifiée' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../static/footer.php'; ?>
