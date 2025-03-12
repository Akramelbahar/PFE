<!-- Add these items to the admin sidebar menu -->
<li class="nav-item">
    <a class="nav-link <?php echo $this->activeClass('admin/bureau', 'active'); ?>" href="<?php echo $this->url('admin/bureau'); ?>">
        <i class="fas fa-user-tie"></i> Bureau Exécutif
    </a>
</li>

<li class="nav-item">
    <a class="nav-link <?php echo $this->activeClass('admin/ideas', 'active'); ?>" href="<?php echo $this->url('admin/ideas'); ?>">
        <i class="fas fa-lightbulb"></i> Idées de Recherche
        <?php if (isset($pendingIdeasCount) && $pendingIdeasCount > 0): ?>
            <span class="badge bg-warning rounded-pill ms-1"><?php echo $pendingIdeasCount; ?></span>
        <?php endif; ?>
    </a>
</li>

<li class="nav-item">
    <a class="nav-link <?php echo $this->activeClass('admin/publications/pending', 'active'); ?>" href="<?php echo $this->url('admin/publications/pending'); ?>">
        <i class="fas fa-clipboard-check"></i> Publications à approuver
        <?php if (isset($pendingPublicationsCount) && $pendingPublicationsCount > 0): ?>
            <span class="badge bg-info rounded-pill ms-1"><?php echo $pendingPublicationsCount; ?></span>
        <?php endif; ?>
    </a>
</li>