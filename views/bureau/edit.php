<!-- views/bureau/edit.php -->
<div class="bureau-edit">
    <h1 class="mb-4">Modifier un Membre du Bureau Exécutif</h1>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Formulaire de modification</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $this->url('admin/bureau/edit/' . $member['utilisateurId']); ?>" method="post" class="needs-validation" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Utilisateur</label>
                                <input type="text" class="form-control" value="<?php echo $this->escape($member['prenom'] . ' ' . $member['nom'] . ' (' . $member['email'] . ')'); ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label">Rôle dans le bureau</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="President" <?php echo $member['role'] === 'President' ? 'selected' : ''; ?>>Président</option>
                                    <option value="VicePresident" <?php echo $member['role'] === 'VicePresident' ? 'selected' : ''; ?>>Vice-Président</option>
                                    <option value="GeneralSecretary" <?php echo $member['role'] === 'GeneralSecretary' ? 'selected' : ''; ?>>Secrétaire Général</option>
                                    <option value="Treasurer" <?php echo $member['role'] === 'Treasurer' ? 'selected' : ''; ?>>Trésorier</option>
                                    <option value="ViceTreasurer" <?php echo $member['role'] === 'ViceTreasurer' ? 'selected' : ''; ?>>Vice-Trésorier</option>
                                    <option value="Counselor" <?php echo $member['role'] === 'Counselor' ? 'selected' : ''; ?>>Conseiller</option>
                                </select>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner un rôle.
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mandat" class="form-label">Mandat (années)</label>
                                <input type="number" name="mandat" id="mandat" class="form-control" min="0" step="0.5" value="<?php echo $member['Mandat']; ?>" required>
                                <div class="invalid-feedback">
                                    Veuillez spécifier la durée du mandat.
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_chercheur" id="is_chercheur" <?php echo $member['chercheurId'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_chercheur">
                                        Ce membre est également un chercheur
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="chercheur_fields" class="row mb-3" style="display: <?php echo $member['chercheurId'] ? 'flex' : 'none'; ?>;">
                            <div class="col-md-6">
                                <label for="domaine_recherche" class="form-label">Domaine de recherche</label>
                                <input type="text" name="domaine_recherche" id="domaine_recherche" class="form-control" value="<?php echo isset($member['domaineRecherche']) ? $this->escape($member['domaineRecherche']) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="bio" class="form-label">Biographie</label>
                                <textarea name="bio" id="bio" class="form-control" rows="3"><?php echo isset($member['bio']) ? $this->escape($member['bio']) : ''; ?></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
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
                                        $memberPermissions = explode(',', $member['permissions'] ?? '');
                                        $permissions = PermissionsHelper::getAllPermissions();
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
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?php echo $this->url('admin/bureau'); ?>" class="btn btn-secondary">Annuler</a>
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
        // Toggle researcher fields visibility
        const checkboxChercheur = document.getElementById('is_chercheur');
        const chercheurFields = document.getElementById('chercheur_fields');

        checkboxChercheur.addEventListener('change', function() {
            chercheurFields.style.display = this.checked ? 'flex' : 'none';
        });

        // Select all permissions
        const selectAll = document.getElementById('select_all');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

        // Initial state of select_all checkbox
        selectAll.checked = [...permissionCheckboxes].every(cb => cb.checked);

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
    });
</script>