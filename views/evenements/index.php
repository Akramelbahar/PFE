<!-- views/evenements/index.php -->
<div class="events-index-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tous les Événements</h1>
        <?php if ($auth->hasPermission('register_event')): ?>
            <a href="<?php echo $this->url('events/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Créer un événement
            </a>
        <?php endif; ?>
    </div>

    <!-- Quick Navigation -->
    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <a href="<?php echo $this->url('events/seminaires'); ?>" class="card text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Séminaires</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-2">
            <a href="<?php echo $this->url('events/conferences'); ?>" class="card text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Conférences</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-2">
            <a href="<?php echo $this->url('events/workshops'); ?>" class="card text-decoration-none">
                <div class="card-body text-center">
                    <i class="fas fa-laptop-code fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Ateliers</h5>
                </div>
            </a>
        </div>
    </div>

    <!-- Events Calendar -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Calendrier des Événements</h4>
        </div>
        <div class="card-body">
            <div id="events-calendar"></div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Liste des Événements</h4>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="event-view" id="grid-view" autocomplete="off" checked>
                <label class="btn btn-outline-light" for="grid-view">
                    <i class="fas fa-th"></i>
                </label>
                <input type="radio" class="btn-check" name="event-view" id="list-view" autocomplete="off">
                <label class="btn btn-outline-light" for="list-view">
                    <i class="fas fa-list"></i>
                </label>
            </div>
        </div>

        <div id="events-grid-view" class="row g-4 p-3">
            <?php if (empty($events)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Aucun événement trouvé.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="badge bg-<?php
                                switch ($event['type']) {
                                    case 'Seminaire': echo 'info'; break;
                                    case 'Conference': echo 'success'; break;
                                    case 'Workshop': echo 'warning'; break;
                                    default: echo 'secondary';
                                }
                                ?>">
                                    <?php echo $this->escape($event['type']); ?>
                                </span>
                                <small class="text-muted">
                                    <?php echo $this->formatDate($event['eventDate'], 'd/m/Y'); ?>
                                </small>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $this->escape($event['titre']); ?></h5>
                                <p class="card-text">
                                    <?php echo $this->truncate($event['description'], 100); ?>
                                </p>
                                <p class="text-muted">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo $this->escape($event['lieu']); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Créé par <?php echo $this->escape($event['createurPrenom'] . ' ' . $event['createurNom']); ?>
                                </small>
                                <a href="<?php echo $this->url('events/' . $event['id']); ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    Détails
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="events-list-view" class="table-responsive" style="display:none;">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Lieu</th>
                    <th>Créateur</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo $this->escape($event['titre']); ?></td>
                        <td>
                                <span class="badge bg-<?php
                                switch ($event['type']) {
                                    case 'Seminaire': echo 'info'; break;
                                    case 'Conference': echo 'success'; break;
                                    case 'Workshop': echo 'warning'; break;
                                    default: echo 'secondary';
                                }
                                ?>">
                                    <?php echo $this->escape($event['type']); ?>
                                </span>
                        </td>
                        <td><?php echo $this->formatDate($event['eventDate'], 'd/m/Y'); ?></td>
                        <td><?php echo $this->escape($event['lieu']); ?></td>
                        <td><?php echo $this->escape($event['createurPrenom'] . ' ' . $event['createurNom']); ?></td>
                        <td>
                            <a href="<?php echo $this->url('events/' . $event['id']); ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css" rel="stylesheet">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View toggle
        const gridViewRadio = document.getElementById('grid-view');
        const listViewRadio = document.getElementById('list-view');
        const gridView = document.getElementById('events-grid-view');
        const listView = document.getElementById('events-list-view');

        gridViewRadio.addEventListener('change', function() {
            gridView.style.display = 'flex';
            listView.style.display = 'none';
        });

        listViewRadio.addEventListener('change', function() {
            gridView.style.display = 'none';
            listView.style.display = 'table-row-group';
        });

        // Calendar initialization
        var calendarEl = document.getElementById('events-calendar');
        fetch('<?php echo $this->url('events/json'); ?>')
            .then(response => response.json())
            .then(events => {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'fr',
                    events: events,
                    eventClick: function(info) {
                        window.location.href = info.event.extendedProps.url;
                    }
                });
                calendar.render();
            });
    });
</script>