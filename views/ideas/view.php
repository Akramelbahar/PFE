<!-- views/ideas/view.php -->
<div class="idea-view-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $this->escape($idea['titre']); ?></h1>
        <div>
            <a href="<?php echo $this->url('ideas'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux idées
            </a>

            <?php if ($auth->isLoggedIn() && ($idea['proposePar'] == $auth->getUser()['id'] || $auth->hasRole(['admin', 'membreBureauExecutif']))): ?>
                <a href="<?php echo $this->url('ideas/edit/' . $idea['id']); ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            <?php endif; ?>

            <?php if ($auth->hasRole(['admin', 'membreBureauExecutif'])): ?>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#statusModal">
                    <i class="fas fa-sync-alt"></i> Changer statut
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-4">
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
        ?> fs-6"><?php echo $this->escape($idea['status']); ?></span>

        <span class="ms-3 text-muted">
            Proposée le <?php echo $this->formatDate($idea['dateProposition']); ?>
        </span>
    </div>

    <div class="row">
        <!-- Main Idea Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Description</h5>
                </div>
                <div class="card-body">
                    <?php echo nl2br($this->escape($idea['description'])); ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Proposer Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Proposé par</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                            <?php echo strtoupper(substr($idea['proposerPrenom'] ?? '', 0, 1) . substr($idea['proposerNom'] ?? '', 0, 1)); ?>
                        </span>
                        <div>
                            <h6 class="mb-0"><?php echo $this->escape(($idea['proposerPrenom'] ?? '') . ' ' . ($idea['proposerNom'] ?? '')); ?></h6>
                            <?php if (!empty($idea['proposePar'])): ?>
                                <a href="<?php echo $this->url('users/' . $idea['proposePar']); ?>">Voir le profil</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Change Modal for Admins and Board Members -->
<?php if ($auth->hasRole(['admin', 'membreBureauExecutif'])): ?>
    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Changer le statut de l'idée</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo $this->url('ideas/update-status/' . $idea['id']); ?>" method="post">
                    <?php echo CSRF::tokenField(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Nouveau statut</label>
                            <select id="status" name="status" class="form-select">
                                <option value="en attente" <?php echo $idea['status'] === 'en attente' ? 'selected' : ''; ?>>En attente</option>
                                <option value="approuvée" <?php echo $idea['status'] === 'approuvée' ? 'selected' : ''; ?>>Approuvée</option>
                                <option value="refusé" <?php echo $idea['status'] === 'refusé' ? 'selected' : ''; ?>>Refusée</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>