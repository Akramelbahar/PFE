<!-- views/partials/admin_sidebar.php -->
<nav id="admin-sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Administration</span>
        </h6>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $this->isCurrentPath('admin') ? 'active' : ''; ?>" href="<?php echo $this->url('admin'); ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'admin/users') !== false ? 'active' : ''; ?>" href="<?php echo $this->url('admin/users'); ?>">
                    <i class="fas fa-users me-2"></i>
                    Utilisateurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'admin/bureau') !== false ? 'active' : ''; ?>" href="<?php echo $this->url('admin/bureau'); ?>">
                    <i class="fas fa-user-tie me-2"></i>
                    Bureau Exécutif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'admin/publications') !== false ? 'active' : ''; ?>" href="<?php echo $this->url('admin/publications'); ?>">
                    <i class="fas fa-book me-2"></i>
                    Publications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'admin/events') !== false ? 'active' : ''; ?>" href="<?php echo $this->url('admin/events'); ?>">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Événements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'admin/projects') !== false ? 'active' : ''; ?>" href="<?php echo $this->url('admin/projects'); ?>">
                    <i class="fas fa-project-diagram me-2"></i>
                    Projets
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'admin/news') !== false ? 'active' : ''; ?>" href="<?php echo $this->url('admin/news'); ?>">
                    <i class="fas fa-newspaper me-2"></i>
                    Actualités
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'admin/contacts') !== false ? 'active' : ''; ?>" href="<?php echo $this->url('admin/contacts'); ?>">
                    <i class="fas fa-envelope me-2"></i>
                    Messages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'admin/settings') !== false ? 'active' : ''; ?>" href="<?php echo $this->url('admin/settings'); ?>">
                    <i class="fas fa-cog me-2"></i>
                    Paramètres
                </a>
            </li>
        </ul>
    </div>
</nav>