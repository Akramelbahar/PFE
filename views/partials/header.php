<!-- views/partials/header.php -->
<?php
// Ensure $config is available, using Config singleton if not passed
if (!isset($config) || $config === null) {
    $config = Config::getInstance();
}

// Ensure $auth is available
if (!isset($auth) || $auth === null) {
    $auth = Auth::getInstance(); // Assuming you have a similar singleton pattern for Auth
}

// Default app name fallback
$appName = $config ? $config->get('app.name', 'Association Recherche et Innovation') : 'Association Recherche et Innovation';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $this->url(''); ?>">
            <img src="<?php echo $this->url('public/images/logo.jpg'); ?>" alt="<?php echo htmlspecialchars($appName); ?> Logo" height="40" class="d-inline-block align-top me-2">
            <?php echo htmlspecialchars($appName); ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo $this->activeClass('', 'active'); ?>" href="<?php echo $this->url(''); ?>">
                        <i class="fas fa-home me-1"></i> Accueil
                    </a>
                </li>

                <?php if ($auth && $auth->isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('publications', 'active'); ?>" href="<?php echo $this->url('publications'); ?>">
                            <i class="fas fa-book me-1"></i> Publications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('events', 'active'); ?>" href="<?php echo $this->url('events'); ?>">
                            <i class="fas fa-calendar-alt me-1"></i> Événements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('projects', 'active'); ?>" href="<?php echo $this->url('projects'); ?>">
                            <i class="fas fa-project-diagram me-1"></i> Projets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('ideas', 'active'); ?>" href="<?php echo $this->url('ideas'); ?>">
                            <i class="fas fa-lightbulb me-1"></i> Idées
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?php echo $this->activeClass('about', 'active'); ?>" href="<?php echo $this->url('about'); ?>">
                        <i class="fas fa-info-circle me-1"></i> À propos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $this->activeClass('contact', 'active'); ?>" href="<?php echo $this->url('contact'); ?>">
                        <i class="fas fa-envelope me-1"></i> Contact
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <!-- Search Box -->
                <form action="<?php echo $this->url('search'); ?>" method="get" class="me-3">
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control" placeholder="Rechercher..." aria-label="Rechercher" required>
                        <button class="btn btn-light" type="submit" aria-label="Rechercher">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <ul class="navbar-nav">
                    <?php if ($auth && $auth->isLoggedIn()): ?>
                        <?php
                        $user = $auth->getUser();
                        $userFullName = $user ? (htmlspecialchars($user['prenom'] . ' ' . $user['nom'])) : 'Utilisateur';
                        ?>
                        <?php if ($auth->hasRole('admin')): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $this->activeClass('admin', 'active'); ?>" href="<?php echo $this->url('admin'); ?>">
                                    <i class="fas fa-cog me-1"></i> Administration
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>
                                <?php echo $userFullName; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?php echo $this->url('profile'); ?>">
                                        <i class="fas fa-user-circle me-2"></i> Mon profil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $this->url('logout'); ?>">
                                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $this->activeClass('login', 'active'); ?>" href="<?php echo $this->url('login'); ?>">
                                <i class="fas fa-sign-in-alt me-1"></i> Connexion
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $this->activeClass('register', 'active'); ?>" href="<?php echo $this->url('register'); ?>">
                                <i class="fas fa-user-plus me-1"></i> Inscription
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</nav>

<?php if (isset($showHero) && $showHero): ?>
    <!-- Hero Section -->
    <div class="jumbotron bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4"><?php echo htmlspecialchars($appName); ?></h1>
                    <p class="lead">Découvrez nos projets de recherche, publications et événements scientifiques.</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="<?php echo $this->url('projects'); ?>" class="btn btn-primary me-md-2">Nos projets</a>
                        <a href="<?php echo $this->url('publications'); ?>" class="btn btn-outline-primary">Publications</a>
                    </div>
                </div>
                <div class="col-md-6 text-center">
                    <img src="<?php echo $this->url('public/images/hero.svg'); ?>" alt="Illustration" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>