<!-- views/partials/sidebar.php -->
<div class="sidebar bg-dark text-white">
    <div class="sidebar-header py-3 px-3 text-center">
        <h4>
            <a href="<?php echo $this->url('admin'); ?>" class="text-white text-decoration-none">
                <img src="<?php echo $this->url('public/images/logo.png'); ?>" alt="Logo" height="30" class="d-inline-block align-top">
                Admin
            </a>
        </h4>
    </div>

    <div class="px-3 py-2">
        <div class="user-info d-flex align-items-center mb-3">
            <div class="avatar me-2">
                <?php if (isset($auth) && $auth->isLoggedIn() && isset($auth->getUser()['profilePicture'])): ?>
                    <img src="<?php echo $this->url('public/uploads/profile_pictures/' . $auth->getUser()['profilePicture']); ?>" alt="Profile" class="rounded-circle" width="40" height="40">
                <?php else: ?>
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="user-details small">
                <div class="fw-bold">
                    <?php echo isset($auth) && $auth->isLoggedIn() ? $auth->getUser()['prenom'] . ' ' . $auth->getUser()['nom'] : 'Utilisateur'; ?>
                </div>
                <div class="text-muted">Administrator</div>
            </div>
        </div>
    </div>

    <hr class="mx-3 my-1">

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $this->activeClass('admin', 'active bg-primary'); ?>" href="<?php echo $this->url('admin'); ?>">
                <i class="fas fa-tachometer-alt fa-fw me-2"></i> Tableau de bord
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white <?php echo $this->activeClass('admin/users', 'active bg-primary'); ?>" href="<?php echo $this->url('admin/users'); ?>">
                <i class="fas fa-users fa-fw me-2"></i> Utilisateurs
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white <?php echo $this->activeClass('admin/publications', 'active bg-primary'); ?>" href="<?php echo $this->url('admin/publications'); ?>">
                <i class="fas fa-book fa-fw me-2"></i> Publications
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white <?php echo $this->activeClass('admin/events', 'active bg-primary'); ?>" href="<?php echo $this->url('admin/events'); ?>">
                <i class="fas fa-calendar-alt fa-fw me-2"></i> Événements
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white <?php echo $this->activeClass('admin/projects', 'active bg-primary'); ?>" href="<?php echo $this->url('admin/projects'); ?>">
                <i class="fas fa-project-diagram fa-fw me-2"></i> Projets
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white <?php echo $this->activeClass('admin/news', 'active bg-primary'); ?>" href="<?php echo $this->url('admin/news'); ?>">
                <i class="fas fa-newspaper fa-fw me-2"></i> Actualités
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white <?php echo $this->activeClass('admin/contacts', 'active bg-primary'); ?>" href="<?php echo $this->url('admin/contacts'); ?>">
                <i class="fas fa-envelope fa-fw me-2"></i> Messages
            </a>
        </li>

        <li class="nav-item mt-3">
            <a class="nav-link text-white <?php echo $this->activeClass('admin/settings', 'active bg-primary'); ?>" href="<?php echo $this->url('admin/settings'); ?>">
                <i class="fas fa-cog fa-fw me-2"></i> Paramètres
            </a>
        </li>
    </ul>

    <hr class="mx-3 my-3">

    <div class="px-3 mb-3">
        <a href="<?php echo $this->url('logout'); ?>" class="btn btn-outline-light btn-sm w-100">
            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
        </a>
    </div>
</div>