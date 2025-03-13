<!-- views/partials/footer.php -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><?php echo $config->get('app.name'); ?></h5>
                <p>École Supérieure de Technologie - Safi<br>
                    Université Cadi Ayyad</p>
            </div>
            <div class="col-md-4">
                <h5>Liens rapides</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo $this->url(''); ?>" class="text-white">Accueil</a></li>
                    <li><a href="<?php echo $this->url('about'); ?>" class="text-white">À propos</a></li>
                    <li><a href="<?php echo $this->url('contact'); ?>" class="text-white">Contact</a></li>
                    <?php if ($auth->isLoggedIn()): ?>
                        <li><a href="<?php echo $this->url('publications'); ?>" class="text-white">Publications</a></li>
                        <li><a href="<?php echo $this->url('events'); ?>" class="text-white">Événements</a></li>
                    <?php endif; ?>
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