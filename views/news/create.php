<!-- views/news/create.php -->
<style>
    .form-header {
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

    .btn-primary, .btn-secondary, .btn-outline-primary {
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 500;
    }
</style>

<div class="container py-4">
    <!-- Form Header Section -->
    <div class="form-header text-center">
        <h1 class="display-6 fw-bold">Nouvelle Actualité</h1>
        <p class="lead">Créez et publiez une nouvelle actualité pour informer la communauté</p>
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

    <!-- News Creation Form -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="form-card card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-pen-fancy me-2"></i>Informations de l'actualité
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $this->url('news/store'); ?>" method="post" enctype="multipart/form-data" id="newsForm">
                        <!-- Title Field -->
                        <div class="mb-4">
                            <label for="titre" class="form-label">
                                Titre<span class="required-indicator">*</span>
                            </label>
                            <input type="text" class="form-control" id="titre" name="titre"
                                   value="<?php echo isset($titre) ? $this->escape($titre) : ''; ?>"
                                   placeholder="Saisissez un titre captivant" required>
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
                                      rows="12" required placeholder="Écrivez le contenu de votre actualité..."><?php echo isset($contenu) ? $this->escape($contenu) : ''; ?></textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>Utilisez l'éditeur pour formater votre contenu. Vous pouvez ajouter des titres, des listes, des liens, etc.
                            </div>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="mb-4">
                            <label for="image" class="form-label">Image de couverture</label>

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
                                        <option value="<?php echo $event['id']; ?>" <?php echo isset($evenement_id) && $evenement_id == $event['id'] ? 'selected' : ''; ?>>
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
                            <a href="<?php echo $this->url('news'); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Publier l'actualité
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for form handling -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form validation
        const form = document.getElementById('newsForm');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');

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
                    }

                    reader.readAsDataURL(this.files[0]);
                } else {
                    // Reset preview if no file is selected
                    imagePreview.innerHTML = '<i class="fas fa-image fa-3x text-muted"></i>';
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