<!-- views/projects/index.php -->
<div class="projects-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Projets de Recherche</h1>
        <?php if ($auth->hasPermission('create_project')): ?>
            <a href="<?php echo $this->url('projects/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau projet
            </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo $this->url('projects'); ?>" method="get" class="row g-3">
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
                    <label for="chercheur" class="form-label">Chercheur</label>
                    <select name="chercheur" id="chercheur" class="form-select">
                        <option value="">Tous les chercheurs</option>
                        <?php foreach ($filters['chercheurs'] as $chercheur): ?>
                            <option value="<?php echo $chercheur['id']; ?>" <?php echo isset($_GET['chercheur']) && $_GET['chercheur'] == $chercheur['id'] ? 'selected' : ''; ?>>
                                <?php echo $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="year" class="form-label">Année</label>
                    <select name="year" id="year" class="form-select">
                        <option value="">Toutes les années</option>
                        <?php foreach ($filters['years'] as $year): ?>
                            <option value="<?php echo $year; ?>" <?php echo isset($_GET['year']) && $_GET['year'] == $year ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Titre, description..." value="<?php echo isset($_GET['search']) ? $this->escape($_GET['search']) : ''; ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Projects -->
    <?php if (empty($projects)): ?>
        <div class="alert alert-info">
            Aucun projet trouvé. Veuillez modifier vos critères de recherche ou <a href="<?php echo $this->url('projects/create'); ?>">créer un nouveau projet</a>.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?php echo $this->escape($project['titre']); ?></h5>
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
                        <div class="card-body">
                            <p class="card-text"><?php echo $this->truncate($project['description'], 150); ?></p>

                            <div class="mb-3">
                                <small class="text-muted">Chef de projet:</small>
                                <div class="d-flex align-items-center mt-1">
                                    <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <?php echo strtoupper(substr($project['chefPrenom'], 0, 1) . substr($project['chefNom'], 0, 1)); ?>
                                    </span>
                                    <span><?php echo $this->escape($project['chefPrenom'] . ' ' . $project['chefNom']); ?></span>
                                </div>
                            </div>

                            <div class="project-dates d-flex">
                                <div class="me-3">
                                    <small class="text-muted">Début:</small><br>
                                    <span><?php echo $this->formatDate($project['dateDebut'], 'd/m/Y'); ?></span>
                                </div>
                                <div>
                                    <small class="text-muted">Fin:</small><br>
                                    <span><?php echo !empty($project['dateFin']) ? $this->formatDate($project['dateFin'], 'd/m/Y') : 'En cours'; ?></span>
                                </div>
                            </div>

                            <?php if (!empty($project['budget'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Budget:</small>
                                    <span class="fw-bold"><?php echo $this->formatCurrency($project['budget']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between">
                                <a href="<?php echo $this->url('projects/' . $project['id']); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Détails
                                </a>
                                <div>
                                    <?php if ($auth->isLoggedIn() && ($project['chefProjet'] == $auth->getUser()['id'] || $auth->hasPermission('edit_project'))): ?>
                                        <a href="<?php echo $this->url('projects/edit/' . $project['id']); ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->url('projects?' . http_build_query(array_merge($_GET, ['page' => 1]))); ?>" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->url('projects?' . http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1]))); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo $this->url('projects?' . http_build_query(array_merge($_GET, ['page' => $i]))); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->url('projects?' . http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1]))); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->url('projects?' . http_build_query(array_merge($_GET, ['page' => $pagination['total_pages']]))); ?>" aria-label="Last">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-label="Last">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>