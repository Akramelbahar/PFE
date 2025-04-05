<!-- views/home/index.php -->
<style>
    :root {
        --uca-blue: #005BAC;
        --hover-blue: #004a8c;
        --light-gray: #f8f9fa;
    }

    .hero-section {
        background-color: var(--uca-blue);
        border-radius: 10px;
        padding: 3rem 0;
        margin-bottom: 2rem;
    }

    .section-title {
        color: var(--uca-blue);
        text-align: center;
        margin: 3rem 0 2rem;
        position: relative;
        padding-bottom: 10px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 3px;
        background: var(--uca-blue);
    }

    .carousel-container {
        margin: 0 auto;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .card {
        border: none;
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .card-img-container {
        height: 200px;
        overflow: hidden;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .card-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .card:hover .card-img-container img {
        transform: scale(1.05);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-footer {
        background: transparent;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }

    .event-date {
        display: inline-block;
        background: var(--uca-blue);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .event-location {
        display: flex;
        align-items: center;
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .event-location i {
        margin-right: 0.5rem;
    }

    .publication-type {
        display: inline-block;
        background: #6c757d;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 5px;
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
    }

    .author-info {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .author-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 0.75rem;
    }

    .author-info .name {
        font-weight: 500;
        margin-bottom: 0;
        line-height: 1.2;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .section-divider {
        height: 3px;
        background: linear-gradient(to right, var(--uca-blue), transparent);
        margin: 3rem 0;
    }

    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section text-white text-center">
    <div class="container">
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
    </div>
</section>

<!-- Main Carousel -->
<div class="container">
    <div class="carousel-container">
        <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="<?php echo $this->url('/public/images/EST.jpg'); ?>" class="d-block w-100" alt="École Supérieure de Technologie">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Centre de Recherche</h5>
                        <p>Découvrez nos initiatives de recherche innovantes</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="<?php echo $this->url('/public/images/EST.png'); ?>" class="d-block w-100" alt="Recherche et Innovation">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Conférences Scientifiques</h5>
                        <p>Participez à nos événements académiques</p>
                    </div>
                </div>

            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
</div>

<!-- Latest News Section -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title">Actualités Récentes</h2>
        <a href="<?php echo $this->url('news'); ?>" class="btn btn-outline-primary">Voir toutes les actualités</a>
    </div>

    <div class="row g-4">
        <?php if (empty($latestNews)): ?>
            <div class="col-12">
                <div class="alert alert-info">Aucune actualité disponible pour le moment.</div>
            </div>
        <?php else: ?>
            <?php foreach ($latestNews as $news): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-img-container">
                            <?php if (!empty($news['mediaUrl'])): ?>
                                <img src="<?php echo $this->escape($news['mediaUrl']); ?>" alt="<?php echo $this->escape($news['titre']); ?>">
                            <?php else: ?>
                                <img src="<?php echo $this->url('/public/images/news-placeholder.jpg'); ?>" alt="News">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $this->escape($news['titre']); ?></h5>
                            <div class="author-info">
                                <img src="<?php echo $this->url('/public/images/avatar.png'); ?>" alt="Auteur">
                                <div>
                                    <p class="name"><?php echo isset($news['auteurPrenom']) ? $this->escape($news['auteurPrenom'] . ' ' . $news['auteurNom']) : 'Administrateur'; ?></p>
                                    <small class="text-muted"><?php echo $this->formatDate($news['datePublication'], 'd/m/Y'); ?></small>
                                </div>
                            </div>
                            <p class="card-text"><?php echo $this->truncate($news['contenu'], 120); ?></p>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo $this->url('news/' . $news['id']); ?>" class="btn btn-primary">Lire plus</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="section-divider"></div>

<!-- Upcoming Events Section -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title">Événements à Venir</h2>
        <a href="<?php echo $this->url('events'); ?>" class="btn btn-outline-primary">Voir tous les événements</a>
    </div>

    <div class="row g-4">
        <?php if (empty($upcomingEvents)): ?>
            <div class="col-12">
                <div class="alert alert-info">Aucun événement à venir pour le moment.</div>
            </div>
        <?php else: ?>
            <?php foreach ($upcomingEvents as $event): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">


                        <div class="card-body">
                            <span class="badge bg-primary"><?php echo $this->escape($event['eventType']); ?></span>
                            <h5 class="card-title mt-2"><?php echo $this->escape($event['titre']); ?></h5>
                            <div class="event-date">
                                <i class="fas fa-calendar-alt"></i> <?php echo $this->formatDate($event['eventDate'], 'd/m/Y'); ?>
                            </div>
                            <div class="event-location">
                                <i class="fas fa-map-marker-alt"></i> <?php echo $this->escape($event['lieu']); ?>
                            </div>
                            <p class="card-text"><?php echo $this->truncate($event['description'], 120); ?></p>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo $this->url('events/' . $event['id']); ?>" class="btn btn-primary">Voir les détails</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="section-divider"></div>

<!-- Latest Publications Section -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title">Publications Récentes</h2>
        <a href="<?php echo $this->url('publications'); ?>" class="btn btn-outline-primary">Voir toutes les publications</a>
    </div>

    <div class="row g-4">
        <?php if (empty($latestPublications)): ?>
            <div class="col-12">
                <div class="alert alert-info">Aucune publication disponible pour le moment.</div>
            </div>
        <?php else: ?>
            <?php foreach ($latestPublications as $publication): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">

                        <div class="card-body">
                            <span class="publication-type"><?php echo $this->escape($publication['type']); ?></span>
                            <h5 class="card-title"><?php echo $this->escape($publication['titre']); ?></h5>
                            <div class="author-info">
                                <img src="<?php echo $this->url('/public/images/avatar.png'); ?>" alt="Auteur">
                                <div>
                                    <p class="name"><?php echo $this->escape($publication['auteurPrenom'] . ' ' . $publication['auteurNom']); ?></p>
                                    <small class="text-muted"><?php echo $this->formatDate($publication['datePublication'], 'd/m/Y'); ?></small>
                                </div>
                            </div>
                            <p class="card-text"><?php echo $this->truncate($publication['contenu'], 120); ?></p>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo $this->url('publications/' . $publication['id']); ?>" class="btn btn-primary">Lire plus</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
