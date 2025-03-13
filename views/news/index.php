<?php
/**
 * News listing view
 */
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Actualités</h1>
        <?php if ($auth->hasPermission('create_news')): ?>
            <a href="<?= $this->url('news/create') ?>" class="btn btn-primary">
                <i class="fa fa-plus"></i> Nouvelle actualité
            </a>
        <?php endif; ?>
    </div>

    <!-- Flash messages -->
    <?php if (isset($flash['message'])): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= $this->escape($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Search bar -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?= $this->url('news') ?>" method="get" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" name="search" id="search" class="form-control"
                           placeholder="Titre, contenu..."
                           value="<?= isset($search) ? $this->escape($search) : '' ?>">
                </div>

                <div class="col-md-3">
                    <label for="sort" class="form-label">Trier par</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="recent" <?= isset($sort) && $sort === 'recent' ? 'selected' : '' ?>>Plus récentes</option>
                        <option value="oldest" <?= isset($sort) && $sort === 'oldest' ? 'selected' : '' ?>>Plus anciennes</option>
                        <option value="title" <?= isset($sort) && $sort === 'title' ? 'selected' : '' ?>>Titre</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- News Grid -->
    <?php if (empty($news)): ?>
        <div class="alert alert-info">
            Aucune actualité trouvée.
            <?php if ($auth->hasPermission('create_news')): ?>
                <a href="<?= $this->url('news/create') ?>">Créer une nouvelle actualité</a>.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($news as $item): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($item['mediaUrl'])): ?>
                            <img src="<?= $this->url($item['mediaUrl']) ?>" class="card-img-top" alt="Image de l'actualité" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light text-center py-5">
                                <i class="fa fa-newspaper fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?= $this->url('news/' . $item['id']) ?>" class="text-decoration-none text-dark">
                                    <?= $this->escape($item['titre']) ?>
                                </a>
                            </h5>
                            <p class="card-text small text-muted">
                                <?= $this->formatDate($item['datePublication'], 'd/m/Y') ?> |
                                Par <?= $this->escape($item['auteurPrenom'] . ' ' . $item['auteurNom']) ?>
                            </p>
                            <p class="card-text">
                                <?= $this->truncate(strip_tags($item['contenu']), 150) ?>
                            </p>
                        </div>

                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <a href="<?= $this->url('news/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary">
                                Lire la suite
                            </a>

                            <?php
                            $isAuthor = $item['auteurId'] == $auth->getUser()['id'];
                            $canEdit = $isAuthor ?
                                $auth->hasPermission('edit_own_news') :
                                $auth->hasPermission('edit_news');

                            $canDelete = $isAuthor ?
                                $auth->hasPermission('delete_own_news') :
                                $auth->hasPermission('delete_news');

                            if ($canEdit || $canDelete):
                                ?>
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php if ($canEdit): ?>
                                        <a href="<?= $this->url('news/edit/' . $item['id']) ?>" class="btn btn-warning" title="Modifier">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($canDelete): ?>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $item['id'] ?>" title="Supprimer">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($canDelete): ?>
                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirmer la suppression</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Êtes-vous sûr de vouloir supprimer l'actualité <strong><?= $this->escape($item['titre']) ?></strong> ? Cette action est irréversible.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <form action="<?= $this->url('news/delete/' . $item['id']) ?>" method="post">
                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
            <nav aria-label="Pagination des actualités" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($pagination['currentPage'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $this->url('news', ['page' => $pagination['currentPage'] - 1]) ?>">
                                <i class="fa fa-angle-left"></i> Précédent
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fa fa-angle-left"></i> Précédent</span>
                        </li>
                    <?php endif; ?>

                    <?php for($i = max(1, $pagination['currentPage'] - 2); $i <= min($pagination['totalPages'], $pagination['currentPage'] + 2); $i++): ?>
                        <li class="page-item <?= $i === $pagination['currentPage'] ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $this->url('news', ['page' => $i]) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $this->url('news', ['page' => $pagination['currentPage'] + 1]) ?>">
                                Suivant <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Suivant <i class="fa fa-angle-right"></i></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>