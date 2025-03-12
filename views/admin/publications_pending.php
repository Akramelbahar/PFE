<!-- views/admin/publications_pending.php -->
<div class="publications-pending-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Publications en attente d'approbation</h1>
        <div>
            <a href="<?php echo $this->url('admin/publications'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Toutes les publications
            </a>
        </div>
    </div>

    <?php if (empty($publications)): ?>
        <div class="alert alert-info">
            Aucune publication en attente d'approbation.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Auteur</th>
                    <th>Date de soumission</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($publications as $publication): ?>
                    <tr>
                        <td><?php echo $this->escape($publication['titre']); ?></td>
                        <td>
                                <span class="badge <?php
                                switch($publication['type']) {
                                    case 'Article':
                                        echo 'bg-primary';
                                        break;
                                    case 'Livre':
                                        echo 'bg-success';
                                        break;
                                    case 'Chapitre':
                                        echo 'bg-info';
                                        break;
                                    default:
                                        echo 'bg-secondary';
                                }
                                ?>"><?php echo $this->escape($publication['type']); ?></span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <?php echo strtoupper(substr($publication['auteurPrenom'], 0, 1) . substr($publication['auteurNom'], 0, 1)); ?>
                                    </span>
                                <?php echo $this->escape($publication['auteurPrenom'] . ' ' . $publication['auteurNom']); ?>
                            </div>
                        </td>
                        <td><?php echo $this->formatDate($publication['datePublication']); ?></td>
                        <td>
                                <span class="badge <?php echo $publication['status'] === 'submitted' ? 'bg-info' : 'bg-warning'; ?>">
                                    <?php
                                    echo $publication['status'] === 'submitted' ? 'Soumise' : 'En révision';
                                    ?>
                                </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo $this->url('publications/' . $publication['id']); ?>" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal-<?php echo $publication['id']; ?>">
                                    <i class="fas fa-clipboard-check"></i> Réviser
                                </button>
                            </div>

                            <!-- Review Modal -->
                            <div class="modal fade" id="reviewModal-<?php echo $publication['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form action="<?php echo $this->url('publications/review/' . $publication['id']); ?>" method="post">
                                            <?php echo CSRF::tokenField(); ?>
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">Réviser la publication</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <h5><?php echo $this->escape($publication['titre']); ?></h5>
                                                    <p><strong>Type:</strong> <?php echo $this->escape($publication['type']); ?></p>
                                                    <p><strong>Auteur:</strong> <?php echo $this->escape($publication['auteurPrenom'] . ' ' . $publication['auteurNom']); ?></p>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="status-<?php echo $publication['id']; ?>" class="form-label">Décision</label>
                                                    <select class="form-select" id="status-<?php echo $publication['id']; ?>" name="status" required>
                                                        <option value="">Sélectionnez une décision</option>
                                                        <option value="under_review" <?php echo $publication['status'] === 'under_review' ? 'selected' : ''; ?>>Marquer en révision</option>
                                                        <option value="approved">Approuver</option>
                                                        <option value="rejected">Rejeter</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="comments-<?php echo $publication['id']; ?>" class="form-label">Commentaires</label>
                                                    <textarea class="form-control" id="comments-<?php echo $publication['id']; ?>" name="comments" rows="5" placeholder="Commentaires pour l'auteur..."><?php echo isset($publication['commentairesRevision']) ? $this->escape($publication['commentairesRevision']) : ''; ?></textarea>
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
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>