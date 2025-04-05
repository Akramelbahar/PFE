<!-- views/bureau/create.php -->
<div class="bureau-create">
    <h1 class="mb-4">Ajouter un Membre au Bureau Exécutif</h1>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Formulaire d'ajout</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $this->url('admin/bureau/create'); ?>" method="post" class="needs-validation" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Utilisateur</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">Sélectionner un utilisateur</option>
                                    <?php foreach ($eligibleUsers as $user): ?>
                                        <option value="<?php echo $user['id']; ?>">
                                            <?php echo $this->escape($user['prenom'] . ' ' . $user['nom'] . ' (' . $user['email'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner un utilisateur.
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label">Rôle dans le bureau</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="President">Président</option>
                                    <option value="VicePresident">Vice-Président</option>
                                    <option value="GeneralSecretary">Secrétaire Général</option>
                                    <option value="Treasurer">Trésorier</option>
                                    <option value="ViceTreasurer">Vice-Trésorier</option>
                                    <option value="Counselor">Conseiller</option>
                                </select>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner un rôle.
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mandat" class="form-label">Mandat (années)</label>
                                <input type="number" name="mandat" id="mandat" class="form-control" min="0" step="0.5" required>
                                <div class="invalid-feedback">
                                    Veuillez spécifier la durée du mandat.
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_chercheur" id="is_chercheur">
                                    <label class="form-check-label" for="is_chercheur">
                                        Ce membre est également un chercheur
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="chercheur_fields" class="row mb-3" style="display: none;">
                            <div class="col-md-6">
                                <label for="domaine_recherche" class="form-label">Domaine de recherche</label>
                                <input type="text" name="domaine_recherche" id="domaine_recherche" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="bio" class="form-label">Biographie</label>
                                <textarea name="bio" id="bio" class="form-control" rows="3"></textarea>
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
                                        $permissions = PermissionsHelper::getAllPermissions();
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
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?php echo $this->url('admin/bureau'); ?>" class="btn btn-secondary">Annuler</a>
                            <button type="submit" class="btn btn-primary">Ajouter au bureau exécutif</button>
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