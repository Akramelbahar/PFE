</div>
</div>
</div>
</div>
</div>
</section>

<!-- Join Us -->
<section class="mb-5">
    <div class="card border-0 shadow">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-6 bg-primary text-white p-4 p-md-5">
                    <h3>Rejoignez notre association</h3>
                    <p class="lead">
                        Vous êtes chercheur, enseignant ou étudiant et vous souhaitez contribuer à nos activités ?
                    </p>
                    <p>
                        Rejoignez notre association pour participer à des projets de recherche innovants, assister à des événements scientifiques et collaborer avec d'autres chercheurs passionnés.
                    </p>
                    <?php if (!$auth->isLoggedIn()): ?>
                        <a href="<?php echo $this->url('register'); ?>" class="btn btn-light mt-3">
                            <i class="fas fa-user-plus me-2"></i> Devenir membre
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $this->url('contact'); ?>" class="btn btn-light mt-3">
                            <i class="fas fa-envelope me-2"></i> Nous contacter
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="p-4 p-md-5">
                        <h3>Avantages de l'adhésion</h3>
                        <ul class="list-unstyled mt-4">
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3 text-primary">
                                        <i class="fas fa-check-circle fa-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Accès aux ressources</h5>
                                        <p class="text-muted mb-0">Accédez à notre base de données de publications et nos ressources de recherche</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="me-3 text-primary">
                                        <i class="fas fa-check-circle fa-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Collaboration</h5>
                                        <p class="text-muted mb-0">Travaillez avec d'autres chercheurs sur des projets innovants</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="me-3 text-primary">
                                        <i class="fas fa-check-circle fa-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Opportunités de financement</h5>
                                        <p class="text-muted mb-0">Bénéficiez d'un accès privilégié aux opportunités de financement</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>