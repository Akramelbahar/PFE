<!-- views/errors/not_found.php -->
<div class="error-page text-center">
    <div class="mb-4">
        <i class="fas fa-search fa-5x text-muted"></i>
    </div>
    <h1 class="display-1 text-danger">404</h1>
    <h2 class="mb-4">Page non trouvée</h2>
    <p class="lead mb-5">La page que vous recherchez n'existe pas ou a été déplacée.</p>
    <div class="d-flex justify-content-center">
        <a href="<?php echo $this->url(''); ?>" class="btn btn-primary me-3">
            <i class="fas fa-home me-2"></i> Retour à l'accueil
        </a>
        <a href="javascript:history.back();" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Page précédente
        </a>
    </div>
</div>