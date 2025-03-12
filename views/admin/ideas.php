<!-- views/admin/ideas.php -->
<div class="ideas-management-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Idées de Recherche</h1>
        <a href="<?php echo $this->url('ideas'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux idées
        </a>
    </div>

    <!-- Status Filter -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtrer par statut</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo $this->url('admin/ideas'); ?>" class="btn <?php echo empty($currentStatus) ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    Toutes
                </a>
                <a href="<?php echo $this->url('admin/ideas', ['status' => 'Soumise']); ?>" class="btn <?php echo $currentStatus === 'Soumise' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="fas fa-file-import me-1"></i> Soumises
                </a>
                <a href="<?php echo $this->url('admin/ideas', ['status' => 'En évaluation']); ?>" class="btn <?php echo $currentStatus === 'En évaluation' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="fas fa-tasks me-1"></i> En évaluation
                </a>
                <a href="<?php echo $this->url('admin/ideas', ['status' => 'Approuvée']); ?>" class="btn <?php echo $currentStatus === 'Approuvée' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="fas fa-check-circle me-1"></i> Approuvées
                </a>
                <a href="<?php echo $this->url('admin/ideas', ['status' => 'Rejetée']); ?>" class="btn <?php echo $currentStatus === 'Rejetée' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="fas fa-times-circle me-1"></i> Rejetées
                </a>
            </div>
        </div>
    </div>

    <!-- Ideas List -->
    <?php if (empty($ideas)): ?>
        <div class="alert alert-info">
            Aucune idée de recherche trouvée avec ce statut.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Titre</th>
                    <th>Proposé par</th>
                    <th>Domaine</th>
                    <th>Date de proposition</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ideas as $idea): ?>
                    <tr>
                        <td><?php echo $this->escape($idea['titre']); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <?php echo strtoupper(substr($idea['proposerPrenom'], 0, 1) . substr($idea['proposerNom'], 0, 1)); ?>
                                    </span>
                                <?php echo $this->escape($idea['proposerPrenom'] . ' ' . $idea['proposerNom']); ?>
                            </div>
                        </td>
                        <td><?php echo $this->escape($idea['domaine']); ?></td>
                        <td><?php echo $this->formatDate($idea['dateProposition']); ?></td>
                        <td>
                            <?php
                            $statusBadgeClass = 'bg-secondary';
                            $statusIcon = 'fas fa-question-circle';

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
                            <span class="badge <?php echo $statusBadgeClass; ?>">
                                    <i class="<?php echo $statusIcon; ?> me-1"></i>
                                    <?php echo $idea['status']; ?>
                                </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo $this->url('ideas/' . $idea['id']); ?>" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Voir
                                </a>

                                <?php if ($idea['status'] === 'Soumise' || $idea['status'] === 'En évaluation'): ?>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#evaluateModal-<?php echo $idea['id']; ?>">
                                        <i class="fas fa-clipboard-check"></i> Évaluer
                                    </button>
                                <?php endif; ?>

                                <?php if ($idea['status'] === 'Approuvée' && !isset($idea['projetId']) && $auth->hasPermission('convert_idea_to_project')): ?>
                                    <a href="<?php echo $this->url('ideas/create-project/' . $idea['id']); ?>" class="btn btn-success">
                                        <i class="fas fa-project-diagram"></i> Créer projet
                                    </a>
                                <?php endif; ?>

                                <?php if (isset($idea['projetId']) && !empty($idea['projetId'])): ?>
                                    <a href="<?php echo $this->url('projects/' . $idea['projetId']); ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i> Voir projet
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Evaluation Modal -->
                            <div class="modal fade" id="evaluateModal-<?php echo $idea['id']; ?>" tabindex="-1">
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
                                                    <h5><?php echo $this->escape($idea['titre']); ?></h5>
                                                    <p><?php echo nl2br($this->escape($idea['description'])); ?></p>

                                                    <?php if (!empty($idea['objectifs'])): ?>
                                                        <h6 class="mt-3">Objectifs:</h6>
                                                        <p><?php echo nl2br($this->escape($idea['objectifs'])); ?></p>
                                                    <?php endif; ?>

                                                    <?php if (!empty($idea['benefices'])): ?>
                                                        <h6 class="mt-3">Bénéfices attendus:</h6>
                                                        <p><?php echo nl2br($this->escape($idea['benefices'])); ?></p>
                                                    <?php endif; ?>

                                                    <div class="d-flex justify-content-between mt-3">
                                                        <div>
                                                            <strong>Domaine:</strong> <?php echo $this->escape($idea['domaine']); ?>
                                                        </div>
                                                        <div>
                                                            <strong>Proposé par:</strong> <?php echo $this->escape($idea['proposerPrenom'] . ' ' . $idea['proposerNom']); ?>
                                                        </div>
                                                        <div>
                                                            <strong>Date:</strong> <?php echo $this->formatDate($idea['dateProposition']); ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr>

                                                <div class="mb-3">
                                                    <label for="status-<?php echo $idea['id']; ?>" class="form-label">Nouveau statut</label>
                                                    <select class="form-select" id="status-<?php echo $idea['id']; ?>" name="status" required>
                                                        <option value="">Sélectionnez un statut</option>
                                                        <option value="En évaluation" <?php echo $idea['status'] === 'En évaluation' ? 'selected' : ''; ?>>En évaluation</option>
                                                        <option value="Approuvée">Approuvée</option>
                                                        <option value="Rejetée">Rejetée</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="commentaire-<?php echo $idea['id']; ?>" class="form-label">Commentaire d'évaluation</label>
                                                    <textarea class="form-control" id="commentaire-<?php echo $idea['id']; ?>" name="commentaire" rows="5" placeholder="Expliquez votre décision..."><?php echo isset($idea['commentaire']) ? $this->escape($idea['commentaire']) : ''; ?></textarea>
                                                    <div class="form-text">Votre évaluation détaillée aidera le proposeur à comprendre votre décision.</div>
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
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Ideas Stats -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Statistiques des idées</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="border rounded p-3">
                            <h2 class="text-info"><?php echo $stats['soumises'] ?? 0; ?></h2>
                            <p class="mb-0">Soumises</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="border rounded p-3">
                            <h2 class="text-warning"><?php echo $stats['enEvaluation'] ?? 0; ?></h2>
                            <p class="mb-0">En évaluation</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="border rounded p-3">
                            <h2 class="text-success"><?php echo $stats['approuvees'] ?? 0; ?></h2>
                            <p class="mb-0">Approuvées</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="border rounded p-3">
                            <h2 class="text-danger"><?php echo $stats['rejetees'] ?? 0; ?></h2>
                            <p class="mb-0">Rejetées</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>