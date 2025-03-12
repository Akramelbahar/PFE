<!-- views/evenements/create.php -->
<div class="event-create-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Créer un événement</h1>
        <a href="<?php echo $this->url('events'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux événements
        </a>
    </div>

    <?php if (isset($errors) && $errors): ?>
        <div class="alert alert-danger">
            <h5 class="alert-heading">Erreurs de validation</h5>
            <ul class="mb-0">
                <?php foreach ($errors as $field => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <li><?php echo ucfirst($field); ?>: <?php echo $error; ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informations de l'événement</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo $this->url('events/create'); ?>" method="post" enctype="multipart/form-data">
                <?php echo CSRF::tokenField(); ?>

                <!-- Common Event Information -->
                <div class="mb-4">
                    <h4>Informations générales</h4>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" id="titre" name="titre" class="form-control" required
                                   value="<?php echo isset($data['titre']) ? $this->escape($data['titre']) : ''; ?>">
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea id="description" name="description" class="form-control" rows="5" required><?php echo isset($data['description']) ? $this->escape($data['description']) : ''; ?></textarea>
                        </div>

                        <div class="col-md-12">
                            <label for="lieu" class="form-label">Lieu <span class="text-danger">*</span></label>
                            <input type="text" id="lieu" name="lieu" class="form-control" required
                                   value="<?php echo isset($data['lieu']) ? $this->escape($data['lieu']) : ''; ?>">
                        </div>

                        <?php if (!empty($projets)): ?>
                            <div class="col-md-12">
                                <label for="projetId" class="form-label">Projet associé</label>
                                <select id="projetId" name="projetId" class="form-select">
                                    <option value="">Aucun projet associé</option>
                                    <?php foreach ($projets as $projet): ?>
                                        <option value="<?php echo $projet['id']; ?>" <?php echo isset($data['projetId']) && $data['projetId'] == $projet['id'] ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($projet['titre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-12">
                            <label for="type" class="form-label">Type d'événement <span class="text-danger">*</span></label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Seminaire" <?php echo isset($data['type']) && $data['type'] === 'Seminaire' ? 'selected' : ''; ?>>Séminaire</option>
                                <option value="Conference" <?php echo isset($data['type']) && $data['type'] === 'Conference' ? 'selected' : ''; ?>>Conférence</option>
                                <option value="Workshop" <?php echo isset($data['type']) && $data['type'] === 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Seminar-specific fields -->
                <div class="event-specific-fields" id="seminaireFields" style="display: none;">
                    <div class="mb-4">
                        <h4>Détails du séminaire</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="seminaire-date" class="form-label">Date du séminaire <span class="text-danger">*</span></label>
                                <input type="date" id="seminaire-date" name="date" class="form-control"
                                       value="<?php echo isset($data['date']) ? $this->escape($data['date']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conference-specific fields -->
                <div class="event-specific-fields" id="conferenceFields" style="display: none;">
                    <div class="mb-4">
                        <h4>Détails de la conférence</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="conference-dateDebut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" id="conference-dateDebut" name="dateDebut" class="form-control"
                                       value="<?php echo isset($data['dateDebut']) ? $this->escape($data['dateDebut']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="conference-dateFin" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" id="conference-dateFin" name="dateFin" class="form-control"
                                       value="<?php echo isset($data['dateFin']) ? $this->escape($data['dateFin']) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Workshop-specific fields -->
                <div class="event-specific-fields" id="workshopFields" style="display: none;">
                    <div class="mb-4">
                        <h4>Détails du workshop</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="workshop-dateDebut" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" id="workshop-dateDebut" name="dateDebut" class="form-control"
                                       value="<?php echo isset($data['dateDebut']) ? $this->escape($data['dateDebut']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="workshop-dateFin" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" id="workshop-dateFin" name="dateFin" class="form-control"
                                       value="<?php echo isset($data['dateFin']) ? $this->escape($data['dateFin']) : ''; ?>">
                            </div>
                            <div class="col-md-12">
                                <label for="instructorId" class="form-label">Instructeur</label>
                                <select id="instructorId" name="instructorId" class="form-select">
                                    <option value="">Sélectionnez un instructeur</option>
                                    <?php foreach ($chercheurs as $chercheur): ?>
                                        <option value="<?php echo $chercheur['utilisateurId']; ?>" <?php echo isset($data['instructorId']) && $data['instructorId'] == $chercheur['utilisateurId'] ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($chercheur['prenom'] . ' ' . $chercheur['nom']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents upload -->
                <div class="mb-4">
                    <h4>Documents</h4>
                    <div class="mb-3">
                        <label for="documents" class="form-label">Ajouter des documents (PDF, Word, Images)</label>
                        <input type="file" id="documents" name="documents[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <div class="form-text">Vous pouvez sélectionner plusieurs fichiers. Taille maximale: 5 Mo par fichier.</div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Créer l'événement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide event-specific fields based on selected type
        var typeSelect = document.getElementById('type');
        var seminaireFields = document.getElementById('seminaireFields');
        var conferenceFields = document.getElementById('conferenceFields');
        var workshopFields = document.getElementById('workshopFields');

        function updateFields() {
            // Hide all specific fields
            seminaireFields.style.display = 'none';
            conferenceFields.style.display = 'none';
            workshopFields.style.display = 'none';

            // Show fields based on selection
            switch(typeSelect.value) {
                case 'Seminaire':
                    seminaireFields.style.display = 'block';
                    break;
                case 'Conference':
                    conferenceFields.style.display = 'block';
                    break;
                case 'Workshop':
                    workshopFields.style.display = 'block';
                    break;
            }
        }

        // Set initial display state
        updateFields();

        // Update on change
        typeSelect.addEventListener('change', updateFields);
    });
</script>