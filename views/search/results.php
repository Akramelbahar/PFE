<!-- views/search/results.php -->
<div class="search-results">
    <div class="container py-4">
        <!-- Search Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3">Résultats de recherche</h1>
                <p class="text-muted">
                    <?php echo count($results['total']); ?> résultat(s) trouvé(s) pour
                    <strong>"<?php echo $this->escape($query); ?>"</strong>
                </p>

                <!-- Search Form -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form action="<?php echo $this->url('search'); ?>" method="get" class="row g-2">
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" name="q" class="form-control border-start-0"
                                           value="<?php echo $this->escape($query); ?>"
                                           placeholder="Rechercher dans toute la plateforme...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Rechercher</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Navigation Tabs -->
        <ul class="nav nav-pills mb-4" id="searchTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
                        type="button" role="tab" aria-controls="all" aria-selected="true">
                    Tous <span class="badge bg-secondary ms-1"><?php echo count($results['total']); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects"
                        type="button" role="tab" aria-controls="projects" aria-selected="false">
                    Projets <span class="badge bg-secondary ms-1"><?php echo count($results['projects']); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="publications-tab" data-bs-toggle="tab" data-bs-target="#publications"
                        type="button" role="tab" aria-controls="publications" aria-selected="false">
                    Publications <span class="badge bg-secondary ms-1"><?php echo count($results['publications']); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events"
                        type="button" role="tab" aria-controls="events" aria-selected="false">
                    Événements <span class="badge bg-secondary ms-1"><?php echo count($results['events']); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="news-tab" data-bs-toggle="tab" data-bs-target="#news"
                        type="button" role="tab" aria-controls="news" aria-selected="false">
                    Actualités <span class="badge bg-secondary ms-1"><?php echo count($results['news']); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ideas-tab" data-bs-toggle="tab" data-bs-target="#ideas"
                        type="button" role="tab" aria-controls="ideas" aria-selected="false">
                    Idées <span class="badge bg-secondary ms-1"><?php echo count($results['ideas']); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users"
                        type="button" role="tab" aria-controls="users" aria-selected="false">
                    Utilisateurs <span class="badge bg-secondary ms-1"><?php echo count($results['users']); ?></span>
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="searchTabsContent">
            <!-- All Results Tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <?php if (empty($results['total'])): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucun résultat trouvé. Essayez d'autres termes de recherche.
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($results['total'] as $item): ?>
                            <a href="<?php echo $this->url($item['url']); ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-1"><?php echo $this->escape($item['title']); ?></h5>
                                    <span class="badge bg-<?php echo $item['type_color']; ?>"><?php echo $this->escape($item['type_label']); ?></span>
                                </div>
                                <p class="mb-1 text-muted"><?php echo $this->escape($item['excerpt']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i> <?php echo $this->escape($item['author']); ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i> <?php echo $this->formatDate($item['date']); ?>
                                    </small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Projects Tab -->
            <div class="tab-pane fade" id="projects" role="tabpanel" aria-labelledby="projects-tab">
                <?php if (empty($results['projects'])): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucun projet trouvé. Essayez d'autres termes de recherche.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($results['projects'] as $project): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title">
                                                <a href="<?php echo $this->url('projects/' . $project['id']); ?>" class="text-decoration-none">
                                                    <?php echo $this->escape($project['titre']); ?>
                                                </a>
                                            </h5>
                                            <?php if (isset($project['status'])): ?>
                                                <span class="badge bg-<?php
                                                echo $project['status'] === 'En cours' ? 'success' :
                                                    ($project['status'] === 'En préparation' ? 'info' :
                                                        ($project['status'] === 'Terminé' ? 'primary' : 'secondary'));
                                                ?>">
                                                    <?php echo $this->escape($project['status']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="card-text text-muted">
                                            <?php echo $this->escape($this->truncate($project['description'], 120)); ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i> <?php echo isset($project['chefPrenom']) ? $this->escape($project['chefPrenom'] . ' ' . $project['chefNom']) : 'N/A'; ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i> <?php echo $this->formatDate($project['dateDebut']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Publications Tab -->
            <div class="tab-pane fade" id="publications" role="tabpanel" aria-labelledby="publications-tab">
                <?php if (empty($results['publications'])): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucune publication trouvée. Essayez d'autres termes de recherche.
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($results['publications'] as $publication): ?>
                            <a href="<?php echo $this->url('publications/' . $publication['id']); ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-1"><?php echo $this->escape($publication['titre']); ?></h5>
                                    <span class="badge bg-<?php
                                    echo $publication['type'] === 'Article' ? 'info' :
                                        ($publication['type'] === 'Livre' ? 'primary' :
                                            ($publication['type'] === 'Chapitre' ? 'warning' : 'secondary'));
                                    ?>">
                                        <?php echo $this->escape($publication['type']); ?>
                                    </span>
                                </div>
                                <p class="mb-1 text-muted"><?php echo $this->escape($this->truncate($publication['contenu'], 150)); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i> <?php echo isset($publication['auteurPrenom']) ? $this->escape($publication['auteurPrenom'] . ' ' . $publication['auteurNom']) : 'N/A'; ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i> <?php echo $this->formatDate($publication['datePublication']); ?>
                                    </small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Events Tab -->
            <div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="events-tab">
                <?php if (empty($results['events'])): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucun événement trouvé. Essayez d'autres termes de recherche.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($results['events'] as $event): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title">
                                                <a href="<?php echo $this->url('events/' . $event['id']); ?>" class="text-decoration-none">
                                                    <?php echo $this->escape($event['titre']); ?>
                                                </a>
                                            </h5>
                                            <span class="badge bg-<?php
                                            echo $event['type'] === 'Seminaire' ? 'info' :
                                                ($event['type'] === 'Conference' ? 'primary' :
                                                    ($event['type'] === 'Workshop' ? 'warning' : 'secondary'));
                                            ?>">
                                                <?php echo $this->escape($event['type']); ?>
                                            </span>
                                        </div>
                                        <p class="card-text"><?php echo $this->escape($this->truncate($event['description'], 120)); ?></p>
                                        <?php if (isset($event['lieu']) && !empty($event['lieu'])): ?>
                                            <div class="mb-2">
                                                <i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo $this->escape($event['lieu']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                <?php echo isset($event['eventDate']) ? $this->formatDate($event['eventDate']) : $this->formatDate($event['dateCreation']); ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo isset($event['createurPrenom']) ? $this->escape($event['createurPrenom'] . ' ' . $event['createurNom']) : 'N/A'; ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- News Tab -->
            <div class="tab-pane fade" id="news" role="tabpanel" aria-labelledby="news-tab">
                <?php if (empty($results['news'])): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucune actualité trouvée. Essayez d'autres termes de recherche.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($results['news'] as $news): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <?php if (!empty($news['mediaUrl'])): ?>
                                        <img src="<?php echo $this->escape($news['mediaUrl']); ?>" class="card-img-top" alt="Image de l'actualité">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="<?php echo $this->url('news/' . $news['id']); ?>" class="text-decoration-none">
                                                <?php echo $this->escape($news['titre']); ?>
                                            </a>
                                        </h5>
                                        <p class="card-text"><?php echo $this->escape($this->truncate($news['contenu'], 120)); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo isset($news['auteurPrenom']) ? $this->escape($news['auteurPrenom'] . ' ' . $news['auteurNom']) : 'N/A'; ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i> <?php echo $this->formatDate($news['datePublication']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Ideas Tab -->
            <div class="tab-pane fade" id="ideas" role="tabpanel" aria-labelledby="ideas-tab">
                <?php if (empty($results['ideas'])): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucune idée trouvée. Essayez d'autres termes de recherche.
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($results['ideas'] as $idea): ?>
                            <a href="<?php echo $this->url('ideas/' . $idea['id']); ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-1"><?php echo $this->escape($idea['titre']); ?></h5>
                                    <span class="badge bg-<?php
                                    echo $idea['status'] === 'en attente' ? 'warning' :
                                        ($idea['status'] === 'approuvée' ? 'success' : 'secondary');
                                    ?>">
                                        <?php echo $this->escape($idea['status']); ?>
                                    </span>
                                </div>
                                <p class="mb-1 text-muted"><?php echo $this->escape($this->truncate($idea['description'], 150)); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        <?php echo isset($idea['proposerPrenom']) ? $this->escape($idea['proposerPrenom'] . ' ' . $idea['proposerNom']) : 'N/A'; ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i> <?php echo $this->formatDate($idea['dateProposition']); ?>
                                    </small>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Users Tab -->
            <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                <?php if (empty($results['users'])): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Aucun utilisateur trouvé. Essayez d'autres termes de recherche.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($results['users'] as $user): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <?php if (isset($user['profilePicture']) && !empty($user['profilePicture'])): ?>
                                                <img src="<?php echo $this->escape($user['profilePicture']); ?>" class="rounded-circle" width="80" height="80" alt="Photo de profil">
                                            <?php else: ?>
                                                <div class="avatar rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px;">
                                                    <span style="font-size: 2rem;">
                                                        <?php echo substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1); ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <h5 class="card-title">
                                            <a href="<?php echo $this->url('users/' . $user['id']); ?>" class="text-decoration-none">
                                                <?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?>
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted"><?php echo $this->escape($user['email']); ?></p>
                                        <div class="mb-2">
                                            <span class="badge bg-<?php
                                            echo $user['role'] === 'Admin' ? 'danger' :
                                                ($user['role'] === 'Chercheur' ? 'primary' :
                                                    ($user['role'] === 'MembreBureauExecutif' ? 'success' : 'secondary'));
                                            ?>">
                                                <?php echo $this->escape($user['role']); ?>
                                            </span>
                                        </div>
                                        <a href="<?php echo $this->url('users/' . $user['id']); ?>" class="btn btn-sm btn-outline-primary">
                                            Voir le profil
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- No Results Message -->
        <?php if (empty($results['total']) && empty($results['projects']) && empty($results['publications'])
            && empty($results['events']) && empty($results['news']) && empty($results['ideas']) && empty($results['users'])): ?>
            <div class="text-center mt-5 mb-5">
                <div class="mb-4">
                    <i class="fas fa-search fa-5x text-muted"></i>
                </div>
                <h3>Aucun résultat trouvé</h3>
                <p class="text-muted">Nous n'avons trouvé aucun contenu correspondant à votre recherche.</p>
                <div class="mt-4">
                    <h5>Suggestions :</h5>
                    <ul class="list-unstyled">
                        <li>Vérifiez l'orthographe des termes de recherche.</li>
                        <li>Essayez d'utiliser des mots-clés plus généraux.</li>
                        <li>Essayez d'utiliser moins de mots-clés.</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Keep selected tab after page refresh
        const activeTab = localStorage.getItem('searchActiveTab');
        if (activeTab) {
            const tab = document.querySelector(activeTab);
            if (tab) {
                new bootstrap.Tab(tab).show();
            }
        }

        // Save active tab
        document.querySelectorAll('#searchTabs button').forEach(tab => {
            tab.addEventListener('click', function() {
                localStorage.setItem('searchActiveTab', '#' + this.id);
            });
        });
    });
</script>