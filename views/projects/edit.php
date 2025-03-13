<?php
/**
 * Edit project view
 */
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Modifier le Projet</h1>
        <div>
            <a href="<?= $this->url('projects/' . $project['id']) ?>" class="btn btn-info me-2">
                <i class="fa fa-eye"></i> Voir le projet
            </a>
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

    <!-- Display validation errors if any -->
    <?php if (isset($errors) && is_array($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">Erreurs de validation</h5>
            <ul class="mb-0">
                <?php foreach ($errors as $field => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <li><?= $field ?>: <?= $this->escape($error) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Project Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations du projet</h6>
        </div>
        <div class="card-body">
            <form action="<?= $this->url('projects/edit/' . $project['id']) ?>" method="post" enctype="multipart/form-data" id="projectForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="titre" class="form-label">Titre du projet <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="titre" name="titre" value="<?= $this->escape($project['titre']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="chefProjet" class="form-label">Chef de projet <span class="text-danger">*</span></label>
                        <select class="form-select" id="chefProjet" name="chefProjet" required>
                            <option value="">Sélectionner un chercheur</option>
                            <?php foreach ($chercheurs as $chercheur): ?>
                                <option value="<?= $this->escape($chercheur['utilisateurId']) ?>" <?= $project['chefProjet'] == $chercheur['utilisateurId'] ? 'selected' : '' ?>>
                                    <?= $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="dateDebut" class="form-label">Date de début <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dateDebut" name="dateDebut" value="<?= $this->escape($project['dateDebut']) ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label for="dateFin" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="dateFin" name="dateFin" value="<?= isset($project['dateFin']) ? $this->escape($project['dateFin']) : '' ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="budget" class="form-label">Budget (MAD)</label>
                        <input type="number" class="form-control" id="budget" name="budget" step="0.01" min="0" value="<?= isset($project['budget']) ? $this->escape($project['budget']) : '' ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="En préparation" <?= $project['status'] === 'En préparation' ? 'selected' : '' ?>>En préparation</option>
                            <option value="En cours" <?= $project['status'] === 'En cours' ? 'selected' : '' ?>>En cours</option>
                            <option value="Terminé" <?= $project['status'] === 'Terminé' ? 'selected' : '' ?>>Terminé</option>
                            <option value="Suspendu" <?= $project['status'] === 'Suspendu' ? 'selected' : '' ?>>Suspendu</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="6" required><?= $this->escape($project['description']) ?></textarea>
                </div>

                <hr class="my-4">

                <div class="row">
                    <div class="col-md-6">
                        <h5>Participants</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-text mb-2">
                                        Sélectionnez les chercheurs qui participent à ce projet. Le chef de projet sera automatiquement ajouté.
                                    </div>
                                    <select class="form-select" id="participants" name="participants[]" multiple size="8">
                                        <?php
                                        // Get current participant IDs
                                        $participantIds = array_column($participants ?? [], 'utilisateurId');

                                        foreach ($chercheurs as $chercheur):
                                            $selected = in_array($chercheur['utilisateurId'], $participantIds);
                                            ?>
                                            <option value="<?= $this->escape($chercheur['utilisateurId']) ?>" <?= $selected ? 'selected' : '' ?>>
                                                <?= $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">
                                        Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs participants.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5>Partenaires</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-text mb-2">
                                        Sélectionnez les partenaires associés à ce projet.
                                    </div>
                                    <select class="form-select" id="partners" name="partners[]" multiple size="8">
                                        <?php
                                        // Get current partner IDs
                                        $partnerIds = array_column($projectPartners ?? [], 'id');

                                        foreach ($partners as $partner):
                                            $selected = in_array($partner['id'], $partnerIds);
                                            ?>
                                            <option value="<?= $this->escape($partner['id']) ?>" <?= $selected ? 'selected' : '' ?>>
                                                <?= $this->escape($partner['nom']) ?> (<?= $this->escape($partner['type']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">
                                        Maintenez la touche Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs partenaires.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Current Documents -->
                <div class="mb-4">
                    <h5>Documents actuels</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                            <tr>
                                <th>Nom du fichier</th>
                                <th>Type</th>
                                <th>Taille</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($documents)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Aucun document attaché</td>
                                </tr>
                            <?php else: ?>
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
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= $this->url('projects/download-document/' . $project['id'] . '/' . $doc['filename']) ?>" class="btn btn-primary" title="Télécharger">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDocModal<?= md5($doc['filename']) ?>" title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>

                                            <!-- Delete Document Confirmation Modal -->
                                            <div class="modal fade" id="deleteDocModal<?= md5($doc['filename']) ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer le document <strong><?= $this->escape($doc['original_name'] ?? $doc['filename']) ?></strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <form action="<?= $this->url('projects/delete-document/' . $project['id'] . '/' . $doc['filename']) ?>" method="post">
                                                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Upload New Documents -->
                <div class="mb-3">
                    <label for="documents" class="form-label">Ajouter des documents</label>
                    <input class="form-control" type="file" id="documents" name="documents[]" multiple>
                    <div class="form-text">
                        Vous pouvez sélectionner plusieurs fichiers (max 10MB par fichier).
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= $this->url('projects/' . $project['id']) ?>" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for form validation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form validation
        const form = document.getElementById('projectForm');

        form.addEventListener('submit', function(event) {
            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    valid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Validate date range if end date is provided
            const startDate = document.getElementById('dateDebut').value;
            const endDate = document.getElementById('dateFin').value;

            if (endDate && new Date(endDate) < new Date(startDate)) {
                document.getElementById('dateFin').classList.add('is-invalid');
                alert('La date de fin doit être postérieure à la date de début.');
                valid = false;
            }

            if (!valid) {
                event.preventDefault();
            }
        });

        // Add event listeners to remove validation styles when fields are corrected
        const fields = form.querySelectorAll('input, select, textarea');
        fields.forEach(function(field) {
            field.addEventListener('input', function() {
                if (field.value.trim()) {
                    field.classList.remove('is-invalid');
                }
            });
        });
    });
</script>