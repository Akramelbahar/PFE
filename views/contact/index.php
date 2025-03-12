<!-- views/contact/index.php -->
<div class="contact-page">
    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4">Contactez-nous</h1>
            <p class="lead mb-4">
                Pour toute question concernant notre association, nos projets de recherche ou pour proposer une collaboration, n'hésitez pas à nous contacter via le formulaire ci-dessous.
            </p>

            <?php if (isset($errors) && $errors): ?>
                <div class="alert alert-danger">
                    <h5 class="alert-heading">Erreurs de validation</h5>
                    <ul class="mb-0">
                        <?php foreach ($errors as $field => $fieldErrors): ?>
                            <?php foreach ($fieldErrors as $error): ?>
                                <li><?php echo ucfirst($field); ?>: <?php echo $error; ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="<?php echo $this->url('contact'); ?>" method="post">
                        <?php echo CSRF::tokenField(); ?>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required
                                       value="<?php echo isset($nom) ? $this->escape($nom) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo isset($email) ? $this->escape($email) : ''; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone"
                                   value="<?php echo isset($telephone) ? $this->escape($telephone) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="sujet" class="form-label">Sujet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sujet" name="sujet" required
                                   value="<?php echo isset($sujet) ? $this->escape($sujet) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="6" required><?php echo isset($message) ? $this->escape($message) : ''; ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Envoyer le message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Coordonnées</h5>
                </div>
                <div class="card-body">
                    <p><strong><i class="fas fa-map-marker-alt me-2"></i> Adresse:</strong><br>
                        Association Recherche et Innovation<br>
                        École Supérieure de Technologie<br>
                        Route Dar Si Aïssa, B.P 89<br>
                        Safi, Maroc</p>

                    <p><strong><i class="fas fa-phone me-2"></i> Téléphone:</strong><br>
                        +212 524 62 50 53</p>

                    <p><strong><i class="fas fa-envelope me-2"></i> Email:</strong><br>
                        contact@rechercheinnovation-ests.com</p>

                    <p><strong><i class="fas fa-clock me-2"></i> Horaires d'ouverture:</strong><br>
                        Lundi - Vendredi: 8h30 - 16h30</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Localisation</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Embedded Google Map -->
                    <div class="ratio ratio-4x3">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3381.5006909368944!2d-9.238024224442655!3d32.30872597323869!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xdac211719897669%3A0x6f59fa5bb517f58a!2sEST%20Safi!5e0!3m2!1sfr!2sma!4v1710282752367!5m2!1sfr!2sma"
                                style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>