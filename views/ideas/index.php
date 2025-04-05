<!-- views/ideas/index.php -->
<div class="ideas-index-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Idées de Recherche</h1>
        <a href="<?php echo $this->url('ideas/create'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Proposer une nouvelle idée
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" method="get" class="row g-3">
                <div class="col-md-4">
                    <label for="domainFilter" class="form-label">Domaine</label>
                    <select id="domainFilter" name="domain" class="form-select">
                        <option value="">Tous les domaines</option>
                        <?php foreach ($domains as $domain): ?>
                            <option value="<?php echo $this->escape($domain); ?>" <?php echo isset($_GET['domain']) && $_GET['domain'] === $domain ? 'selected' : ''; ?>>
                                <?php echo $this->escape($domain); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="statusFilter" class="form-label">Statut</label>
                    <select id="statusFilter" name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en attente" <?php echo isset($_GET['status']) && $_GET['status'] === 'en attente' ? 'selected' : ''; ?>>En attente</option>
                        <option value="approuvée" <?php echo isset($_GET['status']) && $_GET['status'] === 'approuvée' ? 'selected' : ''; ?>>Approuvée</option>
                        <option value="refusé" <?php echo isset($_GET['status']) && $_GET['status'] === 'refusé' ? 'selected' : ''; ?>>Refusée</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                        <a href="<?php echo $this->url('ideas'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-sync-alt"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Ideas List -->
    <?php if (empty($ideas)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Aucune idée de recherche trouvée.
            <?php if (isset($_GET['domain']) || isset($_GET['status'])): ?>
                <p class="mb-0">Essayez de changer les critères de filtre.</p>
            <?php else: ?>
                <p class="mb-0">Proposez une nouvelle idée en cliquant sur le bouton ci-dessus.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($ideas as $idea): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <a href="<?php echo $this->url('ideas/' . $idea['id']); ?>" class="text-decoration-none">
                                    <?php echo $this->escape($idea['titre']); ?>
                                </a>
                            </h5>
                            <span class="badge <?php
                            switch($idea['status']) {
                                case 'en attente':
                                    echo 'bg-secondary';
                                    break;
                                case 'approuvée':
                                    echo 'bg-success';
                                    break;
                                case 'refusé':
                                    echo 'bg-danger';
                                    break;
                                default:
                                    echo 'bg-primary';
                            }
                            ?>"><?php echo $this->escape($idea['status']); ?></span>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                <?php echo $this->truncate($this->escape($idea['description']), 150); ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">
                                Proposée par:
                                <?php if (isset($idea['proposerNom']) && isset($idea['proposerPrenom'])): ?>
                                    <strong><?php echo $this->escape($idea['proposerPrenom'] . ' ' . $idea['proposerNom']); ?></strong>
                                <?php else: ?>
                                    <strong>Utilisateur inconnu</strong>
                                <?php endif; ?>
                                <br>
                                Le <?php echo $this->formatDate($idea['dateProposition']); ?>
                            </small>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo $this->url('ideas/' . $idea['id']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Voir les détails
                            </a>

                            <?php if ($auth->isLoggedIn() && ($idea['proposePar'] == $auth->getUser()['id'] || $auth->hasRole(['admin', 'membreBureauExecutif']))): ?>
                                <a href="<?php echo $this->url('ideas/edit/' . $idea['id']); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form when filters change
        document.getElementById('domainFilter').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('statusFilter').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
</script>