<!-- views/admin/news.php -->
<div class="admin-news">
    <h1 class="mb-4">Gestion des actualités</h1>

    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des actualités</h5>
                        <a href="<?php echo $this->url('news/create'); ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Ajouter une actualité
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="filters mb-4">
                        <form action="<?php echo $this->url('admin/news'); ?>" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label for="author" class="form-label">Auteur</label>
                                <select name="author" id="author" class="form-select">
                                    <option value="">Tous les auteurs</option>
                                    <?php if (isset($authors)): ?>
                                        <?php foreach ($authors as $author): ?>
                                            <option value="<?php echo $author['id']; ?>" <?php echo isset($_GET['author']) && $_GET['author'] == $author['id'] ? 'selected' : ''; ?>>
                                                <?php echo $this->escape($author['prenom'] . ' ' . $author['nom']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date de début</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo isset($_GET['date_from']) ? $this->escape($_GET['date_from']) : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date de fin</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo isset($_GET['date_to']) ? $this->escape($_GET['date_to']) : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_GET['search']) ? $this->escape($_GET['search']) : ''; ?>" placeholder="Titre ou contenu">
                            </div>
                            <div class="col-md-12 d-flex">
                                <button type="submit" class="btn btn-info">Filtrer</button>
                                <a href="<?php echo $this->url('admin/news'); ?>" class="btn btn-outline-secondary ms-2">Réinitialiser</a>
                            </div>
                        </form>
                    </div>

                    <!-- News Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Titre</th>
                                <th>Image</th>
                                <th>Auteur</th>
                                <th>Date de publication</th>
                                <th>Lié à</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($news)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Aucune actualité trouvée</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($news as $item): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td><?php echo $this->escape($item['titre']); ?></td>
                                        <td>
                                            <?php if (!empty($item['mediaUrl'])): ?>
                                                <img src="<?php echo $this->escape($item['mediaUrl']); ?>" class="img-thumbnail" alt="Image" style="max-width: 50px;">
                                            <?php else: ?>
                                                <span class="text-muted">Aucune image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo isset($item['auteurPrenom']) ? $this->escape($item['auteurPrenom'] . ' ' . $item['auteurNom']) : '-'; ?></td>
                                        <td><?php echo $this->formatDate($item['datePublication']); ?></td>
                                        <td>
                                            <?php if (!empty($item['evenementId'])): ?>
                                                <a href="<?php echo $this->url('events/' . $item['evenementId']); ?>" class="badge bg-success">
                                                    Événement #<?php echo $item['evenementId']; ?>
                                                </a>
                                            <?php elseif (!empty($item['projetId'])): ?>
                                                <a href="<?php echo $this->url('projects/' . $item['projetId']); ?>" class="badge bg-danger">
                                                    Projet #<?php echo $item['projetId']; ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Aucun</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo $this->url('news/' . $item['id']); ?>" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo $this->url('news/edit/' . $item['id']); ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $item['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo $item['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer l'actualité <strong><?php echo $this->escape($item['titre']); ?></strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form action="<?php echo $this->url('news/delete/' . $item['id']); ?>" method="post">
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

    <!-- News Activity Chart -->
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Activité de publication</h5>
                </div>
                <div class="card-body">
                    <canvas id="newsActivityChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // News Activity Chart
        var activityCtx = document.getElementById('newsActivityChart').getContext('2d');
        var activityStats = <?php
            // Generate data for the last 12 months
            $monthlyData = [];
            $currentMonth = date('n');
            $currentYear = date('Y');

            for ($i = 0; $i < 12; $i++) {
                $month = (($currentMonth - $i - 1) % 12) + 1;
                $year = $currentYear;
                if ($month > $currentMonth) {
                    $year--;
                }

                $monthLabel = date('M Y', mktime(0, 0, 0, $month, 1, $year));
                $monthlyData[$monthLabel] = 0;
            }

            // Reverse to chronological order
            $monthlyData = array_reverse($monthlyData);

            if (!empty($news)) {
                foreach ($news as $item) {
                    $month = date('M Y', strtotime($item['datePublication']));
                    if (isset($monthlyData[$month])) {
                        $monthlyData[$month]++;
                    }
                }
            }

            echo json_encode([
                'labels' => array_keys($monthlyData),
                'data' => array_values($monthlyData)
            ]);
            ?>;

        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: activityStats.labels,
                datasets: [{
                    label: 'Actualités publiées',
                    data: activityStats.data,
                    backgroundColor: 'rgba(23, 162, 184, 0.2)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
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
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>