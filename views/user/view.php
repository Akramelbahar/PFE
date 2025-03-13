<!-- views/user/view.php -->
<div class="user-profile-page">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Profil</h4>
                </div>
                <div class="card-body text-center">
                    <?php
                    $profilePicture = !empty($user['profilePicture'])
                        ? $this->escape($user['profilePicture'])
                        : 'default-avatar.png';
                    ?>
                    <img src="uploads/profile_pictures/<?php echo $profilePicture; ?>"
                         alt="Photo de profil"
                         class="rounded-circle mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <h3><?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?></h3>
                    <p class="text-muted"><?php echo $this->escape($user['email']); ?></p>

                    <div class="mt-3">
                        <?php foreach ($roles as $role): ?>
                            <span class="badge bg-secondary me-1">
                            <?php
                            switch ($role) {
                                case 'admin':
                                    echo 'Administrateur';
                                    break;
                                case 'chercheur':
                                    echo 'Chercheur';
                                    break;
                                case 'membreBureauExecutif':
                                    echo 'Membre Bureau Exécutif';
                                    break;
                                case 'president':
                                    echo 'Président';
                                    break;
                                case 'vicepresident':
                                    echo 'Vice-Président';
                                    break;
                                default:
                                    echo ucfirst($role);
                            }
                            ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <?php if ($userType === 'chercheur' && !empty($userDetails['domaineRecherche'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Domaine de Recherche</h4>
                    </div>
                    <div class="card-body">
                        <p><?php echo $this->escape($userDetails['domaineRecherche']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <?php if ($userType === 'chercheur' && !empty($userDetails['bio'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Biographie</h4>
                    </div>
                    <div class="card-body">
                        <?php echo nl2br($this->escape($userDetails['bio'])); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($userType === 'membreBureauExecutif'): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Détails du Membre du Bureau Exécutif</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Rôle:</strong>
                                <?php echo $this->escape($userDetails['role']); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Mandat:</strong>
                                <?php echo $this->escape($userDetails['mandat']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Activités Récentes</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        Les informations sur les activités récentes seront ajoutées ultérieurement.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>