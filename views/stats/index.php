<!-- views/stats/index.php -->
<div class="stats-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Statistiques et Rapports</h1>
    </div>

    <!-- Date Range Filter -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo $this->url('stats'); ?>" method="get" class="row g-3">
                <div class="col-md-4">
                    <label for="date_from" class="form-label">Date de début</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="<?php echo isset($_GET['date_from']) ? $this->escape($_GET['date_from']) : date('Y-m-d', strtotime('-1 year')); ?>">
                </div>
                <div class="col-md-4">
                    <label for="date_to" class="form-label">Date de fin</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="<?php echo isset($_GET['date_to']) ? $this->escape($_GET['date_to']) : date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Appliquer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Projets actifs</h6>
                            <h1 class="display-4"><?php echo $stats['activeProjects']; ?></h1>
                        </div>
                        <i class="fas fa-project-diagram fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('projects?status=En cours'); ?>" class="text-white">Voir les projets</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Publications</h6>
                            <h1 class="display-4"><?php echo $stats['publications']; ?></h1>
                        </div>
                        <i class="fas fa-book fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('publications'); ?>" class="text-white">Voir les publications</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Événements</h6>
                            <h1 class="display-4"><?php echo $stats['events']; ?></h1>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('events'); ?>" class="text-white">Voir les événements</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Membres actifs</h6>
                            <h1 class="display-4"><?php echo $stats['activeMembers']; ?></h1>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between bg-warning text-dark">
                    <a href="<?php echo $this->url('members'); ?>" class="text-dark">Voir les membres</a>
                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- Activity Over Time -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Activité par mois</h5>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Publications by Type -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Publications par type</h5>
                </div>
                <div class="card-body">
                    <canvas id="publicationsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Projects by Status -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Projets par statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="projectsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Contributors -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top contributeurs</h5>
                </div>
                <div class="card-body">
                    <canvas id="contributorsChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Options -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Télécharger les rapports</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Rapport d'activité</h5>
                            <p class="card-text">Résumé complet de toutes les activités pendant la période sélectionnée.</p>
                            <a href="<?php echo $this->url('stats/report/activity' . (isset($_GET['date_from']) ? '?date_from=' . $_GET['date_from'] . '&date_to=' . $_GET['date_to'] : '')); ?>" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i> Télécharger PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Rapport de publications</h5>
                            <p class="card-text">Détails des publications et leur impact académique.</p>
                            <a href="<?php echo $this->url('stats/report/publications' . (isset($_GET['date_from']) ? '?date_from=' . $_GET['date_from'] . '&date_to=' . $_GET['date_to'] : '')); ?>" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i> Télécharger PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Rapport financier</h5>
                            <p class="card-text">Résumé des budgets de projets et dépenses.</p>
                            <a href="<?php echo $this->url('stats/report/financial' . (isset($_GET['date_from']) ? '?date_from=' . $_GET['date_from'] . '&date_to=' . $_GET['date_to'] : '')); ?>" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i> Télécharger PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activity Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($charts['activityByMonth']['labels']); ?>,
                datasets: [
                    {
                        label: 'Projets',
                        data: <?php echo json_encode($charts['activityByMonth']['projects']); ?>,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.1
                    },
                    {
                        label: 'Publications',
                        data: <?php echo json_encode($charts['activityByMonth']['publications']); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    },
                    {
                        label: 'Événements',
                        data: <?php echo json_encode($charts['activityByMonth']['events']); ?>,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Publications Chart
        const publicationsCtx = document.getElementById('publicationsChart').getContext('2d');
        const publicationsChart = new Chart(publicationsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Articles', 'Livres', 'Chapitres'],
                datasets: [{
                    data: [
                        <?php echo $charts['publicationsByType']['articles']; ?>,
                        <?php echo $charts['publicationsByType']['books']; ?>,
                        <?php echo $charts['publicationsByType']['chapters']; ?>
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Projects Chart
        const projectsCtx = document.getElementById('projectsChart').getContext('2d');
        const projectsChart = new Chart(projectsCtx, {
            type: 'bar',
            data: {
                labels: ['En préparation', 'En cours', 'Terminé', 'Suspendu'],
                datasets: [{
                    label: 'Nombre de projets',
                    data: [
                        <?php echo $charts['projectsByStatus']['preparation']; ?>,
                        <?php echo $charts['projectsByStatus']['active']; ?>,
                        <?php echo $charts['projectsByStatus']['completed']; ?>,
                        <?php echo $charts['projectsByStatus']['suspended']; ?>
                    ],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(201, 203, 207, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Contributors Chart
        const contributorsCtx = document.getElementById('contributorsChart').getContext('2d');
        const contributorsChart = new Chart(contributorsCtx, {
            type: 'horizontalBar',
            data: {
                labels: <?php echo json_encode($charts['topContributors']['names']); ?>,
                datasets: [{
                    label: 'Contributions',
                    data: <?php echo json_encode($charts['topContributors']['counts']); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script><!-- views/partials/language-selector.php -->
<div class="dropdown">
    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <?php
        $currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'fr';
        switch($currentLang) {
            case 'fr':
                echo '<i class="flag-icon flag-icon-fr"></i> Français';
                break;
            case 'en':
                echo '<i class="flag-icon flag-icon-gb"></i> English';
                break;
            case 'ar':
                echo '<i class="flag-icon flag-icon-ma"></i> العربية';
                break;
        }
        ?>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
        <li>
            <a class="dropdown-item <?php echo $currentLang === 'fr' ? 'active' : ''; ?>" href="<?php echo $this->url('language/set/fr?' . http_build_query(['redirect' => $_SERVER['REQUEST_URI']])); ?>">
                <i class="flag-icon flag-icon-fr"></i> Français
            </a>
        </li>
        <li>
            <a class="dropdown-item <?php echo $currentLang === 'en' ? 'active' : ''; ?>" href="<?php echo $this->url('language/set/en?' . http_build_query(['redirect' => $_SERVER['REQUEST_URI']])); ?>">
                <i class="flag-icon flag-icon-gb"></i> English
            </a>
        </li>
        <li>
            <a class="dropdown-item <?php echo $currentLang === 'ar' ? 'active' : ''; ?>" href="<?php echo $this->url('language/set/ar?' . http_build_query(['redirect' => $_SERVER['REQUEST_URI']])); ?>">
                <i class="flag-icon flag-icon-ma"></i> العربية
            </a>
        </li>
    </ul>
</div>