<?php
/**
 * Edit news view
 */
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">Modifier l'actualité</h1>
        <div>
            <a href="<?= $this->url('news/' . $news['id']) ?>" class="btn btn-info me-2">
                <i class="fa fa-eye"></i> Voir l'actualité
            </a>
            <a href="<?= $this->url('news') ?>" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
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
            <h6 class="m-0 font-weight-bold text-primary">Modifier l'actualité</h6>
        </div>
        <div class="card-body">
            <form action="<?= $this->url('news/edit/' . $news['id']) ?>" method="post" enctype="multipart/form-data" id="newsForm">
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="titre" name="titre"
                               value="<?= $this->escape($news['titre']) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                    <textarea class="form-control rich-editor" id="contenu" name="contenu" rows="12" required><?= $this->escape($news['contenu']) ?></textarea>
                    <div class="form-text">
                        Utilisez l'éditeur pour formater votre contenu. Vous pouvez ajouter des titres, des listes, des liens, etc.
                    </div>
                </div>

                <?php if (!empty($news['mediaUrl'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Image actuelle</label>
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <img src="<?= $this->url($news['mediaUrl']) ?>" alt="Image actuelle" class="img-fluid img-thumbnail">
                            </div>
                            <div class="col-md-9">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                    <label class="form-check-label" for="remove_image">
                                        Supprimer l'image actuelle
                                    </label>
                                    <div class="form-text">Cochez cette case si vous souhaitez supprimer l'image sans la remplacer.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="image" class="form-label">
                        <?= !empty($news['mediaUrl']) ? 'Remplacer l\'image' : 'Ajouter une image' ?>
                    </label>
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
                                <option value="<?= $event['id'] ?>" <?= isset($news['evenementId']) && $news['evenementId'] == $event['id'] ? 'selected' : '' ?>>
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
                    <div>
                        <a href="<?= $this->url('news/' . $news['id']) ?>" class="btn btn-light me-2">Annuler</a>

                        <?php
                        $isAuthor = $news['auteurId'] == $auth->getUser()['id'];
                        $canDelete = $isAuthor ?
                            $auth->hasPermission('delete_own_news') :
                            $auth->hasPermission('delete_news');

                        if ($canDelete):
                            ?>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fa fa-trash"></i> Supprimer
                            </button>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($canDelete): ?>
        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer cette actualité ? Cette action est irréversible.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form action="<?= $this->url('news/delete/' . $news['id']) ?>" method="post">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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

        // Handle image removal checkbox
        const removeImageCheckbox = document.getElementById('remove_image');
        const imageInput = document.getElementById('image');

        if (removeImageCheckbox && imageInput) {
            removeImageCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    imageInput.disabled = true;
                } else {
                    imageInput.disabled = false;
                }
            });

            imageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    removeImageCheckbox.checked = false;
                }
            });
        }
    });
</script>