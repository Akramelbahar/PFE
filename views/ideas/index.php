<!-- views/ideas/index.php -->
<div class="ideas-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Idées de Recherche</h1>
        <a href="<?php echo $this->url('ideas/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle idée
        </a>
    </div>

    <!-- Ideas List -->
    <?php if (empty($ideas)): ?>
        <div class="alert alert-info">
            Aucune idée de recherche trouvée. Veuillez <a href="<?php echo $this->url('ideas/create'); ?>">proposer une nouvelle idée</a>.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($ideas as $idea): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?php echo $this->escape($idea['titre']); ?></h5>
                            <span class="badge <?php
                            switch($idea['status']) {
                                case 'en attente':
                                    echo 'bg-secondary';
                                    break;
                                case 'approuvée':
                                    echo 'bg-success';
                                    break;
                                case 'refusé':
                                    echo 'bg-danger';
                                    break;
                                default:
                                    echo 'bg-primary';
                            }
                            ?>"><?php echo $this->escape($idea['status']); ?></span>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo $this->truncate($idea['description'], 150); ?></p>

                            <?php if (!empty($idea['proposerNom']) || !empty($idea['proposerPrenom'])): ?>
                                <div class="mb-3">
                                    <small class="text-muted">Proposé par:</small>
                                    <div class="d-flex align-items-center mt-1">
                                        <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <?php echo strtoupper(substr($idea['proposerPrenom'] ?? '', 0, 1) . substr($idea['proposerNom'] ?? '', 0, 1)); ?>
                                        </span>
                                        <span><?php echo $this->escape(($idea['proposerPrenom'] ?? '') . ' ' . ($idea['proposerNom'] ?? '')); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-2">
                                <small class="text-muted">Proposé le:</small>
                                <span><?php echo $this->formatDate($idea['dateProposition'], 'd/m/Y'); ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="<?php echo $this->url('ideas/' . $idea['id']); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Détails
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>