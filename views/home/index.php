<!-- views/home/index.php -->
<div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white text-center py-5 rounded">
        <h1 class="display-4">Association Recherche et Innovation</h1>
        <p class="lead">École Supérieure de Technologie de Safi - Université Cadi Ayyad</p>
        <?php if (!$auth->isLoggedIn()): ?>
            <div class="mt-4">
                <a href="<?php echo $this->url('login'); ?>" class="btn btn-light me-2">Se connecter</a>
                <a href="<?php echo $this->url('register'); ?>" class="btn btn-outline-light">S'inscrire</a>
            </div>
        <?php else: ?>
            <div class="mt-4">
                <a href="<?php echo $this->url('projects'); ?>" class="btn btn-light me-2">Nos projets</a>
                <a href="<?php echo $this->url('events'); ?>" class="btn btn-outline-light">Événements</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Latest News Section -->
    <section class="latest-news mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Actualités récentes</h2>
            <a href="<?php echo $this->url('news'); ?>" class="btn btn-outline-primary">Voir toutes les actualités</a>
        </div>

        <div class="row">
            <?php if (empty($latestNews)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Aucune actualité disponible pour le moment.</div>
                </div>
            <?php else: ?>
                <?php foreach ($latestNews as $news): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($news['imageUrl'])): ?>
                                <img src="<?php echo $this->escape($news['imageUrl']); ?>" class="card-img-top" alt="<?php echo $this->escape($news['titre']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $this->escape($news['titre']); ?></h5>
                                <p class="card-text"><?php echo $this->truncate($news['contenu'], 100); ?></p>
                            </div>
                            <div class="card-footer bg-white">
                                <small class="text-muted">Publié le <?php echo $this->formatDate($news['datePublication']); ?></small>
                                <a href="<?php echo $this->url('news/' . $news['id']); ?>" class="btn btn-sm btn-primary float-end">Lire plus</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="upcoming-events mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Événements à venir</h2>
            <a href="<?php echo $this->url('events'); ?>" class="btn btn-outline-primary">Voir tous les événements</a>
        </div>

        <div class="row">
            <?php if (empty($upcomingEvents)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Aucun événement à venir pour le moment.</div>
                </div>
            <?php else: ?>
                <?php foreach ($upcomingEvents as $event): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <span class="badge bg-light text-dark"><?php echo $this->escape($event['eventType']); ?></span>
                                <span class="float-end"><?php echo $this->formatDate($event['eventDate'], 'd/m/Y'); ?></span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $this->escape($event['titre']); ?></h5>
                                <p class="card-text"><?php echo $this->truncate($event['description'], 100); ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo $this->escape($event['lieu']); ?></p>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="<?php echo $this->url('events/' . $event['id']); ?>" class="btn btn-sm btn-primary">Voir les détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Latest Publications Section -->
    <section class="latest-publications mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Publications récentes</h2>
            <a href="<?php echo $this->url('publications'); ?>" class="btn btn-outline-primary">Voir toutes les publications</a>
        </div>

        <div class="row">
            <?php if (empty($latestPublications)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Aucune publication disponible pour le moment.</div>
                </div>
            <?php else: ?>
                <?php foreach ($latestPublications as $publication): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <span class="badge bg-secondary"><?php echo $this->escape($publication['type']); ?></span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $this->escape($publication['titre']); ?></h5>
                                <p class="card-text"><?php echo $this->truncate($publication['contenu'], 100); ?></p>
                            </div>
                            <div class="card-footer bg-white">
                                <small class="text-muted">Par <?php echo $this->escape($publication['auteurPrenom'] . ' ' . $publication['auteurNom']); ?></small>
                                <a href="<?php echo $this->url('publications/' . $publication['id']); ?>" class="btn btn-sm btn-primary float-end">Lire plus</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>