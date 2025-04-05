<!-- views/admin/contacts.php -->
<div class="admin-contacts">
    <h1 class="mb-4">Messages de contact</h1>

    <div class="row mb-4">
        <div class="col">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des messages</h5>
                        <button type="button" class="btn btn-light btn-sm" id="bulkDeleteBtn" disabled>
                            <i class="fas fa-trash"></i> Supprimer la sélection
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="filters mb-4">
                        <form action="<?php echo $this->url('admin/contacts'); ?>" method="get" class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="Non lu" <?php echo isset($_GET['status']) && $_GET['status'] === 'Non lu' ? 'selected' : ''; ?>>Non lu</option>
                                    <option value="Lu" <?php echo isset($_GET['status']) && $_GET['status'] === 'Lu' ? 'selected' : ''; ?>>Lu</option>
                                    <option value="Répondu" <?php echo isset($_GET['status']) && $_GET['status'] === 'Répondu' ? 'selected' : ''; ?>>Répondu</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date de début</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo isset($_GET['date_from']) ? $this->escape($_GET['date_from']) : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date de fin</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo isset($_GET['date_to']) ? $this->escape($_GET['date_to']) : ''; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Recherche</label>
                                <input type="text" name="search" id="search" class="form-control" value="<?php echo isset($_GET['search']) ? $this->escape($_GET['search']) : ''; ?>" placeholder="Nom, email ou sujet">
                            </div>
                            <div class="col-md-12 d-flex">
                                <button type="submit" class="btn btn-secondary">Filtrer</button>
                                <a href="<?php echo $this->url('admin/contacts'); ?>" class="btn btn-outline-secondary ms-2">Réinitialiser</a>
                            </div>
                        </form>
                    </div>

                    <!-- Contacts Table -->
                    <form action="<?php echo $this->url('admin/contacts/bulk-delete'); ?>" method="post" id="bulkDeleteForm">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input select-all" type="checkbox" id="selectAll">
                                            <label class="form-check-label" for="selectAll"></label>
                                        </div>
                                    </th>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Sujet</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($contacts)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Aucun message trouvé</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($contacts as $contact): ?>
                                        <tr>
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input contact-select" type="checkbox" name="ids[]" value="<?php echo $contact['id']; ?>" id="contact<?php echo $contact['id']; ?>">
                                                    <label class="form-check-label" for="contact<?php echo $contact['id']; ?>"></label>
                                                </div>
                                            </td>
                                            <td><?php echo $contact['id']; ?></td>
                                            <td><?php echo $this->escape($contact['nom']); ?></td>
                                            <td><?php echo $this->escape($contact['email']); ?></td>
                                            <td><?php echo $this->escape($contact['sujet'] ?? 'Sans sujet'); ?></td>
                                            <td><?php echo $this->formatDate($contact['dateEnvoi']); ?></td>
                                            <td>
                                            <span class="badge <?php echo $contact['status'] === 'Non lu' ? 'bg-danger' : ($contact['status'] === 'Lu' ? 'bg-warning' : 'bg-success'); ?>">
                                                <?php echo $contact['status']; ?>
                                            </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo $this->url('admin/contacts/' . $contact['id']); ?>" class="btn btn-sm btn-info" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" title="Supprimer"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $contact['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Delete Modal -->
                                                <div class="modal fade" id="deleteModal<?php echo $contact['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Êtes-vous sûr de vouloir supprimer le message de <strong><?php echo $this->escape($contact['nom']); ?></strong> ?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form action="<?php echo $this->url('admin/contacts/' . $contact['id'] . '/delete'); ?>" method="post">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
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
    </div>

    <!-- Contact Stats -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Messages par statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="contactsByStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Messages par mois</h5>
                </div>
                <div class="card-body">
                    <canvas id="contactsByMonthChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer les messages sélectionnés ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmBulkDelete">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bulk selection
        const selectAll = document.getElementById('selectAll');
        const contactSelects = document.querySelectorAll('.contact-select');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        const confirmBulkDelete = document.getElementById('confirmBulkDelete');

        // Toggle select all
        selectAll.addEventListener('change', function() {
            contactSelects.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            updateBulkDeleteBtn();
        });

        // Update bulk delete button state
        contactSelects.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBulkDeleteBtn();

                // Update select all checkbox
                if (!this.checked) {
                    selectAll.checked = false;
                } else {
                    const allChecked = Array.from(contactSelects).every(cb => cb.checked);
                    selectAll.checked = allChecked;
                }
            });
        });

        function updateBulkDeleteBtn() {
            const checkedCount = document.querySelectorAll('.contact-select:checked').length;
            bulkDeleteBtn.disabled = checkedCount === 0;
            bulkDeleteBtn.innerText = checkedCount > 0 ? `Supprimer (${checkedCount})` : 'Supprimer la sélection';
        }

        // Bulk delete
        bulkDeleteBtn.addEventListener('click', function() {
            $('#bulkDeleteModal').modal('show');
        });

        confirmBulkDelete.addEventListener('click', function() {
            bulkDeleteForm.submit();
        });

        // Contact Stats Charts
        var statusCtx = document.getElementById('contactsByStatusChart').getContext('2d');
        var statusStats = <?php
            // Prepare data for chart
            $statusCounts = [
                'Non lu' => 0,
                'Lu' => 0,
                'Répondu' => 0
            ];

            if (!empty($contacts)) {
                foreach ($contacts as $contact) {
                    $status = $contact['status'] ?? 'Non lu';
                    if (isset($statusCounts[$status])) {
                        $statusCounts[$status]++;
                    }
                }
            }

            echo json_encode([
                'labels' => array_keys($statusCounts),
                'data' => array_values($statusCounts)
            ]);
            ?>;

        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: statusStats.labels,
                datasets: [{
                    data: statusStats.data,
                    backgroundColor: [
                        '#dc3545', // danger - Non lu
                        '#ffc107', // warning - Lu
                        '#28a745'  // success - Répondu
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Contacts by Month Chart
        var monthCtx = document.getElementById('contactsByMonthChart').getContext('2d');
        var monthStats = <?php
            // Generate data for the last 6 months
            $monthlyData = [];
            $currentMonth = date('n');
            $currentYear = date('Y');

            for ($i = 0; $i < 6; $i++) {
                $month = (($currentMonth - $i - 1) % 12) + 1;
                $year = $currentYear;
                if ($month > $currentMonth) {
                    $year--;
                }

                $monthLabel = date('M Y', mktime(0, 0, 0, $month, 1, $year));
                $monthlyData[$monthLabel] = 0;
            }

            // Reverse to chronological order
            $monthlyData = array_reverse($monthlyData);

            if (!empty($contacts)) {
                foreach ($contacts as $contact) {
                    $month = date('M Y', strtotime($contact['dateEnvoi']));
                    if (isset($monthlyData[$month])) {
                        $monthlyData[$month]++;
                    }
                }
            }

            echo json_encode([
                'labels' => array_keys($monthlyData),
                'data' => array_values($monthlyData)
            ]);
            ?>;

        new Chart(monthCtx, {
            type: 'bar',
            data: {
                labels: monthStats.labels,
                datasets: [{
                    label: 'Messages reçus',
                    data: monthStats.data,
                    backgroundColor: '#6c757d',
                    borderColor: '#5a6268',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>