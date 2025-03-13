<!-- views/publications/view.php -->
<div class="publications-view-page">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><?php echo $this->escape($publication['titre']); ?></h3>
                    <span class="badge bg-<?php
                    switch ($publicationType) {
                        case 'Article': echo 'info'; break;
                        case 'Livre': echo 'success'; break;
                        case 'Chapitre': echo 'warning'; break;
                        default: echo 'secondary';
                    }
                    ?>">
                        <?php echo $this->escape($publicationType); ?>
                    </span>
                </div>
                <div class="card-body">
                    <p><?php echo nl2br($this->escape($publication['contenu'])); ?></p>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Informations</h5>
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-user me-2 text-muted"></i>
                                    Publié par <?php echo $this->escape($publication['auteurPrenom'] . ' ' . $publication['auteurNom']); ?>
                                </li>
                                <li>
                                    <i class="fas fa-calendar me-2 text-muted"></i>
                                    Publié le <?php echo $this->formatDate($publication['datePublication'], 'd/m/Y'); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <?php if (!empty($publication['evenementId'])): ?>
                                <h5>Événement associé</h5>
                                <p>
                                    <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                    <a href="<?php echo $this->url('events/' . $publication['evenementId']); ?>">
                                        <?php echo $this->escape($publication['evenementTitre']); ?>
                                    </a>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($publication['projetId'])): ?>
                                <h5>Projet associé</h5>
                                <p>
                                    <i class="fas fa-project-diagram me-2 text-muted"></i>
                                    <a href="<?php echo $this->url('projects/' . $publication['projetId']); ?>">
                                        <?php echo $this->escape($publication['projetTitre']); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <?php if ($auth->hasPermission('edit_publication') ||
                        ($auth->getUser()['id'] == $publication['auteurId'])): ?>
                        <div>
                            <a href="<?php echo $this->url('publications/edit/' . $publication['id']); ?>"
                               class="btn btn-sm btn-warning me-2">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deletePublicationModal">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Attached Documents -->
            <?php
            $documents = json_decode($publication['documents'] ?? '[]', true);
            if (!empty($documents)):
                ?>
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Documents joints</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($documents as $document): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file me-2"></i>
                                    <?php echo $this->escape($document['originalName']); ?>
                                </div>
                                <div>
                                    <span class="text-muted me-3">
                                        <?php echo round($document['size'] / 1024, 2); ?> Ko
                                    </span>
                                    <a href="<?php echo $this->url('publications/download/' . $publication['id'] . '/' . $document['filename']); ?>"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Télécharger">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Specific Type Details -->
            <?php if ($publicationType === 'Livre'): ?>
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Chapitres</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php if (!empty($publicationDetails['chapters'])): ?>
                            <?php foreach ($publicationDetails['chapters'] as $chapter): ?>
                                <a href="<?php echo $this->url('publications/' . $chapter['id']); ?>"
                                   class="list-group-item list-group-item-action">
                                    <?php echo $this->escape($chapter['titre']); ?>
                                    <small class="text-muted float-end">
                                        <?php echo $this->formatDate($chapter['datePublication'], 'd/m/Y'); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-muted">Aucun chapitre</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($publicationType === 'Chapitre'): ?>
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">Livre parent</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title">
                            <a href="<?php echo $this->url('publications/' . $publicationDetails['book']['publicationId']); ?>">
                                <?php echo $this->escape($publicationDetails['book']['titre']); ?>
                            </a>
                        </h6>
                        <p class="card-text text-muted">
                            Par <?php echo $this->escape($publicationDetails['book']['auteurPrenom'] . ' ' . $publicationDetails['book']['auteurNom']); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related Publications -->
            <?php if (!empty($relatedPublications)): ?>
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Publications du même auteur</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($relatedPublications as $relatedPub): ?>
                            <a href="<?php echo $this->url('publications/' . $relatedPub['id']); ?>"
                               class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo $this->escape($relatedPub['titre']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo $this->escape($relatedPub['type']); ?>
                                    </small>
                                </div>
                                <p class="mb-1 text-muted">
                                    <?php echo $this->truncate($relatedPub['contenu'], 50); ?>
                                </p>
                                <small class="text-muted">
                                    <?php echo $this->formatDate($relatedPub['datePublication'], 'd/m/Y'); ?>
                                </small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Publication Modal -->
    <?php if ($auth->hasPermission('edit_publication') ||
        ($auth->getUser()['id'] == $publication['auteurId'])): ?>
        <div class="modal fade" id="deletePublicationModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer cette publication ? Cette action est irréversible.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="<?php echo $this->url('publications/delete/' . $publication['id']); ?>" method="post">
                            <?php echo CSRF::tokenField(); ?>
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>