<!-- views/evenements/workshops.php -->
<div class="workshops-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ateliers</h1>
        <?php if ($auth->hasPermission('register_event')): ?>
            <a href="<?php echo $this->url('events/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvel atelier
            </a>
        <?php endif; ?>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (empty($workshops)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Aucun atelier programmé pour le moment.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($workshops as $workshop): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Atelier</h5>
                            <small>
                                <?php echo $this->formatDate($workshop['dateDebut'], 'd/m/Y'); ?>
                                -
                                <?php echo $this->formatDate($workshop['dateFin'], 'd/m/Y'); ?>
                            </small>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $this->escape($workshop['titre']); ?></h5>
                            <p class="card-text">
                                <?php echo $this->truncate($workshop['description'], 100); ?>
                            </p>
                            <div class="mt-2">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <span class="text-muted"><?php echo $this->escape($workshop['lieu']); ?></span>
                            </div>
                            <?php if (!empty($workshop['instructorName'])): ?>
                                <div class="mt-2">
                                    <i class="fas fa-chalkboard-teacher text-muted me-2"></i>
                                    <span class="text-muted">
                                        Animé par <?php echo $this->escape($workshop['instructorName']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Par <?php echo $this->escape($workshop['createurPrenom'] . ' ' . $workshop['createurNom']); ?>
                            </small>
                            <a href="<?php echo $this->url('events/' . $workshop['id']); ?>" class="btn btn-sm btn-outline-warning">
                                Détails
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (count($workshops) > 9): ?>
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