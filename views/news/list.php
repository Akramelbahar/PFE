
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Messages de contact</h1>
            <div>
                <button type="button" class="btn btn-danger" id="bulk-delete-btn" disabled data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Supprimer la sélection
                </button>
            </div>
        </div>

        <?php if (isset($flash['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($flash['success']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($flash['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($flash['error']) ?>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Filtres</h5>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>/admin/contacts" method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous</option>
                            <option value="Non lu" <?= (isset($filters['status']) && $filters['status'] === 'Non lu') ? 'selected' : '' ?>>Non lu</option>
                            <option value="Lu" <?= (isset($filters['status']) && $filters['status'] === 'Lu') ? 'selected' : '' ?>>Lu</option>
                            <option value="Répondu" <?= (isset($filters['status']) && $filters['status'] === 'Répondu') ? 'selected' : '' ?>>Répondu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="date_from" name="date_from"
                               value="<?= isset($filters['date_from']) ? htmlspecialchars($filters['date_from']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="date_to" name="date_to"
                               value="<?= isset($filters['date_to']) ? htmlspecialchars($filters['date_to']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search"
                                   placeholder="Nom, email, sujet..."
                                   value="<?= isset($filters['search']) ? htmlspecialchars($filters['search']) : '' ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form id="bulk-delete-form" action="<?= BASE_URL ?>/admin/contacts/bulk-delete" method="post">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="40">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all">
                                    </div>
                                </th>
                                <th width="180">Date</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Sujet</th>
                                <th width="100">Statut</th>
                                <th width="100">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($contacts)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">Aucun message trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($contacts as $contact): ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input contact-checkbox" type="checkbox"
                                                       name="ids[]" value="<?= $contact['id'] ?>">
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($contact['dateEnvoi']))) ?></td>
                                        <td><?= htmlspecialchars($contact['nom']) ?></td>
                                        <td><?= htmlspecialchars($contact['email']) ?></td>
                                        <td><?= htmlspecialchars($contact['sujet'] ?? 'Sans sujet') ?></td>
                                        <td>
                                            <?php if ($contact['status'] === 'Non lu'): ?>
                                                <span class="badge bg-danger">Non lu</span>
                                            <?php elseif ($contact['status'] === 'Lu'): ?>
                                                <span class="badge bg-warning">Lu</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Répondu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="<?= BASE_URL ?>/admin/contacts/<?= $contact['id'] ?>" class="btn btn-sm btn-outline-primary me-1"
                                                   title="Voir">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-id="<?= $contact['id'] ?>" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"
                                                        title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer les messages sélectionnés ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirm-bulk-delete">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Single Delete Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce message ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="delete-form" action="" method="post" class="d-inline">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle select all checkbox
            const selectAllCheckbox = document.getElementById('select-all');
            const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

            selectAllCheckbox.addEventListener('change', function() {
                contactCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
                updateBulkDeleteButton();
            });

            contactCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBulkDeleteButton();

                    // Update select all checkbox
                    const allChecked = Array.from(contactCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(contactCheckboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                });
            });

            function updateBulkDeleteButton() {
                const checkedCount = document.querySelectorAll('.contact-checkbox:checked').length;
                bulkDeleteBtn.disabled = checkedCount === 0;
            }

            // Handle bulk delete confirmation
            document.getElementById('confirm-bulk-delete').addEventListener('click', function() {
                document.getElementById('bulk-delete-form').submit();
            });

            // Handle single delete confirmation
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const contactId = this.getAttribute('data-id');
                    document.getElementById('delete-form').action = '<?= BASE_URL ?>/admin/contacts/' + contactId + '/delete';
                });
            });
        });
    </script>

