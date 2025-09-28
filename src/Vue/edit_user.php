<?php include __DIR__ . '/../../static/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/utilisateur/<?= $user->id_utilisateur ?>">Profil</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modifier le profil</li>
                </ol>
            </nav>
            
            <div class="card">
                <div class="card-header">
                    <h4>Modifier le profil</h4>
                </div>
                <div class="card-body">
                    <form action="/user/edit/<?= $user->id_utilisateur ?>" method="post" id="editUserForm">
                        <input type="hidden" name="id" value="<?= $user->id_utilisateur ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" 
                                   value="<?= htmlspecialchars($user->prenom ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                   value="<?= htmlspecialchars($user->nom ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user->couriel ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Rôle</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="client" <?= ($user->role ?? '') === 'client' ? 'selected' : '' ?>>Client</option>
                                <option value="admin" <?= ($user->role ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="changePassword" name="change_password">
                                <label class="form-check-label" for="changePassword">Changer le mot de passe</label>
                            </div>
                        </div>
                        
                        <div id="passwordFields" style="display: none;">
                            <div class="mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="form-text">Laissez vide pour ne pas modifier le mot de passe.</div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/utilisateur/<?= $user->id_utilisateur ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('changePassword').addEventListener('change', function() {
    const passwordFields = document.getElementById('passwordFields');
    passwordFields.style.display = this.checked ? 'block' : 'none';
    
    // Clear password fields when hiding
    if (!this.checked) {
        document.getElementById('password').value = '';
        document.getElementById('confirm_password').value = '';
    }
});

document.getElementById('editUserForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
    }
});
</script>

<?php include __DIR__ . '/../../static/footer.php'; ?>
