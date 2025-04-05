<!-- views/admin/dashboard.php -->
<div class="admin-dashboard">
    <h1 class="mb-4">Tableau de bord d'administration</h1>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Utilisateurs</h6>
                            <h1 class="display-4"><?php echo $counts['users']; ?></h1>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('admin/users'); ?>" class="text-white">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Événements</h6>
                            <h1 class="display-4"><?php echo $counts['events']; ?></h1>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('admin/events'); ?>" class="text-white">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Publications</h6>
                            <h1 class="display-4"><?php echo $counts['publications']; ?></h1>
                        </div>
                        <i class="fas fa-book fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('admin/publications'); ?>" class="text-white">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Projets</h6>
                            <h1 class="display-4"><?php echo $counts['projects']; ?></h1>
                        </div>
                        <i class="fas fa-project-diagram fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('admin/projects'); ?>" class="text-white">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Actualités</h6>
                            <h1 class="display-4"><?php echo $counts['news']; ?></h1>
                        </div>
                        <i class="fas fa-newspaper fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('admin/news'); ?>" class="text-white">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Messages</h6>
                            <h1 class="display-4"><?php echo $counts['contacts']; ?></h1>
                        </div>
                        <i class="fas fa-envelope fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('admin/contacts'); ?>" class="text-white">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Chercheurs</h6>
                            <h1 class="display-4"><?php echo $counts['researchers']; ?></h1>
                        </div>
                        <i class="fas fa-flask fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('admin/users?role=chercheur'); ?>" class="text-white">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Membres du Bureau</h6>
                            <h1 class="display-4"><?php echo $counts['boardMembers']; ?></h1>
                        </div>
                        <i class="fas fa-user-tie fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="<?php echo $this->url('admin/users?role=membreBureauExecutif'); ?>" class="text-white">Voir détails</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Items Sections -->
    <div class="row">
        <!-- Recent Users -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Utilisateurs récents</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentItems['users'] as $user): ?>
                                <tr>
                                    <td><?php echo $this->escape($user['prenom'] . ' ' . $user['nom']); ?></td>
                                    <td><?php echo $this->escape($user['email']); ?></td>
                                    <td><?php echo $this->formatDate($user['dateInscription']); ?></td>
                                    <td>
                                        <a href="<?php echo $this->url('users/' . $user['id']); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo $this->url('admin/users'); ?>" class="btn btn-primary btn-sm">Voir tous</a>
                </div>
            </div>
        </div>

        <!-- Recent Events -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Événements récents</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentItems['events'] as $event): ?>
                                <tr>
                                    <td><?php echo $this->escape($event['titre']); ?></td>
                                    <td><?php echo $this->escape($event['eventType']); ?></td>
                                    <td><?php echo $this->formatDate($event['eventDate'] ?? $event['dateCreation']); ?></td>
                                    <td>
                                        <a href="<?php echo $this->url('events/' . $event['id']); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo $this->url('admin/events'); ?>" class="btn btn-success btn-sm">Voir tous</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Publications -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">Publications récentes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Type</th>
                                <th>Auteur</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentItems['publications'] as $pub): ?>
                                <tr>
                                    <td><?php echo $this->escape($pub['titre']); ?></td>
                                    <td><?php echo $this->escape($pub['type']); ?></td>
                                    <td><?php echo $this->escape($pub['auteurPrenom'] . ' ' . $pub['auteurNom']); ?></td>
                                    <td>
                                        <a href="<?php echo $this->url('publications/' . $pub['id']); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo $this->url('admin/publications'); ?>" class="btn btn-warning btn-sm">Voir tous</a>
                </div>
            </div>
        </div>

        <!-- Recent Contact Messages -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">Messages récents</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Sujet</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentItems['contacts'] as $contact): ?>
                                <tr>
                                    <td><?php echo $this->escape($contact['nom']); ?></td>
                                    <td><?php echo $this->escape($contact['sujet']); ?></td>
                                    <td><?php echo $this->formatDate($contact['dateEnvoi']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $contact['status'] === 'Non lu' ? 'bg-danger' : ($contact['status'] === 'Lu' ? 'bg-warning' : 'bg-success'); ?>">
                                            <?php echo $contact['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo $this->url('admin/contacts/' . $contact['id']); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?php echo $this->url('admin/contacts'); ?>" class="btn btn-secondary btn-sm">Voir tous</a>
                </div>
            </div>
        </div>
    </div>
</div>