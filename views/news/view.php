<!-- views/news/view.php -->
<style>
    .news-header {
        background-color: var(--uca-blue);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
    }

    .news-content {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .news-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .news-details {
        padding: 2rem;
    }

    .news-meta {
        display: flex;
        align-items: center;
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    .news-meta i {
        margin-right: 0.5rem;
    }

    .news-meta .separator {
        margin: 0 0.75rem;
    }

    .related-news-card {
        display: flex;
        margin-bottom: 1rem;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .related-news-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .related-news-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .related-news-placeholder {
        width: 100px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        flex-shrink: 0;
    }

    .related-news-content {
        padding: 0.75rem 1rem;
        width: 100%;
    }

    .sidebar-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .sidebar-card .card-header {
        background-color: rgba(0, 91, 172, 0.05);
        border-bottom: 1px solid rgba(0, 91, 172, 0.1);
        padding: 1rem 1.5rem;
    }

    .share-buttons {
        display: flex;
        justify-content: space-around;
    }

    .share-buttons a {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    .share-buttons a:hover {
        transform: translateY(-3px);
    }

    .author-box {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .author-avatar {
        width: 60px;
        height: 60px;
        background-color: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }

    .event-info {
        margin-bottom: 0.5rem;
    }

    .event-info i {
        width: 20px;
        margin-right: 0.5rem;
    }
</style>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $this->url(''); ?>">Accueil</a></li>
            <li class="breadcrumb-item"><a href="<?php echo $this->url('news'); ?>">Actualités</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $this->escape($news['titre']); ?></li>
        </ol>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($flash) && $flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Main Content -->
            <div class="news-content">
                <?php if (!empty($news['mediaUrl'])): ?>
                    <img src="<?php echo $this->url($news['mediaUrl']); ?>" class="news-image" alt="<?php echo $this->escape($news['titre']); ?>">
                <?php endif; ?>

                <div class="news-details">
                    <h1 class="h2 mb-3"><?php echo $this->escape($news['titre']); ?></h1>

                    <div class="news-meta">
                        <i class="far fa-calendar-alt"></i>
                        <span><?php echo $this->formatDate($news['datePublication']); ?></span>
                        <span class="separator">|</span>
                        <i class="far fa-user"></i>
                        <span><?php echo $this->escape($news['auteurPrenom'] . ' ' . $news['auteurNom']); ?></span>
                    </div>

                    <?php if (!empty($news['evenementId']) && isset($relatedEvent) && $relatedEvent): ?>
                        <div class="alert alert-info mb-4">
                            <h5><i class="fas fa-calendar-alt me-2"></i> Événement associé</h5>
                            <p class="mb-2">
                                <strong><?php echo $this->escape($relatedEvent['titre']); ?></strong>
                                <?php if (isset($relatedEvent['type'])): ?>
                                    <span class="badge bg-primary ms-2"><?php echo $this->escape($relatedEvent['type']); ?></span>
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($relatedEvent['lieu'])): ?>
                                <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i> <?php echo $this->escape($relatedEvent['lieu']); ?></p>
                            <?php endif; ?>
                            <a href="<?php echo $this->url('events/' . $relatedEvent['id']); ?>" class="btn btn-sm btn-primary mt-2">
                                <i class="fas fa-external-link-alt me-1"></i> Voir les détails de l'événement
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="news-body mb-4">
                        <?php echo $news['contenu']; ?>
                    </div>

                    <?php
                    // Only show edit/delete buttons if auth exists and is properly set up
                    if (isset($auth) && $auth):
                        // Try to check if the user is the author
                        $isAuthor = false;
                        if (isset($news['auteurId']) && $auth->getUser() && isset($auth->getUser()['id'])) {
                            $isAuthor = $news['auteurId'] == $auth->getUser()['id'];
                        }

                        // Determine permissions
                        $canEdit = $isAuthor ?
                            $auth->hasPermission('edit_own_news') :
                            $auth->hasPermission('edit_news');

                        $canDelete = $isAuthor ?
                            $auth->hasPermission('delete_own_news') :
                            $auth->hasPermission('delete_news');

                        if ($canEdit || $canDelete):
                            ?>
                            <div class="d-flex justify-content-end mt-4">
                                <?php if ($canEdit): ?>
                                    <a href="<?php echo $this->url('news/edit/' . $news['id']); ?>" class="btn btn-warning me-2">
                                        <i class="fas fa-edit me-1"></i> Modifier
                                    </a>
                                <?php endif; ?>

                                <?php if ($canDelete): ?>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash-alt me-1"></i> Supprimer
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php
                        endif;
                    endif;
                    ?>
                </div>
            </div>

            <!-- Related News -->
            <?php if (!empty($relatedNews)): ?>
                <div class="sidebar-card">
                    <div class="card-header">
                        <h3 class="h5 mb-0">Articles similaires</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($relatedNews as $item): ?>
                                <div class="col-md-6 mb-3">
                                    <a href="<?php echo $this->url('news/' . $item['id']); ?>" class="text-decoration-none text-dark">
                                        <div class="related-news-card">
                                            <?php if (!empty($item['mediaUrl'])): ?>
                                                <img src="<?php echo $this->url($item['mediaUrl']); ?>" alt="<?php echo $this->escape($item['titre']); ?>" class="related-news-img">
                                            <?php else: ?>
                                                <div class="related-news-placeholder">
                                                    <i class="far fa-newspaper text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="related-news-content">
                                                <h4 class="h6 mb-1"><?php echo $this->escape($item['titre']); ?></h4>
                                                <small class="text-muted"><?php echo $this->formatDate($item['datePublication'], 'd/m/Y'); ?></small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Sidebar -->

            <!-- Author Info -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h3 class="h5 mb-0">À propos de l'auteur</h3>
                </div>
                <div class="card-body">
                    <div class="author-box">
                        <div class="author-avatar">
                            <i class="fas fa-user fa-2x text-muted"></i>
                        </div>
                        <div>
                            <h4 class="h6 mb-1"><?php echo $this->escape($news['auteurPrenom'] . ' ' . $news['auteurNom']); ?></h4>
                            <?php if (isset($auth) && $auth && $auth->hasPermission('view_users')): ?>
                                <a href="<?php echo $this->url('users/view/' . $news['auteurId']); ?>" class="btn btn-sm btn-outline-secondary">
                                    Voir le profil
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (isset($auth) && $auth && $auth->hasPermission('view_news')): ?>
                        <a href="<?php echo $this->url('news', ['author' => $news['auteurId']]); ?>" class="btn btn-sm btn-outline-primary">
                            Voir toutes les actualités de cet auteur
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Related Event -->
            <?php if (!empty($news['evenementId']) && isset($relatedEvent) && $relatedEvent): ?>
                <div class="sidebar-card">
                    <div class="card-header">
                        <h3 class="h5 mb-0">Détails de l'événement</h3>
                    </div>
                    <div class="card-body">
                        <h4 class="h6 mb-3"><?php echo $this->escape($relatedEvent['titre']); ?></h4>

                        <?php if (isset($relatedEvent['type'])): ?>
                            <p class="event-info mb-2">
                                <i class="fas fa-tag"></i>
                                <?php echo $this->escape($relatedEvent['type']); ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($relatedEvent['dateDebut'])): ?>
                            <p class="event-info mb-2">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo $this->formatDate($relatedEvent['dateDebut'], 'd/m/Y'); ?>
                                <?php if (!empty($relatedEvent['dateFin']) && $relatedEvent['dateDebut'] != $relatedEvent['dateFin']): ?>
                                    - <?php echo $this->formatDate($relatedEvent['dateFin'], 'd/m/Y'); ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($relatedEvent['lieu'])): ?>
                            <p class="event-info mb-2">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo $this->escape($relatedEvent['lieu']); ?>
                            </p>
                        <?php endif; ?>

                        <a href="<?php echo $this->url('events/' . $relatedEvent['id']); ?>" class="btn btn-primary btn-sm mt-3">
                            <i class="fas fa-calendar-day me-1"></i> Voir l'événement complet
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Share Links -->
            <div class="sidebar-card">
                <div class="card-header">
                    <h3 class="h5 mb-0">Partager</h3>
                </div>
                <div class="card-body">
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($this->url('news/' . $news['id'])); ?>" target="_blank" class="btn btn-outline-primary">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($this->url('news/' . $news['id'])); ?>&text=<?php echo urlencode($news['titre']); ?>" target="_blank" class="btn btn-outline-info">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($this->url('news/' . $news['id'])); ?>&title=<?php echo urlencode($news['titre']); ?>" target="_blank" class="btn btn-outline-secondary">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="mailto:?subject=<?php echo urlencode($news['titre']); ?>&body=<?php echo urlencode($this->url('news/' . $news['id'])); ?>" class="btn btn-outline-dark">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Only show delete modal if auth exists, is properly set up, and user has permission
if (isset($auth) && $auth &&
    isset($news['auteurId']) && $auth->getUser() && isset($auth->getUser()['id'])):

    $isAuthor = $news['auteurId'] == $auth->getUser()['id'];
    $canDelete = $isAuthor ?
        $auth->hasPermission('delete_own_news') :
        $auth->hasPermission('delete_news');

    if ($canDelete):
        ?>
        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer l'actualité <strong><?php echo $this->escape($news['titre']); ?></strong> ?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Cette action est irréversible et supprimera définitivement cette actualité.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Annuler
                        </button>
                        <form action="<?php echo $this->url('news/delete/' . $news['id']); ?>" method="post">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-1"></i>Supprimer définitivement
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endif;
endif;
?>