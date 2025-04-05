<!-- views/evenements/view.php -->
<div class="events-view-page">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><?php echo $this->escape($event['titre']); ?></h3>
                    <span class="badge bg-<?php
                    switch ($eventType) {
                        case 'Seminaire': echo 'info'; break;
                        case 'Conference': echo 'success'; break;
                        case 'Workshop': echo 'warning'; break;
                        default: echo 'secondary';
                    }
                    ?>">
                        <?php echo $this->escape($eventType); ?>
                    </span>
                </div>
                <div class="card-body">
                    <p><?php echo nl2br($this->escape($event['description'])); ?></p>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5>Informations</h5>
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                    <?php echo $this->escape($event['lieu']); ?>
                                </li>
                                <?php if ($eventType === 'Seminaire'): ?>
                                    <li>
                                        <i class="fas fa-calendar me-2 text-muted"></i>
                                        <?php echo $this->formatDate($specificDetails['date'], 'd/m/Y'); ?>
                                    </li>
                                <?php elseif (in_array($eventType, ['Conference', 'Workshop'])): ?>
                                    <li>
                                        <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                        Du <?php echo $this->formatDate($specificDetails['dateDebut'], 'd/m/Y'); ?>
                                        au <?php echo $this->formatDate($specificDetails['dateFin'], 'd/m/Y'); ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php if ($eventType === 'Workshop' && !empty($specificDetails['instructorName'])): ?>
                            <div class="col-md-6">
                                <h5>Animateur</h5>
                                <div class="d-flex align-items-center">
                                    <span class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                                        <?php
                                        $nameParts = explode(' ', $specificDetails['instructorName']);
                                        echo strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
                                        ?>
                                    </span>
                                    <div>
                                        <h6 class="mb-0"><?php echo $this->escape($specificDetails['instructorName']); ?></h6>
                                        <a href="<?php echo $this->url('users/' . $specificDetails['instructorId']); ?>" class="text-muted small">
                                            Voir le profil
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Créé par <?php echo $this->escape($event['createurPrenom'] . ' ' . $event['createurNom']); ?>
                        le <?php echo $this->formatDate($event['dateCreation']); ?>
                    </small>

                    <?php if ($auth->hasPermission('edit_events') ||
                        ($auth->getUser()['id'] == $event['createurId'])): ?>
                        <div>
                            <a href="<?php echo $this->url('events/edit/' . $event['id']); ?>" class="btn btn-sm btn-warning me-2">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Documents Section -->
            <?php if (!empty($documents)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Documents</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($documents as $document): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file me-2"></i>
                                    <?php echo $this->escape($document['filename']); ?>
                                </div>
                                <div>
                                    <span class="text-muted me-3">
                                        <?php echo round($document['size'] / 1024, 2); ?> Ko
                                    </span>
                                    <a href="<?php echo $this->url('events/download-document/' . $event['id'] . '/' . $document['filename']); ?>"
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
            <!-- Related Project (if exists) -->
            <?php if (!empty($event['projetId'])): ?>
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Projet associé</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="card-title"><?php echo $this->escape($event['projetTitre']); ?></h6>
                        <p class="card-text">
                            <?php echo $this->truncate($event['projetDescription'], 100); ?>
                        </p>
                        <a href="<?php echo $this->url('projects/' . $event['projetId']); ?>" class="btn btn-sm btn-outline-primary">
                            Voir le projet
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related News -->
            <?php if (!empty($relatedNews)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Actualités liées</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($relatedNews as $news): ?>
                            <a href="<?php echo $this->url('news/' . $news['id']); ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo $this->escape($news['titre']); ?></h6>
                                    <small><?php echo $this->formatDate($news['datePublication'], 'd/m/Y'); ?></small>
                                </div>
                                <p class="mb-1"><?php echo $this->truncate($news['contenu'], 50); ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Event Confirmation Modal -->
<?php if ($auth->hasPermission('edit_events') || ($auth->getUser()['id'] == $event['createurId'])): ?>
    <div class="modal fade" id="deleteEventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="<?php echo $this->url('events/delete/' . $event['id']); ?>" method="post">
                        <?php echo CSRF::tokenField(); ?>
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>