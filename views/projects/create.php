<!-- views/projects/create.php -->
<div class="projects-create-page">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Créer un nouveau projet de recherche</h3>
                </div>

                <div class="card-body">
                    <?php if (isset($errors) && $errors): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $field => $fieldErrors): ?>
                                    <?php foreach ($fieldErrors as $error): ?>
                                        <li><?php echo $this->escape($error); ?></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $this->url('projects/create'); ?>" method="post" enctype="multipart/form-data">
                        <?php echo CSRF::tokenField(); ?>

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre du projet <span class="text-danger">*</span></label>
                            <input type="text" id="titre" name="titre"
                                   class="form-control"
                                   value="<?php echo isset($project['titre']) ? $this->escape($project['titre']) : ''; ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea id="description" name="description"
                                      class="form-control"
                                      rows="5"
                                      required><?php echo isset($project['description']) ? $this->escape($project['description']) : ''; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="dateDebut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" id="dateDebut" name="dateDebut"
                                       class="form-control"
                                       value="<?php echo isset($project['dateDebut']) ? $this->escape($project['dateDebut']) : ''; ?>"
                                       required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="dateFin" class="form-label">Date de fin (optionnel)</label>
                                <input type="date" id="dateFin" name="dateFin"
                                       class="form-control"
                                       value="<?php echo isset($project['dateFin']) ? $this->escape($project['dateFin']) : ''; ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                <select id="status" name="status" class="form-select" required>
                                    <option value="">Sélectionner un statut</option>
                                    <option value="En préparation" <?php echo (isset($project['status']) && $project['status'] === 'En préparation') ? 'selected' : ''; ?>>En préparation</option>
                                    <option value="En cours" <?php echo (isset($project['status']) && $project['status'] === 'En cours') ? 'selected' : ''; ?>>En cours</option>
                                    <option value="Terminé" <?php echo (isset($project['status']) && $project['status'] === 'Terminé') ? 'selected' : ''; ?>>Terminé</option>
                                    <option value="Suspendu" <?php echo (isset($project['status']) && $project['status'] === 'Suspendu') ? 'selected' : ''; ?>>Suspendu</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="chefProjet" class="form-label">Chef de projet <span class="text-danger">*</span></label>
                                <select id="chefProjet" name="chefProjet" class="form-select" required>
                                    <option value="">Sélectionner un chef de projet</option>
                                    <?php foreach ($chercheurs as $chercheur): ?>
                                        <option value="<?php echo $this->escape($chercheur['id']); ?>"
                                            <?php echo (isset($project['chefProjet']) && $project['chefProjet'] == $chercheur['id']) ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="budget" class="form-label">Budget (optionnel)</label>
                                <div class="input-group">
                                    <span class="input-group-text">MAD</span>
                                    <input type="number" id="budget" name="budget"
                                           class="form-control"
                                           value="<?php echo isset($project['budget']) ? $this->escape($project['budget']) : ''; ?>"
                                           min="0" step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Participants Selection -->
                        <div class="mb-3">
                            <label class="form-label">Participants</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($chercheurs as $chercheur): ?>
                                            <?php if ($chercheur['id'] != $project['chefProjet']): ?>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                               type="checkbox"
                                                               name="participants[]"
                                                               id="participant-<?php echo $this->escape($chercheur['id']); ?>"
                                                               value="<?php echo $this->escape($chercheur['id']); ?>">
                                                        <label class="form-check-label"
                                                               for="participant-<?php echo $this->escape($chercheur['id']); ?>">
                                                            <?php echo $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Partners Selection -->
                        <div class="mb-3">
                            <label class="form-label">Partenaires</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($partners as $partner): ?>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="partners[]"
                                                           id="partner-<?php echo $this->escape($partner['id']); ?>"
                                                           value="<?php echo $this->escape($partner['id']); ?>">
                                                    <label class="form-check-label"
                                                           for="partner-<?php echo $this->escape($partner['id']); ?>">
                                                        <?php echo $this->escape($partner['nom']); ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Upload -->
                        <input type="file" id="documents" name="documents[]"
                               class="form-control"
                               multiple
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <div class="form-text">Vous pouvez télécharger plusieurs fichiers</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?php echo $this->url('projects'); ?>" class="btn btn-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le projet
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Date range validation
        const dateDebut = document.getElementById('dateDebut');
        const dateFin = document.getElementById('dateFin');

        if (dateDebut && dateFin) {
            // Set min date for dateFin to be the same as or after dateDebut
            dateFin.min = dateDebut.value;

            dateDebut.addEventListener('change', function() {
                // Update minimum date for dateFin
                dateFin.min = this.value;

                // If dateFin is before dateDebut, reset it
                if (new Date(dateFin.value) < new Date(this.value)) {
                    dateFin.value = this.value;
                }
            });
        }

        // Budget formatting
        const budgetInput = document.getElementById('budget');
        if (budgetInput) {
            budgetInput.addEventListener('input', function() {
                // Remove leading zeros
                this.value = this.value.replace(/^0+/, '');
            });
        }

        // Ensure chef de projet is not selected as a participant
        const chefProjetSelect = document.getElementById('chefProjet');
        const participantCheckboxes = document.querySelectorAll('input[name="participants[]"]');

        chefProjetSelect.addEventListener('change', function() {
            const chefProjetId = this.value;

            participantCheckboxes.forEach(checkbox => {
                if (checkbox.value === chefProjetId) {
                    checkbox.checked = false;
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            });
        });

        // Trigger initial validation
        if (chefProjetSelect.value) {
            const event = new Event('change');
            chefProjetSelect.dispatchEvent(event);
        }
    });
</script>