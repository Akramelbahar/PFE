<!-- views/user/edit.php -->
<div class="user-edit">
    <h1 class="mb-4">Modifier l'utilisateur</h1>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Formulaire de modification</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $this->url('admin/users/edit/' . $user['id']); ?>" method="post" class="needs-validation" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $this->escape($user['nom']); ?>" required>
                                <div class="invalid-feedback">Le nom est requis.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $this->escape($user['prenom']); ?>" required>
                                <div class="invalid-feedback">Le prénom est requis.</div>
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $this->escape($user['email']); ?>" required>
                                <div class="invalid-feedback">Un email valide est requis.</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Laisser vide pour ne pas changer">
                                <div class="form-text">Minimum 6 caractères. Laissez vide pour conserver le mot de passe actuel.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Statut</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="status" name="status" <?php echo $user['status'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="status">Compte actif</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Rôles</label>
                                <div class="border p-3 rounded">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="role_admin" name="roles[]" value="admin"
                                                    <?php echo in_array('admin', $userRoles) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="role_admin">Administrateur</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="role_chercheur" name="roles[]" value="chercheur"
                                                    <?php echo in_array('chercheur', $userRoles) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="role_chercheur">Chercheur</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="role_bureau" name="roles[]" value="membreBureauExecutif"
                                                    <?php echo in_array('membreBureauExecutif', $userRoles) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="role_bureau">Membre du Bureau Exécutif</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chercheur Details -->
                        <div id="chercheur_details" class="row mb-3" style="display: <?php echo in_array('chercheur', $userRoles) ? 'flex' : 'none'; ?>;">
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
                                                       value="<?php echo isset($userDetails['chercheur']['domaineRecherche']) ? $this->escape($userDetails['chercheur']['domaineRecherche']) : ''; ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="bio" class="form-label">Biographie</label>
                                                <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo isset($userDetails['chercheur']['bio']) ? $this->escape($userDetails['chercheur']['bio']) : ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bureau Member Details -->
                        <div id="bureau_details" class="row mb-3" style="display: <?php echo in_array('membreBureauExecutif', $userRoles) ? 'flex' : 'none'; ?>;">
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
                                                    <option value="President" <?php echo isset($userDetails['membreBureauExecutif']['role']) && $userDetails['membreBureauExecutif']['role'] === 'President' ? 'selected' : ''; ?>>Président</option>
                                                    <option value="VicePresident" <?php echo isset($userDetails['membreBureauExecutif']['role']) && $userDetails['membreBureauExecutif']['role'] === 'VicePresident' ? 'selected' : ''; ?>>Vice-Président</option>
                                                    <option value="GeneralSecretary" <?php echo isset($userDetails['membreBureauExecutif']['role']) && $userDetails['membreBureauExecutif']['role'] === 'GeneralSecretary' ? 'selected' : ''; ?>>Secrétaire Général</option>
                                                    <option value="Treasurer" <?php echo isset($userDetails['membreBureauExecutif']['role']) && $userDetails['membreBureauExecutif']['role'] === 'Treasurer' ? 'selected' : ''; ?>>Trésorier</option>
                                                    <option value="ViceTreasurer" <?php echo isset($userDetails['membreBureauExecutif']['role']) && $userDetails['membreBureauExecutif']['role'] === 'ViceTreasurer' ? 'selected' : ''; ?>>Vice-Trésorier</option>
                                                    <option value="Counselor" <?php echo isset($userDetails['membreBureauExecutif']['role']) && $userDetails['membreBureauExecutif']['role'] === 'Counselor' ? 'selected' : ''; ?>>Conseiller</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="mandat" class="form-label">Mandat (années)</label>
                                                <input type="number" class="form-control" id="mandat" name="mandat" min="0" step="0.5"
                                                       value="<?php echo isset($userDetails['membreBureauExecutif']['Mandat']) ? $userDetails['membreBureauExecutif']['Mandat'] : ''; ?>">
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
                                                            $memberPermissions = isset($userDetails['membreBureauExecutif']['permissions']) ?
                                                                explode(',', $userDetails['membreBureauExecutif']['permissions']) : [];
                                                            $categories = PermissionsHelper::getPermissionsByCategory();

                                                            foreach ($categories as $category => $categoryPermissions): ?>
                                                                <div class="col-md-4 mb-3">
                                                                    <h6><?php echo PermissionsHelper::getPermissionCategories()[$category]; ?></h6>
                                                                    <?php foreach ($categoryPermissions as $key => $label): ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" id="<?php echo $key; ?>" value="<?php echo $key; ?>"
                                                                                <?php echo in_array($key, $memberPermissions) ? 'checked' : ''; ?>>
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
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle role details
        const chercheurCheckbox = document.getElementById('role_chercheur');
        const bureauCheckbox = document.getElementById('role_bureau');
        const chercheurDetails = document.getElementById('chercheur_details');
        const bureauDetails = document.getElementById('bureau_details');

        chercheurCheckbox.addEventListener('change', function() {
            chercheurDetails.style.display = this.checked ? 'flex' : 'none';
        });

        bureauCheckbox.addEventListener('change', function() {
            bureauDetails.style.display = this.checked ? 'flex' : 'none';
        });

        // Select all permissions
        const selectAll = document.getElementById('select_all');
        if (selectAll) {
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

            // Initial state of select_all checkbox
            selectAll.checked = permissionCheckboxes.length > 0 &&
                [...permissionCheckboxes].every(cb => cb.checked);

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