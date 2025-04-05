<!-- views/admin/publications.php -->
<div class="admin-publications">
    <h1 class="mb-4">Gestion des publications</h1>

    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des publications</h5>
                        <a href="<?php echo $this->url('publications/create'); ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Ajouter une publication
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="filters mb-4">
                        <form action="<?php echo $this->url('admin/publications'); ?>" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">Tous les types</option>
                                    <option value="Article" <?php echo isset($_GET['type']) && $_GET['type'] === 'Article' ? 'selected' : ''; ?>>Article</option>
                                    <option value="Livre" <?php echo isset($_GET['type']) && $_GET['type'] === 'Livre' ? 'selected' : ''; ?>>Livre</option>
                                    <option value="Chapitre" <?php echo isset($_GET['type']) && $_GET['type'] === 'Chapitre' ? 'selected' : ''; ?>>Chapitre</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="author" class="form-label">Auteur</label>
                                <select name="author" id="author" class="form-select">
                                    <option value="">Tous les auteurs</option>
                                    <?php if (isset($filters['authors'])): ?>
                                        <?php foreach ($filters['authors'] as $author): ?>
                                            <option value="<?php echo $author['id']; ?>" <?php echo isset($_GET['author']) && $_GET['author'] == $author['id'] ? 'selected' : ''; ?>>
                                                <?php echo $this->escape($author['prenom'] . ' ' . $author['nom']); ?>
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
                                <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_GET['search']) ? $this->escape($_GET['search']) : ''; ?>" placeholder="Titre ou contenu">
                            </div>
                            <div class="col-md-12 d-flex">
                                <button type="submit" class="btn btn-warning">Filtrer</button>
                                <a href="<?php echo $this->url('admin/publications'); ?>" class="btn btn-outline-secondary ms-2">Réinitialiser</a>
                            </div>
                        </form>
                    </div>

                    <!-- Publications Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Auteur</th>
                                <th>Date de publication</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($publications)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Aucune publication trouvée</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($publications as $publication): ?>
                                    <tr>
                                        <td><?php echo $publication['id']; ?></td>
                                        <td><?php echo $this->escape($publication['titre']); ?></td>
                                        <td>
                                        <span class="badge bg-<?php
                                        echo $publication['type'] === 'Article' ? 'info' :
                                            ($publication['type'] === 'Livre' ? 'primary' :
                                                ($publication['type'] === 'Chapitre' ? 'warning' : 'secondary'));
                                        ?>">
                                            <?php echo $publication['type']; ?>
                                        </span>
                                        </td>
                                        <td><?php echo isset($publication['auteurPrenom']) ? $this->escape($publication['auteurPrenom'] . ' ' . $publication['auteurNom']) : '-'; ?></td>
                                        <td><?php echo $this->formatDate($publication['datePublication']); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo $this->url('publications/' . $publication['id']); ?>" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo $this->url('publications/edit/' . $publication['id']); ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $publication['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo $publication['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer la publication <strong><?php echo $this->escape($publication['titre']); ?></strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form action="<?php echo $this->url('publications/delete/' . $publication['id']); ?>" method="post">
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

    <!-- Publications Stats -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Publications par type</h5>
                </div>
                <div class="card-body">
                    <canvas id="publicationsByTypeChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Publications par année</h5>
                </div>
                <div class="card-body">
                    <canvas id="publicationsByYearChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Publications by Type Chart
        var typeCtx = document.getElementById('publicationsByTypeChart').getContext('2d');
        var typeStats = <?php
            // Prepare data for chart
            $typeData = [];
            $typeCounts = [
                'Article' => 0,
                'Livre' => 0,
                'Chapitre' => 0,
                'Standard' => 0
            ];

            if (!empty($publications)) {
                foreach ($publications as $pub) {
                    $type = $pub['type'] ?? 'Standard';
                    if (isset($typeCounts[$type])) {
                        $typeCounts[$type]++;
                    }
                }
            }

            echo json_encode([
                'labels' => array_keys($typeCounts),
                'data' => array_values($typeCounts)
            ]);
            ?>;

        new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: typeStats.labels,
                datasets: [{
                    data: typeStats.data,
                    backgroundColor: [
                        '#17a2b8', // info
                        '#007bff', // primary
                        '#ffc107', // warning
                        '#6c757d'  // secondary
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

        // Publications by Year Chart
        var yearCtx = document.getElementById('publicationsByYearChart').getContext('2d');
        var yearStats = <?php
            // Prepare data for chart
            $yearData = [];
            $yearCounts = [];

            if (!empty($publications)) {
                foreach ($publications as $pub) {
                    $year = date('Y', strtotime($pub['datePublication']));
                    if (!isset($yearCounts[$year])) {
                        $yearCounts[$year] = 0;
                    }
                    $yearCounts[$year]++;
                }

                // Sort by year
                ksort($yearCounts);
            }

            echo json_encode([
                'labels' => array_keys($yearCounts),
                'data' => array_values($yearCounts)
            ]);
            ?>;

        new Chart(yearCtx, {
            type: 'bar',
            data: {
                labels: yearStats.labels,
                datasets: [{
                    label: 'Publications',
                    data: yearStats.data,
                    backgroundColor: '#ffc107',
                    borderColor: '#d39e00',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
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
    });
</script>