<!-- views/auth/reset_password.php -->
<div class="reset-password-page">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">Réinitialiser le mot de passe</h3>
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

                    <form action="<?php echo $this->url('reset-password'); ?>" method="post">
                        <?php echo CSRF::tokenField(); ?>
                        <input type="hidden" name="token" value="<?php echo $this->escape($token); ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" id="password" name="password"
                                       class="form-control"
                                       placeholder="Entrez votre nouveau mot de passe"
                                       required
                                       minlength="6">
                            </div>
                            <div class="form-text">Le mot de passe doit comporter au moins 6 caractères</div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" id="password_confirm" name="password_confirm"
                                       class="form-control"
                                       placeholder="Confirmez votre nouveau mot de passe"
                                       required
                                       minlength="6">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Réinitialiser
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
        </div>
    </div>
</div>