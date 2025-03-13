<!-- views/user/index.php -->
<div class="user-management-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Utilisateurs</h1>
        <a href="<?php echo $this->url('admin/users/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Créer un utilisateur
        </a>
    </div>
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Liste des Utilisateurs</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôles</th>
                    <th>Date d'inscription</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="alert alert-info mb-0">Aucun utilisateur trouvé</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $this->escape($user['id']); ?></td>
                            <td>
                                <?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?>
                            </td>
                            <td><?php echo $this->escape($user['email']); ?></td>
                            <td>
                                <?php
                                $userRoles = [];
                                if (!empty($user['roles'])) {
                                    foreach (explode(',', $user['roles']) as $role) {
                                        switch ($role) {
                                            case 'admin':
                                                $userRoles[] = '<span class="badge bg-danger">Administrateur</span>';
                                                break;
                                            case 'chercheur':
                                                $userRoles[] = '<span class="badge bg-success">Chercheur</span>';
                                                break;
                                            case 'membreBureauExecutif':
                                                $userRoles[] = '<span class="badge bg-warning">Membre Bureau</span>';
                                                break;
                                            default:
                                                $userRoles[] = '<span class="badge bg-secondary">' . ucfirst($role) . '</span>';
                                        }
                                    }
                                }
                                echo implode(' ', $userRoles);
                                ?>
                            </td>
                            <td><?php echo $this->formatDate($user['dateInscription']); ?></td>
                            <td>
                                <?php if ($user['status']): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo $this->url('users/' . $user['id']); ?>"
                                       class="btn btn-sm btn-info"
                                       title="Voir le profil">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo $this->url('admin/users/edit/' . $user['id']); ?>"
                                       class="btn btn-sm btn-warning"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-danger delete-user"
                                            data-user-id="<?php echo $user['id']; ?>"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Delete User Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteUserForm" method="post">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-user');
        const deleteUserModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        const deleteUserForm = document.getElementById('deleteUserForm');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                deleteUserForm.action = `<?php echo $this->url('admin/users/delete/'); ?>${userId}`;
                deleteUserModal.show();
            });
        });
    });
</script>