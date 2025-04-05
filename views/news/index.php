<!-- views/news/index.php -->
<style>
    .news-header {
        background-color: var(--uca-blue);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
    }

    .filter-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .news-card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }

    .news-img-container {
        height: 200px;
        overflow: hidden;
    }

    .news-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .news-card:hover .news-img-container img {
        transform: scale(1.05);
    }

    .news-placeholder {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }

    .news-meta {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.75rem;
    }

    .news-meta i {
        margin-right: 0.35rem;
    }

    .news-meta .separator {
        margin: 0 0.5rem;
    }

    .news-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .pagination {
        margin-top: 2rem;
    }

    .pagination .page-item .page-link {
        color: var(--uca-blue);
        border-radius: 5px;
        margin: 0 3px;
    }

    .pagination .page-item.active .page-link {
        background-color: var(--uca-blue);
        border-color: var(--uca-blue);
        color: white;
    }
</style>

<div class="container py-4">
    <!-- News Header Section -->
    <div class="news-header text-center">
        <h1 class="display-5 fw-bold">Actualités</h1>
        <p class="lead">Découvrez les dernières informations et événements de notre association</p>
        <?php if ($auth->hasPermission('create_news')): ?>
            <a href="<?php echo $this->url('news/create'); ?>" class="btn btn-light mt-3">
                <i class="fas fa-plus-circle me-2"></i> Publier une nouvelle actualité
            </a>
        <?php endif; ?>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($flash) && $flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Search and Filter Card -->
    <div class="filter-card card mb-4">
        <div class="card-body py-4">
            <form action="<?php echo $this->url('news'); ?>" method="get" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label"><i class="fas fa-search me-2"></i>Recherche</label>
                    <input type="text" name="search" id="search" class="form-control form-control-lg"
                           placeholder="Rechercher par titre, contenu..."
                           value="<?php echo isset($search) ? $this->escape($search) : ''; ?>">
                </div>

                <div class="col-md-3">
                    <label for="sort" class="form-label"><i class="fas fa-sort me-2"></i>Trier par</label>
                    <select name="sort" id="sort" class="form-select form-select-lg">
                        <option value="recent" <?php echo (isset($sort) && $sort === 'recent') ? 'selected' : ''; ?>>Plus récentes</option>
                        <option value="oldest" <?php echo (isset($sort) && $sort === 'oldest') ? 'selected' : ''; ?>>Plus anciennes</option>
                        <option value="title" <?php echo (isset($sort) && $sort === 'title') ? 'selected' : ''; ?>>Titre (A-Z)</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- News Listing -->
    <?php if (empty($news)): ?>
        <div class="alert alert-info p-4 text-center" role="alert">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <h4>Aucune actualité trouvée</h4>
            <p class="mb-0">Il n'y a pas d'actualité correspondant à votre recherche pour le moment.</p>
            <?php if ($auth->hasPermission('create_news')): ?>
                <div class="mt-3">
                    <a href="<?php echo $this->url('news/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Créer une nouvelle actualité
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($news as $item): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="news-card">
                        <?php if (!empty($item['mediaUrl'])): ?>
                            <div class="news-img-container">
                                <img src="<?php echo $this->url($item['mediaUrl']); ?>" alt="<?php echo $this->escape($item['titre']); ?>">
                            </div>
                        <?php else: ?>
                            <div class="news-placeholder">
                                <i class="fas fa-newspaper fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>

                        <div class="card-body p-4">
                            <h3 class="card-title h5 mb-3">
                                <a href="<?php echo $this->url('news/' . $item['id']); ?>" class="text-decoration-none text-dark stretched-link">
                                    <?php echo $this->escape($item['titre']); ?>
                                </a>
                            </h3>

                            <div class="news-meta">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo $this->formatDate($item['datePublication'], 'd/m/Y'); ?>
                                <span class="separator">|</span>
                                <i class="far fa-user"></i>
                                <?php echo $this->escape($item['auteurPrenom'] . ' ' . $item['auteurNom']); ?>
                            </div>

                            <p class="card-text">
                                <?php echo $this->truncate(strip_tags($item['contenu']), 120); ?>
                            </p>

                            <div class="news-footer">
                                <a href="<?php echo $this->url('news/' . $item['id']); ?>" class="btn btn-sm btn-outline-primary">
                                    Lire la suite <i class="fas fa-arrow-right ms-1"></i>
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
                                            <a href="<?php echo $this->url('news/edit/' . $item['id']); ?>" class="btn btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($canDelete): ?>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $item['id']; ?>" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($canDelete) && $canDelete): ?>
                        <!-- Delete Confirmation Modal -->
                        <div class="modal fade" id="deleteModal<?php echo $item['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirmer la suppression</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Êtes-vous sûr de vouloir supprimer l'actualité <strong><?php echo $this->escape($item['titre']); ?></strong> ?</p>
                                        <p class="text-danger">Cette action est irréversible.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <form action="<?php echo $this->url('news/delete/' . $item['id']); ?>" method="post">
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
            <nav aria-label="Pagination des actualités">
                <ul class="pagination justify-content-center">
                    <!-- Previous page link -->
                    <?php if ($pagination['currentPage'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->url('news', ['page' => $pagination['currentPage'] - 1, 'search' => $search ?? '', 'sort' => $sort ?? '']); ?>">
                                <i class="fas fa-angle-left"></i> Précédent
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-angle-left"></i> Précédent</span>
                        </li>
                    <?php endif; ?>

                    <!-- Page numbers -->
                    <?php for($i = max(1, $pagination['currentPage'] - 2); $i <= min($pagination['totalPages'], $pagination['currentPage'] + 2); $i++): ?>
                        <li class="page-item <?php echo $i === $pagination['currentPage'] ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo $this->url('news', ['page' => $i, 'search' => $search ?? '', 'sort' => $sort ?? '']); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next page link -->
                    <?php if ($pagination['currentPage'] < $pagination['totalPages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo $this->url('news', ['page' => $pagination['currentPage'] + 1, 'search' => $search ?? '', 'sort' => $sort ?? '']); ?>">
                                Suivant <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Suivant <i class="fas fa-angle-right"></i></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>