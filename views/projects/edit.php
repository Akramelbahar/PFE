<?php
/**
 * Edit project view
 * Provides interface for updating project details, participants, partners and documents
 */
?>

<div class="container-fluid py-4">
    <!-- Header with navigation buttons -->
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
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Informations du projet</h6>
        </div>
        <div class="card-body">
            <form action="<?= $this->url('projects/edit/' . $project['id']) ?>" method="post" enctype="multipart/form-data" id="projectForm">
                <!-- Basic Project Information -->
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
                        <?php
                        // Format the start date properly for date input field (YYYY-MM-DD)
                        $dateDebutFormatted = '';
                        if (isset($project['dateDebut']) && !empty($project['dateDebut'])) {
                            // Handle different date formats
                            if (strpos($project['dateDebut'], ' ') !== false) {
                                // Format like "2023-01-01 00:00:00"
                                $dateDebutFormatted = substr($project['dateDebut'], 0, 10);
                            } else {
                                // Already in YYYY-MM-DD format
                                $dateDebutFormatted = $project['dateDebut'];
                            }
                        }
                        ?>
                        <input type="date" class="form-control" id="dateDebut" name="dateDebut" value="<?= $this->escape($dateDebutFormatted) ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label for="dateFin" class="form-label">Date de fin</label>
                        <?php
                        // Format the end date properly for date input field (YYYY-MM-DD)
                        $dateFinFormatted = '';
                        if (isset($project['dateFin']) && !empty($project['dateFin'])) {
                            // Handle different date formats
                            if (strpos($project['dateFin'], ' ') !== false) {
                                // Format like "2023-01-01 00:00:00"
                                $dateFinFormatted = substr($project['dateFin'], 0, 10);
                            } else {
                                // Already in YYYY-MM-DD format
                                $dateFinFormatted = $project['dateFin'];
                            }
                        }
                        ?>
                        <input type="date" class="form-control" id="dateFin" name="dateFin" value="<?= $this->escape($dateFinFormatted) ?>">
                        <div class="form-text">Optionnel</div>
                    </div>

                    <div class="col-md-3">
                        <label for="budget" class="form-label">Budget (MAD)</label>
                        <input type="number" class="form-control" id="budget" name="budget" step="0.01" min="0" value="<?= isset($project['budget']) ? $this->escape($project['budget']) : '' ?>">
                        <div class="form-text">Optionnel</div>
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
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

                <!-- Participants and Partners Section -->
                <div class="row">
                    <!-- Participants Selection -->
                    <div class="col-md-6">
                        <h5>Participants</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-text mb-2">
                                        Sélectionnez les chercheurs qui participent à ce projet. Le chef de projet sera automatiquement ajouté.
                                    </div>

                                    <!-- Using a scrollable box for better UX with many participants -->
                                    <div class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                                        <?php
                                        // Get current participant IDs
                                        $participantIds = array_column($participants ?? [], 'utilisateurId');

                                        foreach ($chercheurs as $chercheur):
                                            $isChef = $chercheur['utilisateurId'] == $project['chefProjet'];
                                            $selected = in_array($chercheur['utilisateurId'], $participantIds);
                                            ?>
                                            <div class="form-check mb-2">
                                                <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="participants[]"
                                                        id="participant-<?= $this->escape($chercheur['utilisateurId']) ?>"
                                                        value="<?= $this->escape($chercheur['utilisateurId']) ?>"
                                                    <?= $selected ? 'checked' : '' ?>
                                                    <?= $isChef ? 'disabled' : '' ?>
                                                >
                                                <label class="form-check-label" for="participant-<?= $this->escape($chercheur['utilisateurId']) ?>">
                                                    <?= $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']) ?>
                                                    <?= $isChef ? ' <small class="text-muted">(Chef de projet)</small>' : '' ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="form-text mt-2">
                                        Le chef de projet est automatiquement inclus dans le projet.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Partners Selection -->
                    <div class="col-md-6">
                        <h5>Partenaires</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="form-text mb-2">
                                        Sélectionnez les partenaires associés à ce projet.
                                    </div>

                                    <!-- Using a scrollable box for better UX with many partners -->
                                    <div class="border rounded p-2" style="max-height: 250px; overflow-y: auto;">
                                        <?php
                                        // Get current partner IDs
                                        $partnerIds = array_column($projectPartners ?? [], 'id');

                                        foreach ($partners as $partner):
                                            $selected = in_array($partner['id'], $partnerIds);
                                            ?>
                                            <div class="form-check mb-2">
                                                <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="partners[]"
                                                        id="partner-<?= $this->escape($partner['id']) ?>"
                                                        value="<?= $this->escape($partner['id']) ?>"
                                                    <?= $selected ? 'checked' : '' ?>
                                                >
                                                <label class="form-check-label" for="partner-<?= $this->escape($partner['id']) ?>">
                                                    <?= $this->escape($partner['nom']) ?>
                                                    <small class="text-muted">(<?= $this->escape($partner['type'] ?? 'Partenaire') ?>)</small>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Documents Section -->
                <div class="mb-4">
                    <h5>Documents actuels</h5>
                    <?php if (empty($documents)): ?>
                        <div class="alert alert-info">
                            Aucun document attaché à ce projet
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>Nom du fichier</th>
                                    <th>Type</th>
                                    <th>Taille</th>
                                    <th class="text-center">Actions</th>
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
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= $this->url('projects/download-document/' . $project['id'] . '/' . $doc['filename']) ?>" class="btn btn-primary" title="Télécharger">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <a href="<?= $this->url('projects/delete-document/' . $project['id'] . '/' . $doc['filename']) ?>" class="btn btn-danger" title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                
                                            </div>



                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Upload New Documents -->
                <div class="mb-4">
                    <h5>Ajouter des documents</h5>
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <input class="form-control" type="file" id="documents" name="documents[]" multiple>
                                <div class="form-text mt-2">
                                    <ul class="mb-0">
                                        <li>Formats acceptés: PDF, DOCX, JPG, PNG</li>
                                        <li>Taille maximale: 10 MB par fichier</li>
                                        <li>Vous pouvez sélectionner plusieurs fichiers à la fois</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= $this->url('projects/' . $project['id']) ?>" class="btn btn-secondary">
                        <i class="fa fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for form validation and dynamic behaviors -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form validation
        const form = document.getElementById('projectForm');
        const dateDebut = document.getElementById('dateDebut');
        const dateFin = document.getElementById('dateFin');
        const chefProjet = document.getElementById('chefProjet');

        // Debug date values
        console.log('Initial dateDebut value:', dateDebut.value);
        console.log('Initial dateFin value:', dateFin.value);

        // Date validation - set minimum date for end date
        dateDebut.addEventListener('change', function() {
            console.log('Date début changed to:', this.value);
            dateFin.min = this.value;

            // If end date is before start date, reset it
            if (dateFin.value && new Date(dateFin.value) < new Date(this.value)) {
                dateFin.value = this.value;
                console.log('Date fin reset to:', dateFin.value);
            }
        });

        // Set initial min date for end date
        if (dateDebut.value) {
            dateFin.min = dateDebut.value;
            console.log('Set initial dateFin.min to:', dateFin.min);
        }

        // Chef de projet validation - ensure they're not also selected as a participant
        chefProjet.addEventListener('change', function() {
            const chefId = this.value;
            const participantCheckboxes = document.querySelectorAll('input[name="participants[]"]');

            participantCheckboxes.forEach(checkbox => {
                if (checkbox.value === chefId) {
                    checkbox.checked = false;
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            });
        });

        // Trigger initial chef de projet validation
        if (chefProjet.value) {
            const event = new Event('change');
            chefProjet.dispatchEvent(event);
        }

        // Form submission validation
        form.addEventListener('submit', function(event) {
            let isValid = true;

            // Required fields validation
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                    console.log('Required field empty:', field.id);
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Date range validation
            if (dateDebut.value && dateFin.value) {
                console.log('Validating date range:', dateDebut.value, dateFin.value);
                if (new Date(dateFin.value) < new Date(dateDebut.value)) {
                    dateFin.classList.add('is-invalid');
                    alert('La date de fin doit être postérieure à la date de début.');
                    isValid = false;
                    console.log('Date validation failed');
                } else {
                    dateFin.classList.remove('is-invalid');
                }
            }

            if (!isValid) {
                event.preventDefault();
                console.log('Form validation failed');
            } else {
                console.log('Form validation passed');
            }
        });

        // Reset validation visual cues when field values change
        const formFields = form.querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            field.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });
    });
</script>