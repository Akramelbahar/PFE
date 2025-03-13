<!-- views/evenements/seminaires.php -->
<div class="seminaires-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Séminaires</h1>
        <?php if ($auth->hasPermission('register_event')): ?>
            <a href="<?php echo $this->url('events/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau séminaire
            </a>
        <?php endif; ?>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (empty($seminaires)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Aucun séminaire programmé pour le moment.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($seminaires as $seminaire): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Séminaire</h5>
                            <small><?php echo $this->formatDate($seminaire['date'], 'd/m/Y'); ?></small>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $this->escape($seminaire['titre']); ?></h5>
                            <p class="card-text">
                                <?php echo $this->truncate($seminaire['description'], 100); ?>
                            </p>
                            <div class="mt-2">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <span class="text-muted"><?php echo $this->escape($seminaire['lieu']); ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Par <?php echo $this->escape($seminaire['createurPrenom'] . ' ' . $seminaire['createurNom']); ?>
                            </small>
                            <a href="<?php echo $this->url('events/' . $seminaire['id']); ?>" class="btn btn-sm btn-outline-info">
                                Détails
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (count($seminaires) > 9): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination">
                    <li class="page-item disabled">
                        <span class="page-link">Pagination à implémenter</span>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>