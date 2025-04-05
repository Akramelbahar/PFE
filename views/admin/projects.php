<!-- views/admin/projects.php -->
<div class="admin-projects">
    <h1 class="mb-4">Gestion des projets</h1>

    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des projets</h5>
                        <a href="<?php echo $this->url('projects/create'); ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Ajouter un projet
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="filters mb-4">
                        <form action="<?php echo $this->url('admin/projects'); ?>" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="En préparation" <?php echo isset($_GET['status']) && $_GET['status'] === 'En préparation' ? 'selected' : ''; ?>>En préparation</option>
                                    <option value="En cours" <?php echo isset($_GET['status']) && $_GET['status'] === 'En cours' ? 'selected' : ''; ?>>En cours</option>
                                    <option value="Terminé" <?php echo isset($_GET['status']) && $_GET['status'] === 'Terminé' ? 'selected' : ''; ?>>Terminé</option>
                                    <option value="Suspendu" <?php echo isset($_GET['status']) && $_GET['status'] === 'Suspendu' ? 'selected' : ''; ?>>Suspendu</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="chercheur" class="form-label">Chef de projet</label>
                                <select name="chercheur" id="chercheur" class="form-select">
                                    <option value="">Tous les chercheurs</option>
                                    <?php if (isset($filters['chercheurs'])): ?>
                                        <?php foreach ($filters['chercheurs'] as $chercheur): ?>
                                            <option value="<?php echo $chercheur['id']; ?>" <?php echo isset($_GET['chercheur']) && $_GET['chercheur'] == $chercheur['id'] ? 'selected' : ''; ?>>
                                                <?php echo $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="year" class="form-label">Année</label>
                                <select name="year" id="year" class="form-select">
                                    <option value="">Toutes les années</option>
                                    <?php if (isset($filters['years'])): ?>
                                        <?php foreach ($filters['years'] as $year): ?>
                                            <option value="<?php echo $year; ?>" <?php echo isset($_GET['year']) && $_GET['year'] == $year ? 'selected' : ''; ?>>
                                                <?php echo $year; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_GET['search']) ? $this->escape($_GET['search']) : ''; ?>" placeholder="Titre ou description">
                            </div>
                            <div class="col-md-12 d-flex">
                                <button type="submit" class="btn btn-danger">Filtrer</button>
                                <a href="<?php echo $this->url('admin/projects'); ?>" class="btn btn-outline-secondary ms-2">Réinitialiser</a>
                            </div>
                        </form>
                    </div>

                    <!-- Projects Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Chef de projet</th>
                                <th>Dates</th>
                                <th>Budget</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($projects)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Aucun projet trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td><?php echo $project['id']; ?></td>
                                        <td><?php echo $this->escape($project['titre']); ?></td>
                                        <td><?php echo isset($project['chefPrenom']) ? $this->escape($project['chefPrenom'] . ' ' . $project['chefNom']) : '-'; ?></td>
                                        <td>
                                            <?php echo $this->formatDate($project['dateDebut'], 'd/m/Y'); ?>
                                            <?php if (!empty($project['dateFin'])): ?>
                                                - <?php echo $this->formatDate($project['dateFin'], 'd/m/Y'); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($project['budget'])): ?>
                                                <?php echo $this->formatCurrency($project['budget']); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                        <span class="badge bg-<?php
                                        echo $project['status'] === 'En cours' ? 'success' :
                                            ($project['status'] === 'En préparation' ? 'info' :
                                                ($project['status'] === 'Terminé' ? 'primary' : 'secondary'));
                                        ?>">
                                            <?php echo $project['status']; ?>
                                        </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo $this->url('projects/' . $project['id']); ?>" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo $this->url('projects/edit/' . $project['id']); ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $project['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo $project['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer le projet <strong><?php echo $this->escape($project['titre']); ?></strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form action="<?php echo $this->url('projects/delete/' . $project['id']); ?>" method="post">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Stats -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Projets par statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="projectsByStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Budget total par année</h5>
                </div>
                <div class="card-body">
                    <canvas id="projectsBudgetChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Projects by Status Chart
        var statusCtx = document.getElementById('projectsByStatusChart').getContext('2d');
        var statusStats = <?php
            // Prepare data for chart
            $statusCounts = [
                'En préparation' => 0,
                'En cours' => 0,
                'Terminé' => 0,
                'Suspendu' => 0
            ];

            if (!empty($projects)) {
                foreach ($projects as $project) {
                    $status = $project['status'] ?? 'En cours';
                    if (isset($statusCounts[$status])) {
                        $statusCounts[$status]++;
                    }
                }
            }

            echo json_encode([
                'labels' => array_keys($statusCounts),
                'data' => array_values($statusCounts)
            ]);
            ?>;

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusStats.labels,
                datasets: [{
                    data: statusStats.data,
                    backgroundColor: [
                        '#17a2b8', // info - En préparation
                        '#28a745', // success - En cours
                        '#007bff', // primary - Terminé
                        '#6c757d'  // secondary - Suspendu
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Budget by Year Chart
        var budgetCtx = document.getElementById('projectsBudgetChart').getContext('2d');
        var budgetStats = <?php
            // Prepare data for chart
            $yearBudgets = [];

            if (!empty($projects)) {
                foreach ($projects as $project) {
                    $year = date('Y', strtotime($project['dateDebut']));
                    if (!isset($yearBudgets[$year])) {
                        $yearBudgets[$year] = 0;
                    }
                    if (!empty($project['budget'])) {
                        $yearBudgets[$year] += (float)$project['budget'];
                    }
                }

                // Sort by year
                ksort($yearBudgets);
            }

            echo json_encode([
                'labels' => array_keys($yearBudgets),
                'data' => array_values($yearBudgets)
            ]);
            ?>;

        new Chart(budgetCtx, {
            type: 'bar',
            data: {
                labels: budgetStats.labels,
                datasets: [{
                    label: 'Budget (MAD)',
                    data: budgetStats.data,
                    backgroundColor: '#dc3545',
                    borderColor: '#c82333',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' MAD';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw.toLocaleString() + ' MAD';
                            }
                        }
                    }
                }
            }
        });
    });
</script>