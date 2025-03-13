<!-- views/bureau/index.php -->
<div class="bureau-members">
    <h1 class="mb-4">Membres du Bureau Exécutif</h1>

    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des membres du bureau</h5>
                        <?php if ($auth->hasRole('admin')): ?>
                            <a href="<?php echo $this->url('admin/bureau/create'); ?>" class="btn btn-light btn-sm">
                                <i class="fas fa-plus"></i> Ajouter un membre
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bureau Members Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Mandat</th>
                                <th>Chercheur</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($bureauMembers)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Aucun membre du bureau trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bureauMembers as $member): ?>
                                    <tr>
                                        <td><?php echo $member['utilisateurId']; ?></td>
                                        <td><?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?></td>
                                        <td><?php echo $this->escape($member['email']); ?></td>
                                        <td>
                                                <span class="badge bg-info">
                                                    <?php echo $this->escape($member['role']); ?>
                                                </span>
                                        </td>
                                        <td><?php echo $member['Mandat']; ?></td>
                                        <td>
                                            <?php if ($member['chercheurId']): ?>
                                                <span class="badge bg-success">Oui</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Non</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo $this->url('users/' . $member['utilisateurId']); ?>" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($auth->hasRole('admin')): ?>
                                                    <a href="<?php echo $this->url('admin/bureau/edit/' . $member['utilisateurId']); ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $member['utilisateurId']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Delete Modal -->
                                            <?php if ($auth->hasRole('admin')): ?>
                                                <div class="modal fade" id="deleteModal<?php echo $member['utilisateurId']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Êtes-vous sûr de vouloir retirer <strong><?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?></strong> du bureau exécutif ?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form action="<?php echo $this->url('admin/bureau/delete/' . $member['utilisateurId']); ?>" method="post">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
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