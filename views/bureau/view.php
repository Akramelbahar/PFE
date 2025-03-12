<!-- views/bureau/view.php -->
<div class="bureau-member-view-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Profil de Membre du Bureau</h1>
        <a href="<?php echo $this->url('admin/bureau'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informations personnelles</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (isset($member['profilePicture']) && !empty($member['profilePicture'])): ?>
                        <img src="<?php echo $this->escape('uploads/profile_pictures/' . $member['profilePicture']); ?>" alt="Photo de profil" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                            <span class="display-4 text-secondary"><?php echo strtoupper(substr($member['prenom'], 0, 1) . substr($member['nom'], 0, 1)); ?></span>
                        </div>
                    <?php endif; ?>

                    <h4><?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?></h4>
                    <p class="text-muted"><?php echo $this->escape($member['email']); ?></p>

                    <?php
                    $roleBadgeClass = 'bg-secondary';
                    $roleIcon = 'fas fa-user-tie';
                    $roleText = $member['role'];

                    switch($member['role']) {
                        case 'President':
                            $roleText = 'Président';
                            $roleIcon = 'fas fa-star';
                            $roleBadgeClass = 'bg-warning text-dark';
                            break;
                        case 'VicePresident':
                            $roleText = 'Vice-président';
                            $roleIcon = 'fas fa-star-half-alt';
                            $roleBadgeClass = 'bg-warning text-dark';
                            break;
                        case 'GeneralSecretary':
                            $roleText = 'Secrétaire Général';
                            $roleIcon = 'fas fa-pen';
                            break;
                        case 'Treasurer':
                            $roleText = 'Trésorier';
                            $roleIcon = 'fas fa-money-bill-wave';
                            $roleBadgeClass = 'bg-success';
                            break;
                        case 'ViceTreasurer':
                            $roleText = 'Vice-trésorier';
                            $roleIcon = 'fas fa-coins';
                            $roleBadgeClass = 'bg-success';
                            break;
                        case 'Counselor':
                            $roleText = 'Conseiller';
                            break;
                    }
                    ?>
                    <div class="mb-3">
                        <span class="badge <?php echo $roleBadgeClass; ?> fs-6">
                            <i class="<?php echo $roleIcon; ?> me-1"></i>
                            <?php echo $roleText; ?>
                        </span>
                    </div>

                    <?php if (isset($member['Mandat']) && !empty($member['Mandat'])): ?>
                        <div class="mt-3">
                            <h6>Mandat</h6>
                            <p><?php echo $this->formatCurrency($member['Mandat']); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($member['dateInscription'])): ?>
                        <div class="mt-3 small text-muted">
                            <div>Inscrit depuis: <?php echo $this->formatDate($member['dateInscription'], 'd/m/Y'); ?></div>
                            <?php if (isset($member['derniereConnexion']) && !empty($member['derniereConnexion'])): ?>
                                <div>Dernière connexion: <?php echo $this->formatDate($member['derniereConnexion']); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($member['chercheurId']) && $member['chercheurId']): ?>
                <!-- Researcher Information -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Informations de chercheur</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($member['domaineRecherche']) && !empty($member['domaineRecherche'])): ?>
                            <h6>Domaine de recherche</h6>
                            <p><?php echo $this->escape($member['domaineRecherche']); ?></p>
                        <?php endif; ?>

                        <?php if (isset($member['bio']) && !empty($member['bio'])): ?>
                            <h6>Biographie / Intérêts</h6>
                            <p><?php echo nl2br($this->escape($member['bio'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <!-- Permissions -->
            <?php if (isset($member['permissions']) && !empty($member['permissions'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Permissions</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $permissions = explode(',', $member['permissions']);
                        $permissionCategories = [];

                        // Organize permissions by category
                        foreach ($permissions as $permission) {
                            $category = 'Autre';

                            if (strpos($permission, 'publication') !== false) {
                                $category = 'Publications';
                            } elseif (strpos($permission, 'event') !== false) {
                                $category = 'Événements';
                            } elseif (strpos($permission, 'project') !== false) {
                                $category = 'Projets';
                            } elseif (strpos($permission, 'idea') !== false) {
                                $category = 'Idées';
                            } elseif (strpos($permission, 'news') !== false) {
                                $category = 'Actualités';
                            } elseif (strpos($permission, 'contact') !== false) {
                                $category = 'Contacts';
                            } elseif (strpos($permission, 'user') !== false || strpos($permission, 'researcher') !== false || strpos($permission, 'member') !== false) {
                                $category = 'Utilisateurs';
                            } elseif (strpos($permission, 'admin') !== false) {
                                $category = 'Administration';
                            }

                            if (!isset($permissionCategories[$category])) {
                                $permissionCategories[$category] = [];
                            }

                            $permissionCategories[$category][] = $permission;
                        }

                        if (in_array('*', $permissions)):
                            ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-star me-2"></i>
                                Ce membre possède <strong>toutes les permissions</strong> (administrateur).
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($permissionCategories as $category => $perms): ?>
                                    <div class="col-md-6 mb-3">
                                        <h6><?php echo $category; ?></h6>
                                        <ul class="list-group">
                                            <?php foreach ($perms as $perm): ?>
                                                <li class="list-group-item">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    <?php echo $this->escape($perm); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Activities -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Activités</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="activitiesTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab" aria-controls="projects" aria-selected="true">
                                Projets
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="publications-tab" data-bs-toggle="tab" data-bs-target="#publications" type="button" role="tab" aria-controls="publications" aria-selected="false">
                                Publications
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab" aria-controls="events" aria-selected="false">
                                Événements
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content p-3" id="activitiesTabsContent">
                        <div class="tab-pane fade show active" id="projects" role="tabpanel" aria-labelledby="projects-tab">
                            <?php if (empty($projects)): ?>
                                <p class="text-muted">Aucun projet trouvé pour ce membre.</p>
                            <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($projects as $project): ?>
                                <a href="<?php echo $this->url('projects/' . $project['id']); ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo $this->escape($project['titre']); ?></h6>
                                        <span class="badge <?php
                                        switch($project['status']) {
                                            case 'En préparation':
                                                echo 'bg-secondary';
                                                break;
                                            case 'En cours':
                                                echo 'bg-primary';
                                                break;
                                            case 'Terminé':
                                                echo 'bg-success';
                                                break;
                                            case 'Suspendu':
                                                echo 'bg-warning';
                                                break;
                                            default:
                                                echo 'bg-info';
                                        }
                                        ?>"><?php echo $this->escape($project['status']); ?></span>
                                    </div>
                                    <p class="mb-1"><?php echo $this->truncate($project['description'], 100); ?></p>
                                    <small class="text-muted">
                                        Du <?php echo $this->formatDate($project['dateDebut'], 'd/m/Y'); ?>
                                        <?php if (!empty($project['dateFin'])): ?>
                                            au <?php echo $this->formatDate($project['dateFin'], 'd/m/Y'); ?>
                                        <?php endif; ?>
                                    </small>
                                </a>
<?php en