<!-- views/ideas/edit.php -->
<div class="idea-edit-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Modifier l'idée de recherche</h1>
        <div>
            <a href="<?php echo $this->url('ideas/' . $idea['id']); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux détails
            </a>

            <!-- Delete Button -->
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    </div>

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
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Modifier les informations</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo $this->url('ideas/edit/' . $idea['id']); ?>" method="post">
                <?php echo CSRF::tokenField(); ?>

                <div class="mb-3">
                    <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                    <input type="text" id="titre" name="titre" class="form-control" required
                           value="<?php echo $this->escape($idea['titre']); ?>">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea id="description" name="description" class="form-control" rows="5" required><?php echo $this->escape($idea['description']); ?></textarea>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette idée de recherche?</p>
                <p><strong><?php echo $this->escape($idea['titre']); ?></strong></p>
                <p>Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="<?php echo $this->url('ideas/delete/' . $idea['id']); ?>" method="post">
                    <?php echo CSRF::tokenField(); ?>
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>