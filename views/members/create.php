<!-- views/members/create.php -->
<div class="member-create-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Ajouter un membre</h1>
        <a href="<?php echo $this->url('members'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
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
            <h5 class="mb-0">Informations du membre</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo $this->url('members/create'); ?>" method="post" enctype="multipart/form-data">
                <?php echo CSRF::tokenField(); ?>

                <div class="mb-4">
                    <h4>Informations personnelles</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" id="nom" name="nom" class="form-control" required
                                   value="<?php echo isset($member['nom']) ? $this->escape($member['nom']) : ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" id="prenom" name="prenom" class="form-control" required
                                   value="<?php echo isset($member['prenom']) ? $this->escape($member['prenom']) : ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required
                                   value="<?php echo isset($member['email']) ? $this->escape($member['email']) : ''; ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password" class="form-control" required>
                            <div class="form-text">Minimum 6 caractères.</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h4>Type de membre</h4>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_chercheur" name="is_chercheur"
                                    <?php echo isset($member['is_chercheur']) && $member['is_chercheur'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_chercheur">
                                    Chercheur
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_bureau" name="is_bureau"
                                    <?php echo isset($member['is_bureau']) && $member['is_bureau'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_bureau">
                                    Membre du bureau exécutif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Researcher Fields (initially hidden) -->
                <div id="chercheur-fields" class="mb-4" style="display: none;">
                    <h4>Informations du chercheur</h4>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="domaine_recherche" class="form-label">Domaine de recherche</label>
                            <input type="text" id="domaine_recherche" name="domaine_recherche" class="form-control"
                                   value="<?php echo isset($member['domaine_recherche']) ? $this->escape($member['domaine_recherche']) : ''; ?>">
                        </div>

                        <div class="col-md-12">
                            <label for="bio" class="form-label">Bio / Présentation</label>
                            <textarea id="bio" name="bio" class="form-control" rows="3"><?php echo isset($member['bio']) ? $this->escape($member['bio']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Executive Board Fields (initially hidden) -->
                <div id="bureau-fields" class="mb-4" style="display: none;">
                    <h4>Informations du membre du bureau</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="role" class="form-label">Rôle dans le bureau</label>
                            <select id="role" name="role" class="form-select">
                                <option value="President" <?php echo isset($member['role']) && $member['role'] === 'President' ? 'selected' : ''; ?>>Président</option>
                                <option value="VicePresident" <?php echo isset($member['role']) && $member['role'] === 'VicePresident' ? 'selected' : ''; ?>>Vice-président</option>
                                <option value="GeneralSecretary" <?php echo isset($member['role']) && $member['role'] === 'GeneralSecretary' ? 'selected' : ''; ?>>Secrétaire général</option>
                                <option value="Treasurer" <?php echo isset($member['role']) && $member['role'] === 'Treasurer' ? 'selected' : ''; ?>>Trésorier</option>
                                <option value="ViceTreasurer" <?php echo isset($member['role']) && $member['role'] === 'ViceTreasurer' ? 'selected' : ''; ?>>Vice-trésorier</option>
                                <option value="Counselor" <?php echo isset($member['role']) && $member['role'] === 'Counselor' ? 'selected' : ''; ?>>Conseiller</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="mandat" class="form-label">Mandat</label>
                            <input type="number" id="mandat" name="mandat" class="form-control" step="0.01"
                                   value="<?php echo isset($member['mandat']) ? $this->escape($member['mandat']) : ''; ?>">
                        </div>

                        <div class="col-md-12">
                            <label for="permissions" class="form-label">Permissions</label>
                            <textarea id="permissions" name="permissions" class="form-control" rows="3"><?php echo isset($member['permissions']) ? $this->escape($member['permissions']) : ''; ?></textarea>
                            <div class="form-text">Entrez les permissions séparées par des virgules (ex: create_project,edit_news)</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h4>Photo de profil</h4>
                    <div class="mb-3">
                        <label for="profile_picture" class="form-label">Photo de profil</label>
                        <input type="file" id="profile_picture" name="profile_picture" class="form-control" accept="image/jpeg,image/png,image/gif">
                        <div class="form-text">Formats acceptés: JPG, PNG, GIF. Taille max: 5 Mo.</div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Ajouter le membre</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isChercheur = document.getElementById('is_chercheur');
        const isBureau = document.getElementById('is_bureau');
        const chercheurFields = document.getElementById('chercheur-fields');
        const bureauFields = document.getElementById('bureau-fields');

        function updateFields() {
            chercheurFields.style.display = isChercheur.checked ? 'block' : 'none';
            bureauFields.style.display = isBureau.checked ? 'block' : 'none';
        }

        isChercheur.addEventListener('change', updateFields);
        isBureau.addEventListener('change', updateFields);

        // Initialize state
        updateFields();
    });
</script>