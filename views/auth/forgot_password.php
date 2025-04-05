<!-- views/auth/forgot_password.php -->
<div class="forgot-password-page">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">Mot de passe oublié</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($errorMessage) && $errorMessage): ?>
                        <div class="alert alert-danger">
                            <?php echo $this->escape($errorMessage); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors) && $errors): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $field => $fieldErrors): ?>
                                    <?php foreach ($fieldErrors as $error): ?>
                                        <li><?php echo $this->escape($error); ?></li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $this->url('forgot-password'); ?>" method="post">
                        <?php echo CSRF::tokenField(); ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" id="email" name="email"
                                       class="form-control"
                                       placeholder="Entrez votre adresse email"
                                       value="<?php echo isset($email) ? $this->escape($email) : ''; ?>"
                                       required>
                            </div>
                            <div class="form-text">Nous vous enverrons un lien de réinitialisation de mot de passe</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Réinitialiser le mot de passe
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer text-center">
                    <p class="mb-0">
                        <a href="<?php echo $this->url('login'); ?>" class="text-muted">
                            <i class="fas fa-arrow-left"></i> Retour à la connexion
                        </a>
                    </p>
                </div>
            </div>

            <div class="text-center mt-3">
                <p>Pas de compte ? <a href="<?php echo $this->url('register'); ?>">Inscrivez-vous</a></p>
            </div>
        </div>
    </div>
</div>