<?php
/**
 * Project details view
 */

// Helper function to get the appropriate CSS class for status badges
function getStatusClass($status) {
    switch ($status) {
        case 'En préparation':
            return 'bg-warning';
        case 'En cours':
            return 'bg-success';
        case 'Terminé':
            return 'bg-info';
        case 'Suspendu':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0"><?= $this->escape($project['titre']) ?></h1>
        <div>
            <?php
            $isChef = $project['chefProjet'] == $auth->getUser()['id'];
            $canEdit = $isChef || $auth->hasPermission('edit_project');

            if ($canEdit):
                ?>
                <a href="<?= $this->url('projects/edit/' . $project['id']) ?>" class="btn btn-warning me-2">
                    <i class="fa fa-edit"></i> Modifier
                </a>
            <?php endif; ?>

            <a href="<?= $this->url('projects') ?>" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Flash messages -->
    <?php if (isset($flash['message'])): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= $this->escape($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <!-- Project Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Détails du projet</h6>
                    <span class="badge <?= getStatusClass($project['status']) ?>"><?= $this->escape($project['status']) ?></span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Description</h5>
                        <div class="p-3 bg-light rounded">
                            <?= nl2br($this->escape($project['description'])) ?>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Période</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Date de début:</span>
                                    <span class="fw-bold"><?= $this->formatDate($project['dateDebut'], 'd/m/Y') ?></span>
                                </li>
                                <?php if (!empty($project['dateFin'])): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Date de fin:</span>
                                        <span class="fw-bold"><?= $this->formatDate($project['dateFin'], 'd/m/Y') ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Durée:</span>
                                        <span>
                                            <?php
                                            $start = new DateTime($project['dateDebut']);
                                            $end = new DateTime($project['dateFin']);
                                            $diff = $start->diff($end);

                                            if ($diff->y > 0) {
                                                echo $diff->y . ' an' . ($diff->y > 1 ? 's' : '') . ' ';
                                            }

                                            echo $diff->m . ' mois';
                                            ?>
                                        </span>
                                    </li>
                                <?php else: ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Date de fin:</span>
                                        <span class="text-muted">Non définie</span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            <h5>Informations générales</h5>
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Chef de projet:</span>
                                    <a href="<?= $this->url('users/' . $project['chefProjet']) ?>" class="fw-bold">
                                        <?= $this->escape($project['chefPrenom'] . ' ' . $project['chefNom']) ?>
                                    </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Budget:</span>
                                    <?php if (!empty($project['budget'])): ?>
                                        <span class="fw-bold"><?= $this->formatCurrency($project['budget']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Non défini</span>
                                    <?php endif; ?>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Date de création:</span>
                                    <span><?= $this->formatDate($project['dateCreation'], 'd/m/Y') ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="mb-4">
                        <h5>Documents</h5>
                        <?php if (empty($documents)): ?>
                            <div class="alert alert-info">
                                Aucun document attaché à ce projet
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Type</th>
                                        <th>Taille</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($documents as $doc): ?>
                                        <tr>
                                            <td><?= $this->escape($doc['original_name'] ?? $doc['filename']) ?></td>
                                            <td><?= $this->escape($doc['mime'] ?? 'Inconnu') ?></td>
                                            <td>
                                                <?php
                                                // Format file size
                                                $size = isset($doc['size']) ? $doc['size'] : 0;
                                                if ($size < 1024) {
                                                    echo $size . ' B';
                                                } elseif ($size < 1048576) {
                                                    echo round($size / 1024, 2) . ' KB';
                                                } else {
                                                    echo round($size / 1048576, 2) . ' MB';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="<?= $this->url('projects/download-document/' . $project['id'] . '/' . $doc['filename']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-download"></i> Télécharger
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($canEdit): ?>
                        <div class="mt-3 border-top pt-3">
                            <a href="<?= $this->url('projects/edit/' . $project['id']) ?>" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Modifier le projet
                            </a>

                            <!-- Delete Project Button -->
                            <?php
                            $canDelete = $isChef ?
                                $auth->hasPermission('delete_own_project') :
                                $auth->hasPermission('delete_project');

                            if ($canDelete):
                                ?>
                                <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteProjectModal">
                                    <i class="fa fa-trash"></i> Supprimer le projet
                                </button>

                                <!-- Delete Project Modal -->
                                <div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Êtes-vous sûr de vouloir supprimer le projet <strong><?= $this->escape($project['titre']) ?></strong> ? Cette action est irréversible.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <form action="<?= $this->url('projects/delete/' . $project['id']) ?>" method="post">
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Related Publications -->
            <?php if (!empty($publications)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Publications liées</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($publications as $publication): ?>
                                <a href="<?= $this->url('publications/' . $publication['id']) ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?= $this->escape($publication['titre']) ?></h5>
                                        <small>
                                            <span class="badge bg-success"><?= $this->escape($publication['type']) ?></span>
                                        </small>
                                    </div>
                                    <p class="mb-1"><?= $this->truncate($publication['contenu'], 100) ?></p>
                                    <small>
                                        Par <?= $this->escape($publication['auteurPrenom'] . ' ' . $publication['auteurNom']) ?> |
                                        <?= $this->formatDate($publication['datePublication'], 'd/m/Y') ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related Events -->
            <?php if (!empty($events)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Événements liés</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($events as $event): ?>
                                <a href="<?= $this->url('events/' . $event['id']) ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?= $this->escape($event['titre']) ?></h5>
                                        <small>
                                            <span class="badge bg-info"><?= $this->escape($event['type'] ?? 'Événement') ?></span>
                                        </small>
                                    </div>
                                    <p class="mb-1"><?= $this->truncate($event['description'], 100) ?></p>
                                    <small>
                                        Lieu: <?= $this->escape($event['lieu']) ?> |
                                        <?php if (isset($event['eventDate'])): ?>
                                            Date: <?= $this->formatDate($event['eventDate'], 'd/m/Y') ?>
                                        <?php else: ?>
                                            Créé le: <?= $this->formatDate($event['dateCreation'], 'd/m/Y') ?>
                                        <?php endif; ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <!-- Participants -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Participants</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($participants)): ?>
                        <div class="alert alert-info">
                            Aucun participant en dehors du chef de projet
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($participants as $participant): ?>
                                <a href="<?= $this->url('users/' . $participant['utilisateurId']) ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($participant['profilePicture'])): ?>
                                            <img src="<?= $this->url('uploads/profile_pictures/' . $participant['profilePicture']) ?>" class="rounded-circle me-3" width="40" height="40" alt="Photo de profil">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary text-white me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <?= strtoupper(substr($participant['prenom'], 0, 1) . substr($participant['nom'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold"><?= $this->escape($participant['prenom'] . ' ' . $participant['nom']) ?></div>
                                            <?php if (!empty($participant['domaineRecherche'])): ?>
                                                <small class="text-muted"><?= $this->escape($participant['domaineRecherche']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Partners -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Partenaires</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($partners)): ?>
                        <div class="alert alert-info">
                            Aucun partenaire associé
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($partners as $partner): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1"><?= $this->escape($partner['nom']) ?></h6>
                                        <span class="badge bg-primary"><?= $this->escape($partner['type']) ?></span>
                                    </div>
                                    <?php if (!empty($partner['description'])): ?>
                                        <p class="small mb-1"><?= $this->truncate($partner['description'], 100) ?></p>
                                    <?php endif; ?>

                                    <div class="small">
                                        <?php if (!empty($partner['website'])): ?>
                                            <a href="<?= $this->escape($partner['website']) ?>" target="_blank" class="me-2">
                                                <i class="fa fa-globe"></i> Site web
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($partner['email'])): ?>
                                            <a href="mailto:<?= $this->escape($partner['email']) ?>" class="me-2">
                                                <i class="fa fa-envelope"></i> Email
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($partner['telephone'])): ?>
                                            <a href="tel:<?= $this->escape($partner['telephone']) ?>">
                                                <i class="fa fa-phone"></i> Téléphone
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Project Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="fa fa-users fa-2x text-primary"></i>
                            </div>
                            <h4><?= count($participants) + 1 ?></h4>
                            <div class="small text-muted">Participants</div>
                        </div>

                        <div class="col-4">
                            <div class="mb-2">
                                <i class="fa fa-file-alt fa-2x text-success"></i>
                            </div>
                            <h4><?= count($publications ?? []) ?></h4>
                            <div class="small text-muted">Publications</div>
                        </div>

                        <div class="col-4">
                            <div class="mb-2">
                                <i class="fa fa-calendar fa-2x text-info"></i>
                            </div>
                            <h4><?= count($events ?? []) ?></h4>
                            <div class="small text-muted">Événements</div>
                        </div>
                    </div>

                    <?php if (!empty($project['dateDebut']) && !empty($project['dateFin'])): ?>
                        <div class="progress mt-4">
                            <?php
                            $start = new DateTime($project['dateDebut']);
                            $end = new DateTime($project['dateFin']);
                            $today = new DateTime();

                            $totalDays = $start->diff($end)->days;
                            $elapsedDays = $start->diff($today)->days;

                            if ($totalDays > 0) {
                                $progress = min(100, max(0, ($elapsedDays / $totalDays) * 100));
                            } else {
                                $progress = 0;
                            }

                            $progressClass = '';
                            if ($progress < 25) {
                                $progressClass = 'bg-danger';
                            } elseif ($progress < 75) {
                                $progressClass = 'bg-warning';
                            } else {
                                $progressClass = 'bg-success';
                            }
                            ?>

                            <div class="progress-bar <?= $progressClass ?>" role="progressbar" style="width: <?= $progress ?>%" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= round($progress) ?>%
                            </div>
                        </div>
                        <div class="small text-muted text-center mt-2">
                            Progression du projet
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>