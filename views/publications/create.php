<!-- views/publications/create.php -->
<div class="publications-create-page">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Créer une nouvelle publication</h3>
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

                    <form action="<?php echo $this->url('publications/create'); ?>" method="post" enctype="multipart/form-data">
                        <?php echo CSRF::tokenField(); ?>

                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" id="titre" name="titre"
                                   class="form-control"
                                   value="<?php echo isset($titre) ? $this->escape($titre) : ''; ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type de publication <span class="text-danger">*</span></label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="">Sélectionner un type</option>
                                <option value="Article" <?php echo (isset($type) && $type === 'Article') ? 'selected' : ''; ?>>Article</option>
                                <option value="Livre" <?php echo (isset($type) && $type === 'Livre') ? 'selected' : ''; ?>>Livre</option>
                                <option value="Chapitre" <?php echo (isset($type) && $type === 'Chapitre') ? 'selected' : ''; ?>>Chapitre</option>
                            </select>
                        </div>

                        <!-- Livre Père (for Chapitre) -->
                        <div id="livre-pere-section" class="mb-3" style="display:none;">
                            <label for="livre_pere" class="form-label">Livre parent</label>
                            <select id="livre_pere" name="livre_pere" class="form-select">
                                <option value="">Sélectionner un livre</option>
                                <!-- Livres will be populated dynamically via JavaScript -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                            <textarea id="contenu" name="contenu"
                                      class="form-control"
                                      rows="6"
                                      required><?php echo isset($contenu) ? $this->escape($contenu) : ''; ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="evenement_id" class="form-label">Événement associé</label>
                                <select id="evenement_id" name="evenement_id" class="form-select">
                                    <option value="">Aucun événement</option>
                                    <!-- Populate with events -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="projet_id" class="form-label">Projet associé</label>
                                <select id="projet_id" name="projet_id" class="form-select">
                                    <option value="">Aucun projet</option>
                                    <!-- Populate with projects -->
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="documents" class="form-label">Documents (PDF, DOC, DOCX, images)</label>
                            <input type="file" id="documents" name="documents[]"
                                   class="form-control"
                                   multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Vous pouvez télécharger plusieurs fichiers</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo $this->url('publications'); ?>" class="btn btn-secondary">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer la publication
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
        const typeSelect = document.getElementById('type');
        const livrePereSection = document.getElementById('livre-pere-section');
        const livrePereSelect = document.getElementById('livre_pere');

        function toggleLivrePereSection() {
            if (typeSelect.value === 'Chapitre') {
                livrePereSection.style.display = 'block';

                // Fetch available books via AJAX
                fetch('<?php echo $this->url('publications/get-books'); ?>')
                    .then(response => response.json())
                    .then(books => {
                        livrePereSelect.innerHTML = '<option value="">Sélectionner un livre</option>';
                        books.forEach(book => {
                            const option = document.createElement('option');
                            option.value = book.id;
                            option.textContent = book.titre;
                            livrePereSelect.appendChild(option);
                        });
                    });
            } else {
                livrePereSection.style.display = 'none';
            }
        }

        typeSelect.addEventListener('change', toggleLivrePereSection);
        toggleLivrePereSection(); // Initial check
    });
</script>