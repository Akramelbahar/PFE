<?php
/**
 * Projects listing view
 */
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Projets de Recherche</h1>
        <?php if ($auth->hasPermission('create_project')): ?>
            <a href="<?= $this->url('projects/create') ?>" class="btn btn-primary">
                <i class="fa fa-plus"></i> Nouveau projet
            </a>
        <?php endif; ?>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <form action="<?= $this->url('projects') ?>" method="get" class="row g-3">
                <!-- Status Filter -->
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <?php foreach ($filters['statuses'] as $status): ?>
                            <option value="<?= $this->escape($status) ?>" <?= isset($currentFilters['status']) && $currentFilters['status'] === $status ? 'selected' : '' ?>>
                                <?= $this->escape($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Researcher Filter -->
                <div class="col-md-3">
                    <label for="chercheur" class="form-label">Chercheur</label>
                    <select name="chercheur" id="chercheur" class="form-select">
                        <option value="">Tous les chercheurs</option>
                        <?php foreach ($filters['chercheurs'] as $chercheur): ?>
                            <option value="<?= $this->escape($chercheur['id']) ?>" <?= isset($currentFilters['chercheur']) && (int)$currentFilters['chercheur'] === (int)$chercheur['id'] ? 'selected' : '' ?>>
                                <?= $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Year Filter -->
                <div class="col-md-2">
                    <label for="year" class="form-label">Année</label>
                    <select name="year" id="year" class="form-select">
                        <option value="">Toutes les années</option>
                        <?php foreach ($filters['years'] as $year): ?>
                            <option value="<?= $this->escape($year) ?>" <?= isset($currentFilters['year']) && $currentFilters['year'] === $year ? 'selected' : '' ?>>
                                <?= $this->escape($year) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Search -->
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Titre, description..." value="<?= isset($currentFilters['search']) ? $this->escape($currentFilters['search']) : '' ?>">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flash messages -->
    <?php if (isset($flash['message'])): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= $this->escape($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Projects List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des projets</h6>
            <span class="badge bg-primary"><?= count($projects) ?> projets</span>
        </div>
        <div class="card-body">
            <?php if (empty($projects)): ?>
                <div class="alert alert-info mb-0">
                    Aucun projet trouvé.
                    <?php if ($auth->hasPermission('create_project')): ?>
                        <a href="<?= $this->url('projects/create') ?>">Créer un nouveau projet</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Chef de Projet</th>
                            <th>Dates</th>
                            <th>Status</th>
                            <th>Budget</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td>
                                    <a href="<?= $this->url('projects/' . $project['id']) ?>"><?= $this->escape($project['titre']) ?></a>
                                </td>
                                <td>
                                    <?php if (isset($project['chefNom'])): ?>
                                        <?= $this->escape($project['chefPrenom'] . ' ' . $project['chefNom']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Non assigné</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>Début: <?= $this->formatDate($project['dateDebut'], 'd/m/Y') ?></div>
                                    <?php if (!empty($project['dateFin'])): ?>
                                        <div>Fin: <?= $this->formatDate($project['dateFin'], 'd/m/Y') ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    switch ($project['status']) {
                                        case 'En préparation':
                                            $statusClass = 'bg-warning';
                                            break;
                                        case 'En cours':
                                            $statusClass = 'bg-success';
                                            break;
                                        case 'Terminé':
                                            $statusClass = 'bg-info';
                                            break;
                                        case 'Suspendu':
                                            $statusClass = 'bg-danger';
                                            break;
                                        default:
                                            $statusClass = 'bg-secondary';
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= $this->escape($project['status']) ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($project['budget'])): ?>
                                        <?= $this->formatCurrency($project['budget']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Non défini</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= $this->url('projects/' . $project['id']) ?>" class="btn btn-info" title="Voir">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <?php
                                        $isChef = $project['chefProjet'] == $auth->getUser()['id'];
                                        $canEdit = $isChef || $auth->hasPermission('edit_project');
                                        if ($canEdit):
                                            ?>
                                            <a href="<?= $this->url('projects/edit/' . $project['id']) ?>" class="btn btn-warning" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php
                                        $canDelete = $isChef ?
                                            $auth->hasPermission('delete_own_project') :
                                            $auth->hasPermission('delete_project');
                                        if ($canDelete):
                                            ?>
                                            <button type="button" class="btn btn-danger" title="Supprimer" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $project['id'] ?>">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Delete Confirmation Modal -->
                                    <?php if ($canDelete): ?>
                                        <div class="modal fade" id="deleteModal<?= $project['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $project['id'] ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel<?= $project['id'] ?>">Confirmer la suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        Êtes-vous sûr de vouloir supprimer le projet <strong><?= $this->escape($project['titre']) ?></strong> ? Cette action est irréversible.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="<?= $this->url('projects/delete/' . $project['id']) ?>" method="post" class="d-inline">
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>