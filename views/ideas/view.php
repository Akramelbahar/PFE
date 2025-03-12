<!-- views/ideas/view.php -->
<div class="idea-view-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $this->escape($idea['titre']); ?></h1>
        <div>
            <a href="<?php echo $this->url('ideas'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux idées
            </a>

            <?php if ($auth->isLoggedIn() && ($idea['proposePar'] == $auth->getUser()['id'] || $auth->hasPermission('edit_idea'))): ?>
                <?php if ($idea['status'] !== 'Approuvée' && $idea['status'] !== 'Rejetée'): ?>
                    <a href="<?php echo $this->url('ideas/edit/' . $idea['id']); ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($auth->hasPermission('delete_idea') || ($auth->hasPermission('delete_own_idea') && $idea['proposePar'] == $auth->getUser()['id'])): ?>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteIdeaModal">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-4">
        <?php
        $statusBadgeClass = 'bg-secondary';
        $statusIcon = 'fas fa-info-circle';

        switch($idea['status']) {
            case 'Soumise':
                $statusBadgeClass = 'bg-info';
                $statusIcon = 'fas fa-file-import';
                break;
            case 'En évaluation':
                $statusBadgeClass = 'bg-warning text-dark';
                $statusIcon = 'fas fa-tasks';
                break;
            case 'Approuvée':
                $statusBadgeClass = 'bg-success';
                $statusIcon = 'fas fa-check-circle';
                break;
            case 'Rejetée':
                $statusBadgeClass = 'bg-danger';
                $statusIcon = 'fas fa-times-circle';
                break;
        }
        ?>
        <span class="badge <?php echo $statusBadgeClass; ?> fs-6">
            <i class="<?php echo $statusIcon; ?> me-1"></i>
            <?php echo $idea['status']; ?>
        </span>

        <?php if ($idea['status'] === 'Approuvée' && !empty($idea['projetId'])): ?>
            <a href="<?php echo $this->url('projects/' . $idea['projetId']); ?>" class="btn btn-sm btn-success ms-2">
                <i class="fas fa-project-diagram me-1"></i> Voir le projet associé
            </a>
        <?php endif; ?>
    </div>

    <!-- Idea Details -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Détails de l'idée</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="fw-bold">Description</h6>
                    <p><?php echo nl2br($this->escape($idea['description'])); ?></p>

                    <?php if (!empty($idea['objectifs'])): ?>
                        <h6 class="fw-bold mt-4">Objectifs</h6>
                        <p><?php echo nl2br($this->escape($idea['objectifs'])); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($idea['benefices'])): ?>
                        <h6 class="fw-bold mt-4">Bénéfices attendus</h6>
                        <p><?php echo nl2br($this->escape($idea['benefices'])); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($idea['ressourcesNecessaires'])): ?>
                        <h6 class="fw-bold mt-4">Ressources nécessaires</h6>
                        <p><?php echo nl2br($this->escape($idea['ressourcesNecessaires'])); ?></p>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <h6 class="fw-bold">Proposé par</h6>
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <?php echo strtoupper(substr($idea['proposerPrenom'], 0, 1) . substr($idea['proposerNom'], 0, 1)); ?>
                        </span>
                        <a href="<?php echo $this->url('users/' . $idea['proposePar']); ?>">
                            <?php echo $this->escape($idea['proposerPrenom'] . ' ' . $idea['proposerNom']); ?>
                        </a>
                    </div>

                    <h6 class="fw-bold">Domaine</h6>
                    <p><?php echo $this->escape($idea['domaine']); ?></p>

                    <h6 class="fw-bold">Date de proposition</h6>
                    <p><?php echo $this->formatDate($idea['dateProposition']); ?></p>
                </div>
            </div>

            <!-- Evaluation Section -->
            <?php if (!empty($idea['commentaire']) || $idea['status'] === 'Approuvée' || $idea['status'] === 'Rejetée'): ?>
                <div class="mt-4 pt-4 border-top">
                    <h5>Évaluation</h5>

                    <?php if (!empty($idea['commentaire'])): ?>
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Commentaire de l'évaluateur</h6>
                                <p class="card-text"><?php echo nl2br($this->escape($idea['commentaire'])); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($idea['evaluateurId']) && isset($idea['dateEvaluation'])): ?>
                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span>
                                <?php if (isset($evaluateur)): ?>
                                    Évalué par <?php echo $this->escape($evaluateur['prenom'] . ' ' . $evaluateur['nom']); ?>
                                <?php endif; ?>
                            </span>
                            <span>
                                <?php echo $this->formatDate($idea['dateEvaluation']); ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Admin Evaluation Button -->
            <?php if ($auth->hasPermission('approve_idea') && ($idea['status'] === 'Soumise' || $idea['status'] === 'En évaluation')): ?>
                <div class="mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#evaluateModal">
                        <i class="fas fa-clipboard-check me-1"></i> Évaluer cette idée
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Documents Section -->
    <?php if (!empty($documents)): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Documents</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php foreach ($documents as $document): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php
                                    $iconClass = 'fa-file';
                                    switch($document['extension']) {
                                        case 'pdf':
                                            $iconClass = 'fa-file-pdf';
                                            break;
                                        case 'doc':
                                        case 'docx':
                                            $iconClass = 'fa-file-word';
                                            break;
                                        case 'jpg':
                                        case 'jpeg':
                                        case 'png':
                                            $iconClass = 'fa-file-image';
                                            break;
                                    }
                                    ?>
                                    <i class="fas <?php echo $iconClass; ?> me-2"></i>
                                    <a href="<?php echo $this->escape($document['url']); ?>" target="_blank">
                                        <?php echo $this->escape($document['filename']); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Evaluation Modal -->
    <?php if ($auth->hasPermission('approve_idea')): ?>
        <div class="modal fade" id="evaluateModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="<?php echo $this->url('ideas/update-status/' . $idea['id']); ?>" method="post">
                        <?php echo CSRF::tokenField(); ?>
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Évaluer l'idée de recherche</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Nouveau statut</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Sélectionnez un statut</option>
                                    <option value="En évaluation" <?php echo $idea['status'] === 'En évaluation' ? 'selected' : ''; ?>>En évaluation</option>
                                    <option value="Approuvée">Approuvée</option>
                                    <option value="Rejetée">Rejetée</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="commentaire" class="form-label">Commentaire d'évaluation</label>
                                <textarea class="form-control" id="commentaire" name="commentaire" rows="5" placeholder="Expliquez votre décision..."><?php echo isset($idea['commentaire']) ? $this->escape($idea['commentaire']) : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer l'évaluation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Delete Modal -->
    <?php if ($auth->hasPermission('delete_idea') || ($auth->hasPermission('delete_own_idea') && $idea['proposePar'] == $auth->getUser()['id'])): ?>
        <div class="modal fade" id="deleteIdeaModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer cette idée de recherche?</p>
                        <p><strong><?php echo $this->escape($idea['titre']); ?></strong></p>
                        <p>Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="<?php echo $this->url('ideas/delete/' . $idea['id']); ?>" method="post">
                            <?php echo CSRF::tokenField(); ?>
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>