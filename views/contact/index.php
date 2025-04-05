<!-- views/home/contact.php -->

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h1 class="mb-3">Contactez-nous</h1>
                <p class="lead">Nous sommes à votre écoute pour toute question, suggestion ou demande de collaboration</p>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row">
        <!-- Contact Form -->
        <div class="col-lg-8 mb-4 mb-lg-0">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h3 class="card-title mb-4">Envoyez-nous un message</h3>

                    <?php if (isset($success) && $success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i> Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $field => $fieldErrors): ?>
                                    <?php foreach ($fieldErrors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $this->url('contact'); ?>" method="post">
                        <?php echo CSRF::tokenField(); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo isset($contact['nom']) ? $this->escape($contact['nom']) : ($auth->isLoggedIn() ? $this->escape($auth->getUser()['nom']) : ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo isset($contact['prenom']) ? $this->escape($contact['prenom']) : ($auth->isLoggedIn() ? $this->escape($auth->getUser()['prenom']) : ''); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($contact['email']) ? $this->escape($contact['email']) : ($auth->isLoggedIn() ? $this->escape($auth->getUser()['email']) : ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="sujet" class="form-label">Sujet <span class="text-danger">*</span></label>
                            <select class="form-select" id="sujet" name="sujet" required>
                                <option value="" selected disabled>Choisissez un sujet</option>
                                <option value="Demande d'informations" <?php echo isset($contact['sujet']) && $contact['sujet'] === 'Demande d\'informations' ? 'selected' : ''; ?>>Demande d'informations</option>
                                <option value="Adhésion" <?php echo isset($contact['sujet']) && $contact['sujet'] === 'Adhésion' ? 'selected' : ''; ?>>Adhésion</option>
                                <option value="Proposition de collaboration" <?php echo isset($contact['sujet']) && $contact['sujet'] === 'Proposition de collaboration' ? 'selected' : ''; ?>>Proposition de collaboration</option>
                                <option value="Événement" <?php echo isset($contact['sujet']) && $contact['sujet'] === 'Événement' ? 'selected' : ''; ?>>Événement</option>
                                <option value="Autre" <?php echo isset($contact['sujet']) && $contact['sujet'] === 'Autre' ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="6" required><?php echo isset($contact['message']) ? $this->escape($contact['message']) : ''; ?></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="consentement" name="consentement" required <?php echo isset($contact['consentement']) && $contact['consentement'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="consentement">
                                J'accepte que mes données soient utilisées pour traiter ma demande <span class="text-danger">*</span>
                            </label>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-4">Coordonnées</h3>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-map-marker-alt fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Adresse</h5>
                                    <p class="text-muted mb-0">École Supérieure de Technologie<br>Route Dar Si Aïssa, Safi<br>Maroc</p>
                                </div>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-envelope fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Email</h5>
                                    <p class="text-muted mb-0">contact@association-ri.ma</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-phone fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Téléphone</h5>
                                    <p class="text-muted mb-0">+212 5XX XX XX XX</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-4">Horaires</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Lundi - Vendredi</span>
                                <span>9h - 17h</span>
                            </div>
                        </li>
                        <li class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Samedi</span>
                                <span>9h - 12h</span>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex justify-content-between">
                                <span>Dimanche</span>
                                <span>Fermé</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Réseaux sociaux</h3>
                    <div class="d-flex justify-content-around">
                        <a href="#" class="btn btn-outline-primary btn-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn btn-outline-info btn-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-icon">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="mt-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3354.7686606883193!2d-9.239482084768191!3d32.78220598096936!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xdac212049843597%3A0x42f3ddc4ce56a8ab!2s%C3%89cole%20Sup%C3%A9rieure%20de%20Technologie%20de%20Safi!5e0!3m2!1sfr!2sma!4v1677601232619!5m2!1sfr!2sma" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>