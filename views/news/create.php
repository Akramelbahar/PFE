<?php
/**
 * Create news view
 */
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Créer une nouvelle actualité</h1>
        <a href="<?= $this->url('news') ?>" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <!-- Flash messages -->
    <?php if (isset($flash['message'])): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <?= $this->escape($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display validation errors if any -->
    <?php if (isset($errors) && is_array($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">Erreurs de validation</h5>
            <ul class="mb-0">
                <?php foreach ($errors as $field => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <li><?= $field ?>: <?= $this->escape($error) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- News Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations de l'actualité</h6>
        </div>
        <div class="card-body">
            <form action="<?= $this->url('news/create') ?>" method="post" enctype="multipart/form-data" id="newsForm">
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="titre" name="titre"
                               value="<?= isset($titre) ? $this->escape($titre) : '' ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                    <textarea class="form-control rich-editor" id="contenu" name="contenu" rows="12" required><?= isset($contenu) ? $this->escape($contenu) : '' ?></textarea>
                    <div class="form-text">
                        Utilisez l'éditeur pour formater votre contenu. Vous pouvez ajouter des titres, des listes, des liens, etc.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label">Image de couverture</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/*">
                    <div class="form-text">
                        Format recommandé: 1200 x 630 pixels, JPG ou PNG, max 2MB.
                    </div>
                </div>



                <?php if (!empty($events)): ?>
                    <div class="mb-4">
                        <label for="evenement_id" class="form-label">Lier à un événement</label>
                        <select class="form-select" id="evenement_id" name="evenement_id">
                            <option value="">Aucun événement</option>
                            <?php foreach ($events as $event): ?>
                                <option value="<?= $event['id'] ?>" <?= isset($evenement_id) && $evenement_id == $event['id'] ? 'selected' : '' ?>>
                                    <?= $this->escape($event['titre']) ?>
                                    (<?= isset($event['eventDate']) ? $this->formatDate($event['eventDate'], 'd/m/Y') : $this->formatDate($event['dateCreation'], 'd/m/Y') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">
                            Associer cette actualité à un événement permet de la faire apparaître sur la page de l'événement correspondant.
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= $this->url('news') ?>" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Publier l'actualité
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for form validation and rich text editor -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form validation
        const form = document.getElementById('newsForm');

        form.addEventListener('submit', function(event) {
            // Validate required fields
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    valid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                event.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });

        // Add event listeners to remove validation styles when fields are corrected
        const fields = form.querySelectorAll('input, select, textarea');
        fields.forEach(function(field) {
            field.addEventListener('input', function() {
                if (field.value.trim()) {
                    field.classList.remove('is-invalid');
                }
            });
        });

        // Initialize rich text editor if available
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '.rich-editor',
                height: 400,
                menubar: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; }'
            });
        }
    });
</script>