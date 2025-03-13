<!-- views/home/about.php -->
<div class="about-page">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="display-4 mb-4 text-center">À propos de notre Association</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Notre Mission</h4>
                    </div>
                    <div class="card-body">
                        <p>L'Association Recherche et Innovation a pour mission de promouvoir l'excellence académique,
                            de stimuler la recherche scientifique et de favoriser l'innovation technologique au sein de
                            l'École Supérieure de Technologie de Safi, université Cadi Ayyad.</p>
                        <p>Nous croyons en la puissance de la recherche pour résoudre les défis complexes et contribuer
                            au développement technologique et sociétal.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Nos Valeurs</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-primary me-2"></i> Excellence académique
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-primary me-2"></i> Innovation continue
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-primary me-2"></i> Collaboration interdisciplinaire
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-primary me-2"></i> Impact sociétal
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-primary me-2"></i> Éthique et intégrité scientifique
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Nos Domaines de Recherche</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-laptop text-primary fa-2x me-3"></i>
                                    <h5 class="mb-0">Informatique</h5>
                                </div>
                                <p class="text-muted">Intelligence artificielle, systèmes distribués, cybersécurité</p>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-building text-primary fa-2x me-3"></i>
                                    <h5 class="mb-0">Génie Civil</h5>
                                </div>
                                <p class="text-muted">Matériaux de construction, structures durables, infrastructure</p>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-bolt text-primary fa-2x me-3"></i>
                                    <h5 class="mb-0">Génie Électrique</h5>
                                </div>
                                <p class="text-muted">Énergies renouvelables, électronique, automatisation</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card bg-light mb-4">
                    <div class="card-body text-center">
                        <h3 class="card-title mb-3">Rejoignez-nous dans notre quête d'innovation</h3>
                        <p class="card-text mb-4">Que vous soyez chercheur, étudiant ou partenaire, votre contribution peut faire la différence.</p>

                        <?php if (!$auth->isLoggedIn()): ?>
                            <div>
                                <a href="<?php echo $this->url('register'); ?>" class="btn btn-primary me-2">
                                    <i class="fas fa-user-plus"></i> S'inscrire
                                </a>
                                <a href="<?php echo $this->url('contact'); ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope"></i> Nous contacter
                                </a>
                            </div>
                        <?php else: ?>
                            <div>
                                <a href="<?php echo $this->url('ideas/create'); ?>" class="btn btn-primary me-2">
                                    <i class="fas fa-lightbulb"></i> Proposer une idée
                                </a>
                                <a href="<?php echo $this->url('projects'); ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-project-diagram"></i> Nos projets
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>