<!-- views/evenements/conferences.php -->
<div class="conferences-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Conférences</h1>
        <?php if ($auth->hasPermission('register_event')): ?>
            <a href="<?php echo $this->url('events/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle conférence
            </a>
        <?php endif; ?>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (empty($conferences)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Aucune conférence programmée pour le moment.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($conferences as $conference): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Conférence</h5>
                            <small>
                                <?php echo $this->formatDate($conference['dateDebut'], 'd/m/Y'); ?>
                                -
                                <?php echo $this->formatDate($conference['dateFin'], 'd/m/Y'); ?>
                            </small>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $this->escape($conference['titre']); ?></h5>
                            <p class="card-text">
                                <?php echo $this->truncate($conference['description'], 100); ?>
                            </p>
                            <div class="mt-2">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <span class="text-muted"><?php echo $this->escape($conference['lieu']); ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Par <?php echo $this->escape($conference['createurPrenom'] . ' ' . $conference['createurNom']); ?>
                            </small>
                            <a href="<?php echo $this->url('events/' . $conference['id']); ?>" class="btn btn-sm btn-outline-success">
                                Détails
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (count($conferences) > 9): ?>
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