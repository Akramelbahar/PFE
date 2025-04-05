<!-- views/publications/edit.php -->
<div class="publications-edit-page">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Modifier la publication</h3>
                    <span class="badge bg-<?php
                    switch ($publicationType) {
                        case 'Article': echo 'info'; break;
                        case 'Livre': echo 'success'; break;
                        case 'Chapitre': echo 'warning'; break;
                        default: echo 'secondary';
                    }
                    ?>">
                        <?php echo $this->escape($publicationType); ?>
                    </span>
                </div>

                <div class="card-body">
                    <?php if (isset($errorMessage) && $errorMessage): ?>
                        <div class="alert alert-danger">
                            <?php echo $this->escape($errorMessage); ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $this->url('publications/edit/' . $publication['id']); ?>" method="post" enctype="multipart/form-data">
                        <?php echo CSRF::tokenField(); ?>

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" id="titre" name="titre"
                                   class="form-control"
                                   value="<?php echo $this->escape($publication['titre']); ?>"
                                   required>
                        </div>

                        <!-- Chapitre-specific Book Selection -->
                        <?php if ($publicationType === 'Chapitre'): ?>
                            <div class="mb-3">
                                <label for="livre_pere" class="form-label">Livre parent</label>
                                <select id="livre_pere" name="livre_pere" class="form-select">
                                    <option value="">Sélectionner un livre</option>
                                    <!-- Books to be populated dynamically -->
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                            <textarea id="contenu" name="contenu"
                                      class="form-control"
                                      rows="6"
                                      required><?php echo $this->escape($publication['contenu']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="documents" class="form-label">Documents</label>
                            <input type="file" id="documents" name="documents[]"
                                   class="form-control"
                                   multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Vous pouvez télécharger plusieurs fichiers</div>
                        </div>

                        <!-- Existing Documents -->
                        <?php
                        $documents = json_decode($publication['documents'] ?? '[]', true);
                        if (!empty($documents)):
                            ?>
                            <div class="card mb-3">
                                <div class="card-header">Documents existants</div>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($documents as $document): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-file me-2"></i>
                                                <?php echo $this->escape($document['originalName']); ?>
                                            </span>
                                            <div>
                                                <span class="text-muted me-3">
                                                    <?php echo round($document['size'] / 1024, 2); ?> Ko
                                                </span>
                                                <a href="<?php echo $this->url('publications/download/' . $publication['id'] . '/' . $document['filename']); ?>"
                                                   class="btn btn-sm btn-outline-primary me-2"
                                                   title="Télécharger">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <!-- Replace the delete document link in edit.php with this -->
                                                <a href="#"
                                                   onclick="deletePublicationDocument('<?php echo $this->url('publications/delete-document/' . $publication['id'] . '/' . urlencode($document['filename'])); ?>')"
                                                   class="btn btn-sm btn-outline-danger"
                                                   title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo $this->url('publications/' . $publication['id']); ?>" class="btn btn-secondary">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Document deletion confirmation
        const deleteDocumentButtons = document.querySelectorAll('.delete-document');
        deleteDocumentButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Voulez-vous vraiment supprimer ce document ?')) {
                    e.preventDefault();
                }
            });
        });

        <?php if ($publicationType === 'Chapitre'): ?>
        // Populate livre_pere select for Chapitre
        const livrePereSelect = document.getElementById('livre_pere');
        fetch('<?php echo $this->url('publications/get-books'); ?>')
            .then(response => response.json())
            .then(books => {
                const currentBook = <?php echo json_encode($publicationDetails['book']['publicationId'] ?? null); ?>;
                livrePereSelect.innerHTML = '<option value="">Sélectionner un livre</option>';
                books.forEach(book => {
                    const option = document.createElement('option');
                    option.value = book.id;
                    option.textContent = book.titre;
                    option.selected = book.id === currentBook;
                    livrePereSelect.appendChild(option);
                });
            });
        <?php endif; ?>
    });
    function deletePublicationDocument(url) {
        if (confirm('Voulez-vous vraiment supprimer ce document ?')) {
            // Create a form to submit the delete request as POST
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            // Add CSRF token if needed
            <?php if (function_exists('CSRF::getToken')): ?>
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '<?php echo CSRF::getToken(); ?>';
            form.appendChild(csrfInput);
            <?php endif; ?>

            document.body.appendChild(form);
            form.submit();
        }
        return false;
    }
</script>