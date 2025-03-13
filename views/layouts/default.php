<!-- views/layouts/default.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->escape($pageTitle); ?> | <?php echo $config->get('app.name'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $this->url(''); ?>">
            <img src="assets/images/logo.png" alt="Logo" height="40" class="d-inline-block align-top">
            <?php echo $config->get('app.name'); ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $this->activeClass('', 'active'); ?>" href="<?php echo $this->url(''); ?>">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                </li>

                <?php if ($auth->isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('publications', 'active'); ?>" href="<?php echo $this->url('publications'); ?>">
                            <i class="fas fa-book"></i> Publications
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('events', 'active'); ?>" href="<?php echo $this->url('events'); ?>">
                            <i class="fas fa-calendar-alt"></i> Événements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('projects', 'active'); ?>" href="<?php echo $this->url('projects'); ?>">
                            <i class="fas fa-project-diagram"></i> Projets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('ideas', 'active'); ?>" href="<?php echo $this->url('ideas'); ?>">
                            <i class="fas fa-lightbulb"></i> Idées
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?php echo $this->activeClass('about', 'active'); ?>" href="<?php echo $this->url('about'); ?>">
                        <i class="fas fa-info-circle"></i> À propos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $this->activeClass('contact', 'active'); ?>" href="<?php echo $this->url('contact'); ?>">
                        <i class="fas fa-envelope"></i> Contact
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <?php if ($auth->isLoggedIn()): ?>
                    <?php if ($auth->hasRole('admin')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $this->activeClass('admin', 'active'); ?>" href="<?php echo $this->url('admin'); ?>">
                                <i class="fas fa-cog"></i> Administration
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                            <?php echo $auth->getUser()['prenom'] . ' ' . $auth->getUser()['nom']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo $this->url('profile'); ?>">
                                    <i class="fas fa-user-circle"></i> Mon profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?php echo $this->url('logout'); ?>">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('login', 'active'); ?>" href="<?php echo $this->url('login'); ?>">
                            <i class="fas fa-sign-in-alt"></i> Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $this->activeClass('register', 'active'); ?>" href="<?php echo $this->url('register'); ?>">
                            <i class="fas fa-user-plus"></i> Inscription
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="ms-auto me-3">
            <?php include 'views/partials/search-box.php'; ?>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php if (isset($flash) && $flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Main Content -->
<main class="container py-4">
    <?php echo $content; ?>
</main>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Association Recherche et Innovation</h5>
                <p>École Supérieure de Technologie - Safi<br>
                    Université Cadi Ayyad</p>
            </div>
            <div class="col-md-4">
                <h5>Liens rapides</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo $this->url(''); ?>" class="text-white">Accueil</a></li>
                    <li><a href="<?php echo $this->url('about'); ?>" class="text-white">À propos</a></li>
                    <li><a href="<?php echo $this->url('contact'); ?>" class="text-white">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Suivez-nous</h5>
                <div class="social-links">
                    <a href="#" class="text-white me-2"><i class="fab fa-facebook-f fa-lg"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-linkedin-in fa-lg"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $config->get('app.name'); ?>. Tous droits réservés.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>