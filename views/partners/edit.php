<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">
                        <i class="fas fa-edit"></i> Modifier le Partenaire
                    </h2>
                </div>
                <div class="card-body">
                    <form action="<?php echo $this->url('partners/edit/' . $partner['id']); ?>" method="post">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom du Partenaire <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="nom"
                                   id="nom"
                                   class="form-control <?php echo isset($errors['nom']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo $this->escape($partner['nom']); ?>"
                                   required>
                            <?php if (isset($errors['nom'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $this->escape($errors['nom'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">URL du Logo</label>
                            <input type="url"
                                   name="logo"
                                   id="logo"
                                   class="form-control <?php echo isset($errors['logo']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo $this->escape($partner['logo'] ?? ''); ?>">
                            <?php if (isset($errors['logo'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $this->escape($errors['logo'][0]); ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                Lien direct vers l'image du logo (optionnel)
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="siteweb" class="form-label">Site Web <span class="text-danger">*</span></label>
                            <input type="url"
                                   name="siteweb"
                                   id="siteweb"
                                   class="form-control <?php echo isset($errors['siteweb']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo $this->escape($partner['siteweb']); ?>"
                                   required>
                            <?php if (isset($errors['siteweb'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $this->escape($errors['siteweb'][0]); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact</label>
                            <input type="text"
                                   name="contact"
                                   id="contact"
                                   class="form-control <?php echo isset($errors['contact']) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo $this->escape($partner['contact'] ?? ''); ?>">
                            <?php if (isset($errors['contact'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $this->escape($errors['contact'][0]); ?>
                                </div>
                            <?php endif; ?>
                            <small class="form-text text-muted">
                                Information de contact (téléphone, email, etc.)
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo $this->url('partners'); ?>" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>