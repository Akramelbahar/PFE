<!-- views/evenements/create.php -->
<div class="events-create-page">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Créer un nouvel événement</h3>
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

                    <form action="<?php echo $this->url('events/create'); ?>" method="post" enctype="multipart/form-data">
                        <?php echo CSRF::tokenField(); ?>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type d'événement <span class="text-danger">*</span></label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="">Sélectionner un type</option>
                                <option value="Seminaire" <?php echo (isset($data['type']) && $data['type'] === 'Seminaire') ? 'selected' : ''; ?>>
                                    Séminaire
                                </option>
                                <option value="Conference" <?php echo (isset($data['type']) && $data['type'] === 'Conference') ? 'selected' : ''; ?>>
                                    Conférence
                                </option>
                                <option value="Workshop" <?php echo (isset($data['type']) && $data['type'] === 'Workshop') ? 'selected' : ''; ?>>
                                    Atelier
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" id="titre" name="titre"
                                   class="form-control"
                                   value="<?php echo isset($data['titre']) ? $this->escape($data['titre']) : ''; ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea id="description" name="description"
                                      class="form-control"
                                      rows="4"
                                      required><?php echo isset($data['description']) ? $this->escape($data['description']) : ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="lieu" class="form-label">Lieu <span class="text-danger">*</span></label>
                            <input type="text" id="lieu" name="lieu"
                                   class="form-control"
                                   value="<?php echo isset($data['lieu']) ? $this->escape($data['lieu']) : ''; ?>"
                                   required>
                        </div>

                        <!-- Seminaire-specific Date -->
                        <div id="seminaire-date-section" class="mb-3" style="display:none;">
                            <label for="date" class="form-label">Date du séminaire <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date"
                                   class="form-control"
                                   value="<?php echo isset($data['date']) ? $this->escape($data['date']) : ''; ?>">
                        </div>

                        <!-- Conference and Workshop Date Range -->
                        <div id="conference-date-section" class="row" style="display:none;">
                            <div class="col-md-6 mb-3">
                                <label for="dateDebut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" id="dateDebut" name="dateDebut"
                                       class="form-control"
                                       value="<?php echo isset($data['dateDebut']) ? $this->escape($data['dateDebut']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dateFin" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" id="dateFin" name="dateFin"
                                       class="form-control"
                                       value="<?php echo isset($data['dateFin']) ? $this->escape($data['dateFin']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Workshop Instructor (for Workshop only) -->
                        <div id="workshop-instructor-section" class="mb-3" style="display:none;">
                            <label for="instructorId" class="form-label">Animateur</label>
                            <select id="instructorId" name="instructorId" class="form-select">
                                <option value="">Sélectionner un animateur</option>
                                <?php foreach ($chercheurs as $chercheur): ?>
                                    <option value="<?php echo $this->escape($chercheur['id']); ?>">
                                        <?php echo $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="projetId" class="form-label">Projet associé</label>
                            <select id="projetId" name="projetId" class="form-select">
                                <option value="">Aucun projet</option>
                                <!-- Populate with projects -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="documents" class="form-label">Documents</label>
                            <input type="file" id="documents" name="documents[]"
                                   class="form-control"
                                   multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Vous pouvez télécharger plusieurs fichiers</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo $this->url('events'); ?>" class="btn btn-secondary">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer l'événement
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
        const typeSelect = document.getElementById('type');
        const seminaireSection = document.getElementById('seminaire-date-section');
        const conferenceSection = document.getElementById('conference-date-section');
        const workshopInstructorSection = document.getElementById('workshop-instructor-section');

        function toggleEventSections() {
            const selectedType = typeSelect.value;

            // Hide all sections first
            seminaireSection.style.display = 'none';
            conferenceSection.style.display = 'none';
            workshopInstructorSection.style.display = 'none';

            // Show appropriate sections based on selected type
            switch(selectedType) {
                case 'Seminaire':
                    seminaireSection.style.display = 'block';
                    break;
                case 'Conference':
                    conferenceSection.style.display = 'flex';
                    break;
                case 'Workshop':
                    conferenceSection.style.display = 'flex';
                    workshopInstructorSection.style.display = 'block';
                    break;
            }
            dateFin.min = dateDebut.value;

// Ajoutez:
            if (!dateDebut.value) {
                dateFin.disabled = true;
            } else {
                dateFin.disabled = false;
            }

            // Validate date range for Conference and Workshop
            const dateDebut = document.getElementById('dateDebut');
            const dateFin = document.getElementById('dateFin');

            if (dateDebut && dateFin) {
                dateFin.min = dateDebut.value;
                dateDebut.addEventListener('change', function() {
                    dateFin.min = this.value;
                    if (new Date(dateFin.value) < new Date(this.value)) {
                        dateFin.value = this.value;
                    }
                });
            }
        }

        // Initial setup and event listener
        typeSelect.addEventListener('change', toggleEventSections);
        toggleEventSections(); // Initial call to set up initial state

        // Populate projects via AJAX (placeholder)
        const projetSelect = document.getElementById('projetId');
        fetch('<?php echo $this->url('projects/get-list'); ?>')
            .then(response => response.json())
            .then(projects => {
                projects.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.id;
                    option.textContent = project.titre;
                    projetSelect.appendChild(option);
                });
            });
    });
</script>