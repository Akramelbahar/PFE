<!-- views/errors/forbidden.php -->
<div class="error-page forbidden">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <span class="display-1 text-muted">403</span>
                            <i class="fas fa-lock fa-3x text-danger ms-3"></i>
                        </div>

                        <h1 class="h3 mb-4">Accès interdit</h1>

                        <p class="text-muted mb-4">
                            Vous n'avez pas les droits nécessaires pour accéder à cette page.
                            Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administrateur.
                        </p>

                        <div class="mb-5">
                            <a href="<?php echo $this->url(''); ?>" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i> Retour à l'accueil
                            </a>

                            <?php if (!$auth->isLoggedIn()): ?>
                                <a href="<?php echo $this->url('login'); ?>" class="btn btn-outline-primary ms-2">
                                    <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="alert alert-info d-inline-block">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div class="text-start">
                                    <strong>Information</strong>
                                    <p class="mb-0">
                                        Cette restriction peut être due à votre niveau d'accès.
                                        <?php if ($auth->isLoggedIn()): ?>
                                            Votre rôle actuel :
                                            <?php foreach ($auth->getRoles() as $role): ?>
                                                <span class="badge bg-secondary"><?php echo $role; ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            Vous n'êtes pas connecté.
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">Que faire ?</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-transparent">
                                <i class="fas fa-arrow-left text-primary me-2"></i> Revenir à la page précédente
                            </li>
                            <li class="list-group-item bg-transparent">
                                <i class="fas fa-home text-primary me-2"></i> Retourner à la page d'accueil
                            </li>
                            <?php if ($auth->isLoggedIn()): ?>
                                <li class="list-group-item bg-transparent">
                                    <i class="fas fa-user text-primary me-2"></i> Vérifier votre profil et vos droits d'accès
                                </li>
                            <?php else: ?>
                                <li class="list-group-item bg-transparent">
                                    <i class="fas fa-sign-in-alt text-primary me-2"></i> Vous connecter à un compte avec les droits nécessaires
                                </li>
                            <?php endif; ?>
                            <li class="list-group-item bg-transparent">
                                <i class="fas fa-envelope text-primary me-2"></i> Contacter l'équipe support si le problème persiste
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="<?php echo $this->url('contact'); ?>" class="text-decoration-none">
                        <i class="fas fa-question-circle me-1"></i> Besoin d'aide ? Contactez-nous
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .error-page .fas {
        vertical-align: middle;
    }

    .error-page .list-group-item {
        border-left: none;
        border-right: none;
        border-radius: 0;
        transition: background-color 0.3s ease;
    }

    .error-page .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .error-page .list-group-item:first-child {
        border-top: none;
    }
</style>