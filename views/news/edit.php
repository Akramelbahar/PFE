<!-- views/news/edit.php -->
<style>
    .edit-header {
        background-color: var(--uca-blue);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
    }

    .form-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .form-card .card-header {
        background-color: rgba(0, 91, 172, 0.05);
        border-bottom: 1px solid rgba(0, 91, 172, 0.1);
        padding: 1rem 1.5rem;
    }

    .form-card .card-body {
        padding: 2rem;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #ced4da;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: rgba(0, 91, 172, 0.5);
        box-shadow: 0 0 0 0.25rem rgba(0, 91, 172, 0.25);
    }

    .form-text {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.5rem;
    }

    .required-indicator {
        color: #dc3545;
        margin-left: 4px;
    }

    .form-footer {
        display: flex;
        justify-content: space-between;
        padding-top: 1.5rem;
        margin-top: 1.5rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .current-image-container {
        position: relative;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .current-image-container img {
        width: 100%;
        height: auto;
        max-height: 250px;
        object-fit: contain;
        border-radius: 8px;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .current-image-container:hover .image-overlay {
        opacity: 1;
    }

    .upload-preview {
        width: 100%;
        height: 200px;
        background-color: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .upload-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .btn-primary, .btn-secondary, .btn-outline-primary, .btn-danger, .btn-warning, .btn-info {
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 500;
    }

    .action-buttons .btn {
        margin-right: 0.5rem;
    }

    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
    }
</style>

<div class="container py-4">
    <!-- Form Header Section -->
    <div class="edit-header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="display-6 fw-bold mb-0">Modifier l'actualité</h1>
            <div class="action-buttons">
                <a href="<?php echo $this->url('news/' . $news['id']); ?>" class="btn btn-light btn-sm">
                    <i class="fas fa-eye me-1"></i> Voir l'actualité
                </a>
                <a href="<?php echo $this->url('news'); ?>" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
        <p class="lead"><?php echo $this->escape($news['titre']); ?></p>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($flash) && $flash): ?>
        <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $flash['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display validation errors if any -->
    <?php if (isset($errors) && is_array($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation</h5>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $field => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <li><?php echo $field; ?>: <?php echo $this->escape($error); ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- News Edit Form -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="form-card card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Modifier l'actualité
                    </h5>
                    <span class="badge bg-info">
                        <i class="far fa-calendar-alt me-1"></i> Publiée le <?php echo $this->formatDate($news['datePublication'], 'd/m/Y'); ?>
                    </span>
                </div>
                <div class="card-body">
                    <form action="<?php echo $this->url('news/update/' . $news['id']); ?>" method="post" enctype="multipart/form-data" id="newsForm">
                        <!-- Title Field -->
                        <div class="mb-4">
                            <label for="titre" class="form-label">
                                Titre<span class="required-indicator">*</span>
                            </label>
                            <input type="text" class="form-control" id="titre" name="titre"
                                   value="<?php echo $this->escape($news['titre']); ?>" required>
                            <div class="form-text">
                                Le titre doit être clair et concis (maximum 255 caractères).
                            </div>
                        </div>

                        <!-- Content Field -->
                        <div class="mb-4">
                            <label for="contenu" class="form-label">
                                Contenu<span class="required-indicator">*</span>
                            </label>
                            <textarea class="form-control rich-editor" id="contenu" name="contenu"
                                      rows="12" required><?php echo $this->escape($news['contenu']); ?></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>Utilisez l'éditeur pour formater votre contenu.
                            </div>
                        </div>

                        <!-- Current Image Section (if exists) -->
                        <?php if (!empty($news['mediaUrl'])): ?>
                            <div class="mb-4">
                                <label class="form-label">Image actuelle</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="current-image-container">
                                            <img src="<?php echo $this->url($news['mediaUrl']); ?>"
                                                 alt="Image actuelle" class="img-fluid">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox"
                                                   id="remove_image" name="remove_image" value="1">
                                            <label class="form-check-label" for="remove_image">
                                                <i class="fas fa-trash-alt text-danger me-1"></i>
                                                Supprimer l'image actuelle
                                            </label>
                                            <div class="form-text">
                                                Cochez cette case si vous souhaitez supprimer l'image sans la remplacer.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Upload New Image Section -->
                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <?php echo !empty($news['mediaUrl']) ? 'Remplacer l\'image' : 'Ajouter une image'; ?>
                            </label>

                            <div class="upload-preview" id="imagePreview">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>

                            <input class="form-control" type="file" id="image" name="image" accept="image/*">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>Format recommandé: 1200 x 630 pixels, JPG ou PNG, max 2MB.
                            </div>
                        </div>

                        <!-- Related Event Selection (if available) -->
                        <?php if (!empty($events)): ?>
                            <div class="mb-4">
                                <label for="evenement_id" class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>Lier à un événement
                                </label>
                                <select class="form-select" id="evenement_id" name="evenement_id">
                                    <option value="">Aucun événement</option>
                                    <?php foreach ($events as $event): ?>
                                        <option value="<?php echo $event['id']; ?>" <?php echo isset($news['evenementId']) && $news['evenementId'] == $event['id'] ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($event['titre']); ?>
                                            (<?php echo isset($event['eventDate']) ? $this->formatDate($event['eventDate'], 'd/m/Y') : $this->formatDate($event['dateCreation'], 'd/m/Y'); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-link me-1"></i>Associer cette actualité à un événement la fait apparaître sur la page de l'événement correspondant.
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Form Footer with Buttons -->
                        <div class="form-footer">
                            <div>
                                <a href="<?php echo $this->url('news/' . $news['id']); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>

                                <?php
                                $isAuthor = $news['auteurId'] == $auth->getUser()['id'];
                                $canDelete = $isAuthor ?
                                    $auth->hasPermission('delete_own_news') :
                                    $auth->hasPermission('delete_news');

                                if ($canDelete):
                                    ?>
                                    <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash-alt me-1"></i>Supprimer
                                    </button>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($canDelete) && $canDelete): ?>
        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer l'actualité <strong><?php echo $this->escape($news['titre']); ?></strong> ?</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Cette action est irréversible et supprimera définitivement cette actualité.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Annuler
                        </button>
                        <form action="<?php echo $this->url('news/delete/' . $news['id']); ?>" method="post">
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-1"></i>Supprimer définitivement
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript for form handling -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form elements
        const form = document.getElementById('newsForm');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageCheckbox = document.getElementById('remove_image');

        // Handle image preview
        if (imageInput && imagePreview) {
            imageInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        // Clear previous content
                        imagePreview.innerHTML = '';

                        // Create image element
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Image preview';

                        // Add image to preview
                        imagePreview.appendChild(img);

                        // If we have a remove checkbox, uncheck it when uploading a new image
                        if (removeImageCheckbox) {
                            removeImageCheckbox.checked = false;
                        }
                    }

                    reader.readAsDataURL(this.files[0]);
                } else {
                    // Reset preview if no file is selected
                    imagePreview.innerHTML = '<i class="fas fa-image fa-3x text-muted"></i>';
                }
            });
        }

        // Handle remove image checkbox
        if (removeImageCheckbox && imageInput) {
            removeImageCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    imageInput.disabled = true;
                    imageInput.classList.add('text-muted');

                    if (imagePreview) {
                        imagePreview.innerHTML = '<i class="fas fa-ban fa-3x text-danger"></i>';
                    }
                } else {
                    imageInput.disabled = false;
                    imageInput.classList.remove('text-muted');

                    if (imagePreview) {
                        imagePreview.innerHTML = '<i class="fas fa-image fa-3x text-muted"></i>';
                    }
                }
            });

            imageInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    removeImageCheckbox.checked = false;
                    imageInput.disabled = false;
                }
            });
        }

        // Form validation
        if (form) {
            form.addEventListener('submit', function(event) {
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
                    // Scroll to the first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });

            // Clear validation styling on input
            const fields = form.querySelectorAll('input, select, textarea');
            fields.forEach(function(field) {
                field.addEventListener('input', function() {
                    if (field.value.trim()) {
                        field.classList.remove('is-invalid');
                    }
                });
            });
        }

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