<!-- views/publications/index.php -->
<div class="publications-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Publications</h1>
        <?php if ($auth->hasPermission('add_publication')): ?>
            <a href="<?php echo $this->url('publications/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle publication
            </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo $this->url('publications'); ?>" method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Tous les types</option>
                        <?php foreach ($filters['types'] as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo isset($_GET['type']) && $_GET['type'] === $type ? 'selected' : ''; ?>>
                                <?php echo $type; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="author" class="form-label">Auteur</label>
                    <select name="author" id="author" class="form-select">
                        <option value="">Tous les auteurs</option>
                        <?php foreach ($filters['authors'] as $author): ?>
                            <option value="<?php echo $author['id']; ?>" <?php echo isset($_GET['author']) && $_GET['author'] == $author['id'] ? 'selected' : ''; ?>>
                                <?php echo $this->escape($author['prenom'] . ' ' . $author['nom']); ?>
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
                    <input type="text" name="search" id="search" class="form-control" placeholder="Titre, contenu..." value="<?php echo isset($_GET['search']) ? $this->escape($_GET['search']) : ''; ?>">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Publications -->
    <?php if (empty($publications)): ?>
        <div class="alert alert-info">
            Aucune publication trouvée. Veuillez modifier vos critères de recherche ou <a href="<?php echo $this->url('publications/create'); ?>">créer une nouvelle publication</a>.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($publications as $publication): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?php echo $this->escape($publication['titre']); ?></h5>
                            <span class="badge <?php
                            switch($publication['type']) {
                                case 'Article':
                                    echo 'bg-primary';
                                    break;
                                case 'Livre':
                                    echo 'bg-success';
                                    break;
                                case 'Chapitre':
                                    echo 'bg-info';
                                    break;
                                default:
                                    echo 'bg-secondary';
                            }
                            ?>"><?php echo $this->escape($publication['type']); ?></span>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo $this->truncate($publication['contenu'], 150); ?></p>

                            <?php if (!empty($publication['auteurNom']) || !empty($publication['auteurPrenom'])): ?>
                                <div class="mb-3">
                                    <small class="text-muted">Auteur:</small>
                                    <div class="d-flex align-items-center mt-1">
                                        <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <?php echo strtoupper(substr($publication['auteurPrenom'] ?? '', 0, 1) . substr($publication['auteurNom'] ?? '', 0, 1)); ?>
                                        </span>
                                        <span><?php echo $this->escape(($publication['auteurPrenom'] ?? '') . ' ' . ($publication['auteurNom'] ?? '')); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-2">
                                <small class="text-muted">Publié le:</small>
                                <span><?php echo $this->formatDate($publication['datePublication'], 'd/m/Y'); ?></span>
                            </div>

                            <?php if (!empty($publication['documents'])): ?>
                                <?php $documents = json_decode($publication['documents'], true); ?>
                                <?php if (!empty($documents)): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Documents:</small>
                                        <span class="badge bg-secondary"><?php echo count($documents); ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (!empty($publication['evenementId'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Lié à un évènement</small>
                                    <i class="fas fa-calendar-alt ms-1 text-info"></i>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($publication['projetId'])): ?>
                                <div class="mt-2">
                                    <small class="text-muted">Lié à un projet</small>
                                    <i class="fas fa-project-diagram ms-1 text-primary"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between">
                                <a href="<?php echo $this->url('publications/' . $publication['id']); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Détails
                                </a>
                                <div>
                                    <?php if ($auth->isLoggedIn() && ($publication['auteurId'] == $auth->getUser()['id'] || $auth->hasPermission('edit_publication'))): ?>
                                        <a href="<?php echo $this->url('publications/edit/' . $publication['id']); ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($auth->isLoggedIn() && ($publication['auteurId'] == $auth->getUser()['id'] && $auth->hasPermission('delete_own_publication')) || $auth->hasPermission('delete_publication')): ?>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $publication['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal-<?php echo $publication['id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirmer la suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Êtes-vous sûr de vouloir supprimer cette publication?</p>
                                                        <p><strong><?php echo $this->escape($publication['titre']); ?></strong></p>
                                                        <p>Cette action est irréversible.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="<?php echo $this->url('publications/delete/' . $publication['id']); ?>" method="post">
                                                            <?php echo CSRF::tokenField(); ?>
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                            <a class="page-link" href="<?php echo $this->url('publications?' . http_build_query(array_merge($_GET, ['page' => 1]))); ?>" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->url('publications?' . http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1]))); ?>" aria-label="Previous">
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
                            <a class="page-link" href="<?php echo $this->url('publications?' . http_build_query(array_merge($_GET, ['page' => $i]))); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->url('publications?' . http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1]))); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->url('publications?' . http_build_query(array_merge($_GET, ['page' => $pagination['total_pages']]))); ?>" aria-label="Last">
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

    <!-- Publication Type Information -->
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Types de Publications</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">Article</span>
                        <span>Articles de journaux scientifiques, articles de conférences</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-success me-2">Livre</span>
                        <span>Livres complets, monographies</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-info me-2">Chapitre</span>
                        <span>Chapitres de livres, contributions à des ouvrages</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>