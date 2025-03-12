<!-- views/bureau/index.php -->
<div class="bureau-members-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Membres du Bureau Exécutif</h1>
        <?php if ($auth->hasPermission('create_board_member')): ?>
            <a href="<?php echo $this->url('admin/bureau/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un membre
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($bureauMembers)): ?>
        <div class="alert alert-info">
            Aucun membre du bureau exécutif n'a été trouvé.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Rôle</th>
                    <th>Email</th>
                    <th>Chercheur</th>
                    <th>Mandat</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bureauMembers as $member): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <?php echo strtoupper(substr($member['prenom'], 0, 1) . substr($member['nom'], 0, 1)); ?>
                                    </span>
                                <?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            $roleBadgeClass = 'bg-secondary';
                            $roleIcon = 'fas fa-user-tie';
                            $roleText = $member['role'];

                            switch($member['role']) {
                                case 'President':
                                    $roleText = 'Président';
                                    $roleIcon = 'fas fa-star';
                                    $roleBadgeClass = 'bg-warning text-dark';
                                    break;
                                case 'VicePresident':
                                    $roleText = 'Vice-président';
                                    $roleIcon = 'fas fa-star-half-alt';
                                    $roleBadgeClass = 'bg-warning text-dark';
                                    break;
                                case 'GeneralSecretary':
                                    $roleText = 'Secrétaire Général';
                                    $roleIcon = 'fas fa-pen';
                                    break;
                                case 'Treasurer':
                                    $roleText = 'Trésorier';
                                    $roleIcon = 'fas fa-money-bill-wave';
                                    $roleBadgeClass = 'bg-success';
                                    break;
                                case 'ViceTreasurer':
                                    $roleText = 'Vice-trésorier';
                                    $roleIcon = 'fas fa-coins';
                                    $roleBadgeClass = 'bg-success';
                                    break;
                                case 'Counselor':
                                    $roleText = 'Conseiller';
                                    break;
                            }
                            ?>
                            <span class="badge <?php echo $roleBadgeClass; ?>">
                                    <i class="<?php echo $roleIcon; ?> me-1"></i>
                                    <?php echo $roleText; ?>
                                </span>
                        </td>
                        <td><?php echo $this->escape($member['email']); ?></td>
                        <td>
                            <?php if ($member['chercheurId']): ?>
                                <span class="badge bg-info">
                                        <i class="fas fa-flask me-1"></i> Oui
                                    </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Non</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $member['Mandat'] ? $this->formatCurrency($member['Mandat']) : '-'; ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo $this->url('users/' . $member['utilisateurId']); ?>" class="btn btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($auth->hasPermission('edit_board_member')): ?>
                                    <a href="<?php echo $this->url('admin/bureau/edit/' . $member['utilisateurId']); ?>" class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($auth->hasPermission('delete_board_member')): ?>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo $member['utilisateurId']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal-<?php echo $member['utilisateurId']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Êtes-vous sûr de vouloir retirer ce membre du bureau exécutif?</p>
                                            <p><strong><?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?></strong></p>
                                            <p>Cette action ne supprimera pas le compte utilisateur ni le statut de chercheur, le cas échéant.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <form action="<?php echo $this->url('admin/bureau/delete/' . $member['utilisateurId']); ?>" method="post">
                                                <?php echo CSRF::tokenField(); ?>
                                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>