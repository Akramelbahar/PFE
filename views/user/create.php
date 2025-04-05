<!-- views/user/create.php -->
<div class="user-create">
    <h1 class="mb-4">Créer un utilisateur</h1>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Formulaire de création</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $this->url('admin/users/create'); ?>" method="post" class="needs-validation" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo isset($nom) ? $this->escape($nom) : ''; ?>" required>
                                <div class="invalid-feedback">Le nom est requis.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo isset($prenom) ? $this->escape($prenom) : ''; ?>" required>
                                <div class="invalid-feedback">Le prénom est requis.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $this->escape($email) : ''; ?>" required>
                                <div class="invalid-feedback">Un email valide est requis.</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="invalid-feedback">Le mot de passe est requis (minimum 6 caractères).</div>
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label">Rôle principal</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="admin" <?php echo isset($role) && $role === 'admin' ? 'selected' : ''; ?>>Administrateur</option>
                                    <option value="chercheur" <?php echo isset($role) && $role === 'chercheur' ? 'selected' : ''; ?>>Chercheur</option>
                                    <option value="membreBureauExecutif" <?php echo isset($role) && $role === 'membreBureauExecutif' ? 'selected' : ''; ?>>Membre du Bureau Exécutif</option>
                                </select>
                                <div class="invalid-feedback">Veuillez sélectionner un rôle.</div>
                            </div>
                        </div>

                        <!-- Chercheur Details -->
                        <div id="chercheur_details" class="row mb-3" style="display: none;">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Détails du chercheur</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="domaine_recherche" class="form-label">Domaine de recherche</label>
                                                <input type="text" class="form-control" id="domaine_recherche" name="domaine_recherche"
                                                       value="<?php echo isset($domaine_recherche) ? $this->escape($domaine_recherche) : ''; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="bio" class="form-label">Biographie</label>
                                                <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo isset($bio) ? $this->escape($bio) : ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bureau Member Details -->
                        <div id="bureau_details" class="row mb-3" style="display: none;">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Détails du membre du bureau exécutif</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="membre_role" class="form-label">Rôle dans le bureau</label>
                                                <select class="form-select" id="membre_role" name="membre_role">
                                                    <option value="">Sélectionner un rôle</option>
                                                    <option value="President" <?php echo isset($membre_role) && $membre_role === 'President' ? 'selected' : ''; ?>>Président</option>
                                                    <option value="VicePresident" <?php echo isset($membre_role) && $membre_role === 'VicePresident' ? 'selected' : ''; ?>>Vice-Président</option>
                                                    <option value="GeneralSecretary" <?php echo isset($membre_role) && $membre_role === 'GeneralSecretary' ? 'selected' : ''; ?>>Secrétaire Général</option>
                                                    <option value="Treasurer" <?php echo isset($membre_role) && $membre_role === 'Treasurer' ? 'selected' : ''; ?>>Trésorier</option>
                                                    <option value="ViceTreasurer" <?php echo isset($membre_role) && $membre_role === 'ViceTreasurer' ? 'selected' : ''; ?>>Vice-Trésorier</option>
                                                    <option value="Counselor" <?php echo isset($membre_role) && $membre_role === 'Counselor' ? 'selected' : ''; ?>>Conseiller</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="mandat" class="form-label">Mandat (années)</label>
                                                <input type="number" class="form-control" id="mandat" name="mandat" min="0" step="0.5"
                                                       value="<?php echo isset($mandat) ? $mandat : ''; ?>">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_chercheur" name="is_chercheur"
                                                        <?php echo isset($is_chercheur) && $is_chercheur ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="is_chercheur">
                                                        Ce membre est également un chercheur
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="bureau_chercheur_details" class="row mt-3" style="display: none;">
                                            <div class="col-md-6">
                                                <label for="chercheur_domaine" class="form-label">Domaine de recherche</label>
                                                <input type="text" class="form-control" id="chercheur_domaine" name="chercheur_domaine"
                                                       value="<?php echo isset($chercheur_domaine) ? $this->escape($chercheur_domaine) : ''; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="chercheur_bio" class="form-label">Biographie</label>
                                                <textarea class="form-control" id="chercheur_bio" name="chercheur_bio" rows="3"><?php echo isset($chercheur_bio) ? $this->escape($chercheur_bio) : ''; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <label class="form-label">Permissions</label>
                                                <div class="border p-3 rounded">
                                                    <div class="row">
                                                        <div class="col-md-3 mb-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input select-all" type="checkbox" id="select_all">
                                                                <label class="form-check-label fw-bold" for="select_all">
                                                                    Sélectionner tout
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <?php
                                                        if (class_exists('PermissionsHelper')) {
                                                            $categories = PermissionsHelper::getPermissionsByCategory();

                                                            foreach ($categories as $category => $categoryPermissions): ?>
                                                                <div class="col-md-4 mb-3">
                                                                    <h6><?php echo PermissionsHelper::getPermissionCategories()[$category]; ?></h6>
                                                                    <?php foreach ($categoryPermissions as $key => $label): ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" id="<?php echo $key; ?>" value="<?php echo $key; ?>">
                                                                            <label class="form-check-label" for="<?php echo $key; ?>">
                                                                                <?php echo $label; ?>
                                                                            </label>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endforeach;
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?php echo $this->url('admin/users'); ?>" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle role details based on selection
        const roleSelect = document.getElementById('role');
        const chercheurDetails = document.getElementById('chercheur_details');
        const bureauDetails = document.getElementById('bureau_details');

        roleSelect.addEventListener('change', function() {
            chercheurDetails.style.display = this.value === 'chercheur' ? 'flex' : 'none';
            bureauDetails.style.display = this.value === 'membreBureauExecutif' ? 'flex' : 'none';
        });

        // Toggle researcher fields for bureau member
        const isChercheurCheckbox = document.getElementById('is_chercheur');
        const bureauChercheurDetails = document.getElementById('bureau_chercheur_details');

        isChercheurCheckbox.addEventListener('change', function() {
            bureauChercheurDetails.style.display = this.checked ? 'flex' : 'none';
        });

        // Set initial display based on existing values
        if (roleSelect.value === 'chercheur') {
            chercheurDetails.style.display = 'flex';
        } else if (roleSelect.value === 'membreBureauExecutif') {
            bureauDetails.style.display = 'flex';
            if (isChercheurCheckbox.checked) {
                bureauChercheurDetails.style.display = 'flex';
            }
        }

        // Select all permissions
        const selectAll = document.getElementById('select_all');
        if (selectAll) {
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

            selectAll.addEventListener('change', function() {
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Check if all permissions are selected
            permissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = [...permissionCheckboxes].every(cb => cb.checked);
                    selectAll.checked = allChecked;
                });
            });
        }
    });
</script>