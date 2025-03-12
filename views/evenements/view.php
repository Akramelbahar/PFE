<!-- views/projects/view.php -->
<div class="project-view-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?php echo $this->escape($project['titre']); ?></h1>
        <div>
            <a href="<?php echo $this->url('projects'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux projets
            </a>

            <?php if ($auth->isLoggedIn() && ($project['chefProjet'] == $auth->getUser()['id'] || $auth->hasPermission('edit_project'))): ?>
                <a href="<?php echo $this->url('projects/edit/' . $project['id']); ?>" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            <?php endif; ?>

            <?php if ($auth->hasPermission('delete_project')): ?>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProjectModal">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Project Details Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Détails du projet</h5>
            <span class="badge <?php
            switch($project['status']) {
                case 'En préparation':
                    echo 'bg-secondary';
                    break;
                case 'En cours':
                    echo 'bg-primary';
                    break;
                case 'Terminé':
                    echo 'bg-success';
                    break;
                case 'Suspendu':
                    echo 'bg-warning';
                    break;
                default:
                    echo 'bg-info';
            }
            ?> fs-6"><?php echo $this->escape($project['status']); ?></span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="fw-bold">Description</h6>
                    <p><?php echo nl2br($this->escape($project['description'])); ?></p>

                    <?php if (!empty($project['budget'])): ?>
                        <h6 class="fw-bold mt-4">Budget</h6>
                        <p><?php echo $this->formatCurrency($project['budget']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <h6 class="fw-bold">Chef de projet</h6>
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <?php echo strtoupper(substr($project['chefPrenom'], 0, 1) . substr($project['chefNom'], 0, 1)); ?>
                        </span>
                        <a href="<?php echo $this->url('users/' . $project['chefProjet']); ?>">
                            <?php echo $this->escape($project['chefPrenom'] . ' ' . $project['chefNom']); ?>
                        </a>
                    </div>

                    <h6 class="fw-bold">Période</h6>
                    <p>
                        <strong>Début:</strong> <?php echo $this->formatDate($project['dateDebut'], 'd/m/Y'); ?><br>
                        <strong>Fin:</strong> <?php echo !empty($project['dateFin']) ? $this->formatDate($project['dateFin'], 'd/m/Y') : 'En cours'; ?>
                    </p>

                    <h6 class="fw-bold">Date de création</h6>
                    <p><?php echo $this->formatDate($project['dateCreation']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Participants Section -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Participants</h5>
                    <span class="badge bg-primary"><?php echo count($participants); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($participants)): ?>
                        <p class="text-muted">Aucun participant pour ce projet.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($participants as $participant): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                <?php echo strtoupper(substr($participant['prenom'], 0, 1) . substr($participant['nom'], 0, 1)); ?>
                                            </span>
                                            <div>
                                                <a href="<?php echo $this->url('users/' . $participant['utilisateurId']); ?>">
                                                    <?php echo $this->escape($participant['prenom'] . ' ' . $participant['nom']); ?>
                                                </a>
                                                <div class="small text-muted"><?php echo $this->escape($participant['email']); ?></div>
                                            </div>
                                        </div>
                                        <span class="badge <?php echo $participant['role'] === 'chercheur' ? 'bg-info' : 'bg-secondary'; ?>">
                                            <?php echo $participant['role'] === 'chercheur' ? 'Chercheur' : 'Participant'; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Partners Section -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Partenaires</h5>
                    <span class="badge bg-primary"><?php echo count($partners); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($partners)): ?>
                        <p class="text-muted">Aucun partenaire pour ce projet.</p>
                    <?php else: ?>
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            <?php foreach ($partners as $partner): ?>
                                <div class="col">
                                    <div class="card h-100">
                                        <?php if (!empty($partner['logo'])): ?>
                                            <img src="<?php echo $this->escape($partner['logo']); ?>" class="card-img-top" alt="<?php echo $this->escape($partner['nom']); ?>" style="max-height: 100px; object-fit: contain; padding: 10px;">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo $this->escape($partner['nom']); ?></h6>
                                            <?php if (!empty($partner['contact'])): ?>
                                                <p class="card-text small">
                                                    <i class="fas fa-envelope me-1"></i> <?php echo $this->escape($partner['contact']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($partner['siteweb'])): ?>
                                                <a href="<?php echo $this->escape($partner['siteweb']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-globe me-1"></i> Site web
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Documents Section -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Documents</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($documents)): ?>
                        <p class="text-muted">Aucun document pour ce projet.</p>
                    <?php else: ?>
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
                                            <small class="text-muted ms-2">(<?php echo Utils::formatFileSize($document['size']); ?>)</small>
                                        </div>

                                        <?php if ($auth->isLoggedIn() && ($project['chefProjet'] == $auth->getUser()['id'] || $auth->hasPermission('edit_project'))): ?>
                                            <form action="<?php echo $this->url('projects/delete-document/' . $project['id'] . '/' . $document['filename']); ?>" method="post" class="d-inline">
                                                <?php echo CSRF::tokenField(); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document?');">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Related Events & Publications -->
        <div class="col-md-6">
            <!-- Related Events -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Événements liés</h5>
                    <span class="badge bg-primary"><?php echo count($events); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($events)): ?>
                        <p class="text-muted">Aucun événement lié à ce projet.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($events as $event): ?>
                                <a href="<?php echo $this->url('events/' . $event['id']); ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo $this->escape($event['titre']); ?></h6>
                                        <span class="badge <?php
                                        switch($event['type'] ?? '') {
                                            case 'Seminaire':
                                                echo 'bg-info';
                                                break;
                                            case 'Conference':
                                                echo 'bg-primary';
                                                break;
                                            case 'Workshop':
                                                echo 'bg-success';
                                                break;
                                            default:
                                                echo 'bg-secondary';
                                        }
                                        ?>"><?php echo $this->escape($event['type'] ?? 'Événement'); ?></span>
                                    </div>
                                    <p class="mb-1"><?php echo $this->truncate($event['description'], 100); ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo $this->escape($event['lieu']); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Related Publications -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Publications liées</h5>
                    <span class="badge bg-primary"><?php echo count($publications); ?></span>
                </div>
                <div class="card-body">
                    <?php if (empty($publications)): ?>
                        <p class="text-muted">Aucune publication liée à ce projet.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($publications as $publication): ?>
                                <a href="<?php echo $this->url('publications/' . $publication['id']); ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo $this->escape($publication['titre']); ?></h6>
                                        <span class="badge bg-secondary"><?php echo $publication['type'] ?? 'Publication'; ?></span>
                                    </div>
                                    <p class="mb-1"><?php echo $this->truncate($publication['contenu'], 100); ?></p>
                                    <small class="text-muted">
                                        <?php echo $this->formatDate($publication['datePublication']); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Project Modal -->
<?php if ($auth->hasPermission('delete_project')): ?>
    <div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-labelledby="deleteProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteProjectModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce projet? Cette action est irréversible et supprimera toutes les données associées.</p>
                    <p><strong>Projet:</strong> <?php echo $this->escape($project['titre']); ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="<?php echo $this->url('projects/delete/' . $project['id']); ?>" method="post">
                        <?php echo CSRF::tokenField(); ?>
                        <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>