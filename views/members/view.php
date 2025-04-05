<!-- views/members/view.php -->
<div class="member-view-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?></h1>
        <div>
            <a href="<?php echo $this->url('members'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>

            <?php if ($auth->hasPermission('edit_member')): ?>
                <a href="<?php echo $this->url('members/edit/' . $member['id']); ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Member Profile Info -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <?php if (isset($member['profilePicture']) && !empty($member['profilePicture'])): ?>
                        <img src="<?php echo $this->escape('uploads/profile_pictures/' . $member['profilePicture']); ?>" alt="Profile Picture" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                            <span class="display-4 text-secondary"><?php echo strtoupper(substr($member['prenom'], 0, 1) . substr($member['nom'], 0, 1)); ?></span>
                        </div>
                    <?php endif; ?>

                    <h4><?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?></h4>
                    <p class="text-muted"><?php echo $this->escape($member['email']); ?></p>

                    <div class="d-flex justify-content-center mt-3">
                        <?php foreach ($roles as $role): ?>
                            <?php
                            $badgeClass = 'bg-secondary';
                            $roleText = $role;

                            switch($role) {
                                case 'admin':
                                    $badgeClass = 'bg-danger';
                                    $roleText = 'Administrateur';
                                    break;
                                case 'chercheur':
                                    $badgeClass = 'bg-primary';
                                    $roleText = 'Chercheur';
                                    break;
                                case 'membreBureauExecutif':
                                    $badgeClass = 'bg-success';
                                    $roleText = 'Membre Bureau';
                                    break;
                                case 'president':
                                    $badgeClass = 'bg-warning text-dark';
                                    $roleText = 'Président';
                                    break;
                                case 'vicepresident':
                                    $badgeClass = 'bg-warning text-dark';
                                    $roleText = 'Vice-président';
                                    break;
                                case 'generalsecretary':
                                    $badgeClass = 'bg-info';
                                    $roleText = 'Secrétaire';
                                    break;
                                case 'treasurer':
                                case 'vicetreasurer':
                                    $badgeClass = 'bg-success';
                                    $roleText = $role === 'treasurer' ? 'Trésorier' : 'Vice-trésorier';
                                    break;
                                case 'counselor':
                                    $badgeClass = 'bg-secondary';
                                    $roleText = 'Conseiller';
                                    break;
                            }
                            ?>
                            <span class="badge <?php echo $badgeClass; ?> me-1"><?php echo $roleText; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="small text-muted">
                        <div><strong>Membre depuis:</strong> <?php echo $this->formatDate($member['dateInscription'], 'd/m/Y'); ?></div>
                        <?php if (isset($member['derniereConnexion']) && !empty($member['derniereConnexion'])): ?>
                            <div><strong>Dernière connexion:</strong> <?php echo $this->formatDate($member['derniereConnexion']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Member Status Card -->
            <div class="card mt-3">
                <div class="card-header bg-<?php echo $member['status'] ? 'success' : 'danger'; ?> text-white">
                    <h5 class="mb-0">Statut</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <span class="badge bg-<?php echo $member['status'] ? 'success' : 'danger'; ?>">
                            <?php echo $member['status'] ? 'Actif' : 'Inactif'; ?>
                        </span>
                    </p>
                </div>
                <?php if ($auth->hasPermission('edit_member')): ?>
                    <div class="card-footer">
                        <form action="<?php echo $this->url('members/toggle-status/' . $member['id']); ?>" method="post">
                            <?php echo CSRF::tokenField(); ?>
                            <button type="submit" class="btn btn-sm btn-<?php echo $member['status'] ? 'danger' : 'success'; ?>">
                                <?php echo $member['status'] ? 'Désactiver' : 'Activer'; ?> ce membre
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Researcher Info -->
            <?php if (in_array('chercheur', $roles) && !empty($userDetails)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informations du chercheur</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($userDetails['domaineRecherche'])): ?>
                            <div class="mb-3">
                                <strong>Domaine de recherche:</strong>
                                <p><?php echo $this->escape($userDetails['domaineRecherche']); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($userDetails['bio'])): ?>
                            <div>
                                <strong>Bio / Présentation:</strong>
                                <p><?php echo nl2br($this->escape($userDetails['bio'])); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Board Member Info -->
            <?php if (in_array('membreBureauExecutif', $roles) && !empty($userDetails)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Informations du membre du bureau</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($userDetails['role'])): ?>
                            <div class="mb-3">
                                <strong>Rôle:</strong>
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
                                ?>
                                <p><?php echo $this->escape($roleTitle); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($userDetails['Mandat'])): ?>
                            <div class="mb-3">
                                <strong>Mandat:</strong>
                                <p><?php echo $this->formatCurrency($userDetails['Mandat']); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($userDetails['permissions'])): ?>
                            <div>
                                <strong>Permissions:</strong>
                                <p>
                                    <?php
                                    $permissions = explode(',', $userDetails['permissions']);
                                    foreach ($permissions as $permission): ?>
                                        <span class="badge bg-secondary me-1"><?php echo $this->escape($permission); ?></span>
                                    <?php endforeach; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Activities Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Activités récentes</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="activityTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab" aria-controls="projects" aria-selected="true">Projets</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="publications-tab" data-bs-toggle="tab" data-bs-target="#publications" type="button" role="tab" aria-controls="publications" aria-selected="false">Publications</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab" aria-controls="events" aria-selected="false">Événements</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ideas-tab" data-bs-toggle="tab" data-bs-target="#ideas" type="button" role="tab" aria-controls="ideas" aria-selected="false">Idées</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="activityTabsContent">
                        <div class="tab-pane fade show active" id="projects" role="tabpanel" aria-labelledby="projects-tab">
                            <?php if (empty($projects)): ?>
                                <p class="text-muted">Aucun projet trouvé pour ce membre.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($projects as $project): ?>
                                        <a href="<?php echo $this->url('projects/' . $project['id']); ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo $this->escape($project['titre']); ?></h6>
                                                <span class="badge <?php echo $project['chefProjet'] == $member['id'] ? 'bg-primary' : 'bg-secondary'; ?>"><?php echo $project['chefProjet'] == $member['id'] ? 'Chef' : 'Participant'; ?></span>
                                            </div>
                                            <p class="mb-1"><?php echo $this->truncate($project['description'], 100); ?></p>
                                            <small class="text-muted">
                                                <?php echo $this->formatDate($project['dateDebut']); ?> -
                                                <?php echo !empty($project['dateFin']) ? $this->formatDate($project['dateFin']) : 'En cours'; ?>
                                            </small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="tab-pane fade" id="publications" role="tabpanel" aria-labelledby="publications-tab">
                            <?php if (empty($publications)): ?>
                                <p class="text-muted">Aucune publication trouvée pour ce membre.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($publications as $publication): ?>
                                        <a href="<?php echo $this->url('publications/' . $publication['id']); ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo $this->escape($publication['titre']); ?></h6>
                                                <span class="badge bg-secondary"><?php echo $this->escape($publication['type']); ?></span>
                                            </div>
                                            <p class="mb-1"><?php echo $this->truncate($publication['contenu'], 100); ?></p>
                                            <small class="text-muted"><?php echo $this->formatDate($publication['datePublication']); ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="events-tab">
                            <?php if (empty($events)): ?>
                                <p class="text-muted">Aucun événement trouvé pour ce membre.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($events as $event): ?>
                                        <a href="<?php echo $this->url('events/' . $event['id']); ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo $this->escape($event['titre']); ?></h6>
                                                <span class="badge bg-secondary"><?php echo $this->escape($event['type']); ?></span>
                                            </div>
                                            <p class="mb-1"><?php echo $this->truncate($event['description'], 100); ?></p>
                                            <small class="text-muted">
                                                <?php echo $this->formatDate($event['eventDate'] ?? $event['dateCreation']); ?>
                                            </small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="tab-pane fade" id="ideas" role="tabpanel" aria-labelledby="ideas-tab">
                            <?php if (empty($ideas)): ?>
                                <p class="text-muted">Aucune idée trouvée pour ce membre.</p>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($ideas as $idea): ?>
                                        <a href="<?php echo $this->url('ideas/' . $idea['id']); ?>" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><?php echo $this->escape($idea['titre']); ?></h6>
                                                <span class="badge <?php
                                                switch($idea['status']) {
                                                    case 'en attente':
                                                        echo 'bg-secondary';
                                                        break;
                                                    case 'approuvée':
                                                        echo 'bg-success';
                                                        break;
                                                    case 'refusé':
                                                        echo 'bg-danger';
                                                        break;
                                                    default:
                                                        echo 'bg-primary';
                                                }
                                                ?>"><?php echo $this->escape($idea['status']); ?></span>
                                            </div>
                                            <p class="mb-1"><?php echo $this->truncate($idea['description'], 100); ?></p>
                                            <small class="text-muted"><?php echo $this->formatDate($idea['dateProposition']); ?></small>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>