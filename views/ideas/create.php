<!-- views/ideas/create.php -->
<div class="idea-create-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Proposer une idée de recherche</h1>
        <a href="<?php echo $this->url('ideas'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux idées
        </a>
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
            <h5 class="mb-0">Informations de l'idée</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo $this->url('ideas/create'); ?>" method="post">
                <?php echo CSRF::tokenField(); ?>

                <div class="mb-3">
                    <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                    <input type="text" id="titre" name="titre" class="form-control" required
                           value="<?php echo isset($idea['titre']) ? $this->escape($idea['titre']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea id="description" name="description" class="form-control" rows="5" required><?php echo isset($idea['description']) ? $this->escape($idea['description']) : ''; ?></textarea>
                    <div class="form-text">Décrivez votre idée de recherche en détail.</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">Soumettre l'idée</button>
                </div>
            </form>
        </div>
    </div>
</div>