<!-- views/evenements/edit.php -->
<div class="events-edit-page">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Modifier l'événement</h3>
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

                    <form action="<?php echo $this->url('events/edit/' . $event['id']); ?>" method="post" enctype="multipart/form-data">
                        <?php echo CSRF::tokenField(); ?>

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" id="titre" name="titre"
                                   class="form-control"
                                   value="<?php echo $this->escape($event['titre']); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea id="description" name="description"
                                      class="form-control"
                                      rows="4"
                                      required><?php echo $this->escape($event['description']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="lieu" class="form-label">Lieu <span class="text-danger">*</span></label>
                            <input type="text" id="lieu" name="lieu"
                                   class="form-control"
                                   value="<?php echo $this->escape($event['lieu']); ?>"
                                   required>
                        </div>

                        <!-- Seminaire-specific Date -->
                        <?php if ($eventType === 'Seminaire'): ?>
                            <div class="mb-3">
                                <label for="date" class="form-label">Date du séminaire <span class="text-danger">*</span></label>
                                <input type="date" id="date" name="date"
                                       class="form-control"
                                       value="<?php echo date('Y-m-d', strtotime($specificDetails['date'])); ?>"
                                       required>
                            </div>
                        <?php endif; ?>

                        <!-- Conference and Workshop Date Range -->
                        <?php if (in_array($eventType, ['Conference', 'Workshop'])): ?>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="dateDebut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                    <input type="date" id="dateDebut" name="dateDebut"
                                           class="form-control"
                                           value="<?php echo date('Y-m-d', strtotime($specificDetails['dateDebut'])); ?>"
                                           required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="dateFin" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                    <input type="date" id="dateFin" name="dateFin"
                                           class="form-control"
                                           value="<?php echo date('Y-m-d', strtotime($specificDetails['dateFin'])); ?>"
                                           min="<?php echo date('Y-m-d', strtotime($specificDetails['dateDebut'])); ?>"
                                           required>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Workshop Instructor (for Workshop only) -->
                        <?php if ($eventType === 'Workshop'): ?>
                            <div class="mb-3">
                                <label for="instructorId" class="form-label">Animateur</label>
                                <select id="instructorId" name="instructorId" class="form-select">
                                    <option value="">Sélectionner un animateur</option>
                                    <?php foreach ($chercheurs as $chercheur): ?>
                                        <option value="<?php echo $this->escape($chercheur['id']); ?>"
                                            <?php echo ($specificDetails['instructorId'] == $chercheur['id']) ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="projetId" class="form-label">Projet associé</label>
                            <select id="projetId" name="projetId" class="form-select">
                                <option value="">Aucun projet</option>
                                <!-- Projects will be populated dynamically -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="documents" class="form-label">Nouveaux documents</label>
                            <input type="file" id="documents" name="documents[]"
                                   class="form-control"
                                   multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Vous pouvez télécharger plusieurs fichiers</div>
                        </div>

                        <!-- Existing Documents -->
                        <?php if (!empty($documents)): ?>
                            <div class="card mb-3">
                                <div class="card-header">Documents existants</div>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($documents as $document): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-file me-2"></i>
                                                <?php echo $this->escape($document['filename']); ?>
                                            </span>
                                            <div>
                                                <span class="text-muted me-3">
                                                    <?php echo round($document['size'] / 1024, 2); ?> Ko
                                                </span>
                                                <a href="<?php echo $this->url('events/download-document/' . $event['id'] . '/' . $document['filename']); ?>"
                                                   class="btn btn-sm btn-outline-primary me-2"
                                                   title="Télécharger">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="#"
                                                   onclick="deleteDocument('<?php echo $this->url('events/delete-document/' . $event['id'] . '/' . urlencode($document['filename'])); ?>')"
                                                   class="btn btn-sm btn-outline-danger"
                                                   title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo $this->url('events/' . $event['id']); ?>" class="btn btn-secondary">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
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
            // S'assurer que la date minimale pour dateFin est bien définie
            dateFin.min = dateDebut.value;

            dateDebut.addEventListener('change', function() {
                // Mettre à jour la date minimale pour dateFin
                dateFin.min = this.value;

                // Si la date de fin est antérieure à la nouvelle date de début, la réinitialiser
                if (dateFin.value && new Date(dateFin.value) < new Date(this.value)) {
                    dateFin.value = this.value;
                }
            });
        }

        // Document deletion confirmation
        const deleteDocumentButtons = document.querySelectorAll('.delete-document');
        deleteDocumentButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Voulez-vous vraiment supprimer ce document ?')) {
                    e.preventDefault();
                }
            });
        });

        // Populate projects
        const projetSelect = document.getElementById('projetId');
        fetch('<?php echo $this->url('projects/get-list'); ?>')
            .then(response => response.json())
            .then(projects => {
                const currentProjectId = <?php echo json_encode($event['projetId'] ?? null); ?>;
                projects.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.id;
                    option.textContent = project.titre;
                    option.selected = project.id == currentProjectId;
                    projetSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Erreur lors du chargement des projets:', error));
    });
    // Document deletion with POST request
    function deleteDocument(url) {
        if (confirm('Voulez-vous vraiment supprimer ce document ?')) {
            // Create a form to submit the delete request as POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            // Add CSRF token if needed
            <?php if (function_exists('CSRF::getToken')): ?>
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '<?php echo CSRF::getToken(); ?>';
            form.appendChild(csrfInput);
            <?php endif; ?>

            document.body.appendChild(form);
            form.submit();
        }
        return false;
    }
</script>