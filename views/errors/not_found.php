<!-- views/errors/not_found.php -->
<div class="error-page not-found">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <span class="display-1 text-muted">404</span>
                            <i class="fas fa-map-signs fa-3x text-warning ms-3"></i>
                        </div>

                        <h1 class="h3 mb-4">Page non trouvée</h1>

                        <p class="text-muted mb-4">
                            La page que vous recherchez n'existe pas ou a été déplacée.
                            Nous nous excusons pour ce désagrément.
                        </p>

                        <div class="mb-4">
                            <a href="<?php echo $this->url(''); ?>" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i> Retour à l'accueil
                            </a>
                        </div>

                        <hr class="my-4">

                        <div class="suggestions">
                            <h5>Vous pourriez être intéressé par</h5>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <a href="<?php echo $this->url('projects'); ?>" class="suggestion-link">
                                        <i class="fas fa-project-diagram text-danger"></i> Projets
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo $this->url('publications'); ?>" class="suggestion-link">
                                        <i class="fas fa-book text-warning"></i> Publications
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="<?php echo $this->url('events'); ?>" class="suggestion-link">
                                        <i class="fas fa-calendar-alt text-success"></i> Événements
                                    </a>
                                </div>
                            </div>
                        </div>
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
    .error-page .suggestion-link {
        display: block;
        padding: 15px;
        text-align: center;
        text-decoration: none;
        color: #333;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .error-page .suggestion-link:hover {
        background-color: #f5f5f5;
        transform: translateY(-3px);
    }
</style>