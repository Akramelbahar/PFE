<!-- views/bureau/dashboard.php -->
<div class="bureau-dashboard-page">
    <h1 class="mb-4">Tableau de bord du Bureau Exécutif</h1>

    <div class="row">
        <div class="col-md-8">
            <!-- Pending Tasks -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tâches en attente</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php if ($pendingIdeasCount > 0): ?>
                            <a href="<?php echo $this->url('admin/ideas', ['status' => 'Soumise']); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-lightbulb me-2 text-warning"></i>
                                    Idées de recherche à évaluer
                                </div>
                                <span class="badge bg-warning rounded-pill"><?php echo $pendingIdeasCount; ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if ($pendingPublicationsCount > 0): ?>
                            <a href="<?php echo $this->url('admin/publications/pending'); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-book me-2 text-info"></i>
                                    Publications à approuver
                                </div>
                                <span class="badge bg-info rounded-pill"><?php echo $pendingPublicationsCount; ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if ($unreadContactsCount > 0): ?>
                            <a href="<?php echo $this->url('admin/contacts'); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    Messages de contact non lus
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $unreadContactsCount; ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if ($upcomingEventsCount > 0): ?>
                            <a href="<?php echo $this->url('events'); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-calendar-alt me-2 text-success"></i>
                                    Événements à venir
                                </div>
                                <span class="badge bg-success rounded-pill"><?php echo $upcomingEventsCount; ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if ($pendingIdeasCount === 0 && $pendingPublicationsCount === 0 && $unreadContactsCount === 0 && $upcomingEventsCount === 0): ?>
                            <div class="list-group-item text-muted">
                                <i class="fas fa-check-circle me-2"></i>
                                Aucune tâche en attente pour le moment.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Activités récentes</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="timeline-item">
                                <div class="timeline-icon bg-<?php echo $activity['typeColor']; ?>">
                                    <i class="<?php echo $activity['typeIcon']; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6><?php echo $this->escape($activity['title']); ?></h6>
                                    <p><?php echo $this->escape($activity['description']); ?></p>
                                    <div class="text-muted small">
                                        <?php echo $this->formatDate($activity['date']); ?>
                                        <?php if (isset($activity['user'])): ?>
                                            par <?php echo $this->escape($activity['user']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <?php if (empty($recentActivities)): ?>
                            <div class="text-muted text-center my-4">
                                Aucune activité récente.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Role Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Votre rôle</h5>
                </div>
                <div class="card-body">
                    <?php
                    $roleBadgeClass = 'bg-secondary';
                    $roleIcon = 'fas fa-user-tie';
                    $roleText = $memberDetails['role'];

                    switch($memberDetails['role']) {
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
                    <div class="text-center">
                        <div class="avatar avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="<?php echo $roleIcon; ?> fa-2x"></i>
                        </div>
                        <h4 class="mb-3"><?php echo $roleText; ?></h4>
                        <span class="badge <?php echo $roleBadgeClass; ?> d-block p-2 mb-3">
                            <?php echo $roleText; ?> du Bureau Exécutif
                        </span>

                        <?php if (isset($memberDetails['Mandat']) && !empty($memberDetails['Mandat'])): ?>
                            <p>
                                <strong>Mandat:</strong> <?php echo $this->formatCurrency($memberDetails['Mandat']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if ($auth->hasPermission('create_event')): ?>
                            <a href="<?php echo $this->url('events/create'); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-calendar-plus me-2"></i>Créer un événement
                            </a>
                        <?php endif; ?>

                        <?php if ($auth->hasPermission('create_news')): ?>
                            <a href="<?php echo $this->url('news/create'); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-newspaper me-2"></i>Publier une actualité
                            </a>
                        <?php endif; ?>

                        <?php if ($auth->hasPermission('create_project')): ?>
                            <a href="<?php echo $this->url('projects/create'); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-project-diagram me-2"></i>Créer un projet
                            </a>
                        <?php endif; ?>

                        <?php if ($auth->hasPermission('create_board_member')): ?>
                            <a href="<?php echo $this->url('admin/bureau/create'); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus me-2"></i>Ajouter un membre au bureau
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="mb-0"><?php echo $stats['totalProjects']; ?></h3>
                                <p class="text-muted mb-0">Projets</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="mb-0"><?php echo $stats['totalEvents']; ?></h3>
                                <p class="text-muted mb-0">Événements</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="mb-0"><?php echo $stats['totalResearchers']; ?></h3>
                                <p class="text-muted mb-0">Chercheurs</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h3 class="mb-0"><?php echo $stats['totalIdeas']; ?></h3>
                                <p class="text-muted mb-0">Idées</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>