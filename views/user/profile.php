<!-- views/user/profile.php -->
<div class="profile-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mon profil</h1>
        <a href="<?php echo $this->url(''); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>
    </div>

    <?php if (isset($errors) && $errors): ?>
        <div class="alert alert-danger">
            <h5 class="alert-heading">Erreurs de validation</h5>
            <ul class="mb-0">
                <?php foreach ($errors as $field => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <li><?php echo ucfirst($field); ?>: <?php echo $error; ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (isset($user['profilePicture']) && !empty($user['profilePicture'])): ?>
                        <img src="<?php echo $this->escape('uploads/profile_pictures/' . $user['profilePicture']); ?>" alt="Profile Picture" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                            <span class="display-4 text-secondary"><?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?></span>
                        </div>
                    <?php endif; ?>

                    <h4><?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?></h4>
                    <p class="text-muted"><?php echo $this->escape($user['email']); ?></p>

                    <div class="d-grid gap-2 mt-3">
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#changePictureModal">
                            <i class="fas fa-camera"></i> Changer la photo
                        </button>
                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key"></i> Changer le mot de passe
                        </button>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="small text-muted">
                        <div><strong>Inscrit depuis:</strong> <?php echo $this->formatDate($user['dateInscription'], 'd/m/Y'); ?></div>
                        <?php if (isset($user['derniereConnexion']) && !empty($user['derniereConnexion'])): ?>
                            <div><strong>Dernière connexion:</strong> <?php echo $this->formatDate($user['derniereConnexion']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- User Roles Card -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Rôles</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php if ($auth->hasRole('admin')): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Administrateur
                                <span class="badge bg-danger rounded-pill"><i class="fas fa-shield-alt"></i></span>
                            </li>
                        <?php endif; ?>

                        <?php if ($auth->hasRole('chercheur')): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Chercheur
                                <span class="badge bg-primary rounded-pill"><i class="fas fa-flask"></i></span>
                            </li>
                        <?php endif; ?>

                        <?php if ($auth->hasRole('membreBureauExecutif')): ?>
                            <?php
                            $roleBadgeClass = 'bg-secondary';
                            $roleIcon = 'fas fa-user-tie';
                            if ($userType === 'membreBureauExecutif' && !empty($userDetails['role'])) {
                                switch($userDetails['role']) {
                                    case 'President':
                                        $roleIcon = 'fas fa-star';
                                        $roleBadgeClass = 'bg-warning text-dark';
                                        break;
                                    case 'VicePresident':
                                        $roleIcon = 'fas fa-star-half-alt';
                                        $roleBadgeClass = 'bg-warning text-dark';
                                        break;
                                    case 'GeneralSecretary':
                                        $roleIcon = 'fas fa-pen';
                                        break;
                                    case 'Treasurer':
                                        $roleIcon = 'fas fa-money-bill-wave';
                                        $roleBadgeClass = 'bg-success';
                                        break;
                                    case 'ViceTreasurer':
                                        $roleIcon = 'fas fa-coins';
                                        $roleBadgeClass = 'bg-success';
                                        break;
                                }
                            }
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Membre du Bureau Exécutif
                                <span class="badge <?php echo $roleBadgeClass; ?> rounded-pill"><i class="<?php echo $roleIcon; ?>"></i></span>
                            </li>
                            <?php if ($userType === 'membreBureauExecutif' && !empty($userDetails['role'])): ?>
                                <li class="list-group-item small text-muted ps-4">
                                    <?php
                                    $roleTitle = '';
                                    switch($userDetails['role']) {
                                        case 'President':
                                            $roleTitle = 'Président';
                                            break;
                                        case 'VicePresident':
                                            $roleTitle = 'Vice-président';
                                            break;
                                        case 'GeneralSecretary':
                                            $roleTitle = 'Secrétaire Général';
                                            break;
                                        case 'Treasurer':
                                            $roleTitle = 'Trésorier';
                                            break;
                                        case 'ViceTreasurer':
                                            $roleTitle = 'Vice-trésorier';
                                            break;
                                        case 'Counselor':
                                            $roleTitle = 'Conseiller';
                                            break;
                                    }
                                    echo $roleTitle;
                                    ?>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Profile Forms -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Modifier mon profil</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $this->url('profile'); ?>" method="post">
                        <?php echo CSRF::tokenField(); ?>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo $this->escape($user['nom']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required value="<?php echo $this->escape($user['prenom']); ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?php echo $this->escape($user['email']); ?>">
                            <div class="form-text">La modification de l'email nécessitera une nouvelle connexion.</div>
                        </div>

                        <?php if ($userType === 'chercheur' || ($userType === 'membreBureauExecutif' && !empty($userDetails['domaineRecherche']))): ?>
                            <hr class="my-4">
                            <h5>Informations de recherche</h5>

                            <div class="mb-3">
                                <label for="domaine_recherche" class="form-label">Domaine de recherche</label>
                                <input type="text" class="form-control" id="domaine_recherche" name="domaine_recherche"
                                       value="<?php echo $this->escape($userDetails['domaineRecherche'] ?? ''); ?>">
                            </div>

                            <div class="mb-4">
                                <label for="bio" class="form-label">Biographie / Intérêts de recherche</label>
                                <textarea class="form-control" id="bio" name="bio" rows="5"><?php echo $this->escape($userDetails['bio'] ?? ''); ?></textarea>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Profile Picture Modal -->
<div class="modal fade" id="changePictureModal" tabindex="-1" aria-labelledby="changePictureModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $this->url('profile'); ?>" method="post" enctype="multipart/form-data">
                <?php echo CSRF::tokenField(); ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="changePictureModalLabel">Changer ma photo de profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Sélectionner une nouvelle photo</label>
                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif">
                        <div class="form-text">Formats acceptés: JPG, PNG, GIF. Taille max: 5 Mo.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $this->url('profile'); ?>" method="post">
                <?php echo CSRF::tokenField(); ?>

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="changePasswordModalLabel">Changer mon mot de passe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirm" class="form-label">Confirmer le nouveau mot de passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                </div>
            </form>
        </div>
    </div>
</div>