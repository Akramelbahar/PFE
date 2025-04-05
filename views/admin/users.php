<!-- views/admin/users.php -->
<div class="admin-users">
    <h1 class="mb-4">Gestion des utilisateurs</h1>

    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des utilisateurs</h5>
                        <a href="<?php echo $this->url('admin/users/create'); ?>" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Ajouter un utilisateur
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="filters mb-4">
                        <form action="<?php echo $this->url('admin/users'); ?>" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label for="role" class="form-label">Rôle</label>
                                <select name="role" id="role" class="form-select">
                                    <option value="">Tous les rôles</option>
                                    <option value="admin" <?php echo isset($_GET['role']) && $_GET['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="chercheur" <?php echo isset($_GET['role']) && $_GET['role'] === 'chercheur' ? 'selected' : ''; ?>>Chercheur</option>
                                    <option value="membreBureauExecutif" <?php echo isset($_GET['role']) && $_GET['role'] === 'membreBureauExecutif' ? 'selected' : ''; ?>>Membre du Bureau</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="1" <?php echo isset($_GET['status']) && $_GET['status'] === '1' ? 'selected' : ''; ?>>Actif</option>
                                    <option value="0" <?php echo isset($_GET['status']) && $_GET['status'] === '0' ? 'selected' : ''; ?>>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_GET['search']) ? $this->escape($_GET['search']) : ''; ?>" placeholder="Nom, prénom ou email">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filtrer</button>
                                <a href="<?php echo $this->url('admin/users'); ?>" class="btn btn-outline-secondary ms-2">Réinitialiser</a>
                            </div>
                        </form>
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôles</th>
                                <th>Date d'inscription</th>
                                <th>Dernière connexion</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Aucun utilisateur trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?></td>
                                        <td><?php echo $this->escape($user['email']); ?></td>
                                        <td>
                                            <?php if (isset($user['roles'])): ?>
                                                <?php foreach ($user['roles'] as $role): ?>
                                                    <span class="badge bg-<?php
                                                    echo $role === 'admin' ? 'danger' :
                                                        ($role === 'chercheur' ? 'primary' :
                                                            ($role === 'membreBureauExecutif' ? 'success' : 'secondary'));
                                                    ?>">
                                            <?php echo $role; ?>
                                        </span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Utilisateur</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $this->formatDate($user['dateInscription']); ?></td>
                                        <td><?php echo $user['derniereConnexion'] ? $this->formatDate($user['derniereConnexion']) : '-'; ?></td>
                                        <td>
                                        <span class="badge bg-<?php echo $user['status'] ? 'success' : 'danger'; ?>">
                                            <?php echo $user['status'] ? 'Actif' : 'Inactif'; ?>
                                        </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo $this->url('users/' . $user['id']); ?>" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo $this->url('admin/users/edit/' . $user['id']); ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($user['id'] !== $auth->getUser()['id']): ?>
                                                    <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer l'utilisateur <strong><?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?></strong> ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <form action="<?php echo $this->url('admin/users/delete/' . $user['id']); ?>" method="post">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
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
        </div>
    </div>
</div>