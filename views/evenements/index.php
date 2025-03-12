<!-- views/evenements/index.php -->
<div class="events-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Événements</h1>
        <?php if ($auth->hasPermission('create_event')): ?>
            <a href="<?php echo $this->url('events/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvel événement
            </a>
        <?php endif; ?>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo $this->url('events'); ?>" method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="Seminaire" <?php echo isset($_GET['type']) && $_GET['type'] === 'Seminaire' ? 'selected' : ''; ?>>Séminaire</option>
                        <option value="Conference" <?php echo isset($_GET['type']) && $_GET['type'] === 'Conference' ? 'selected' : ''; ?>>Conférence</option>
                        <option value="Workshop" <?php echo isset($_GET['type']) && $_GET['type'] === 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="period" class="form-label">Période</label>
                    <select name="period" id="period" class="form-select">
                        <option value="">Toutes les périodes</option>
                        <option value="past" <?php echo isset($_GET['period']) && $_GET['period'] === 'past' ? 'selected' : ''; ?>>Événements passés</option>
                        <option value="upcoming" <?php echo isset($_GET['period']) && $_GET['period'] === 'upcoming' ? 'selected' : ''; ?>>Événements à venir</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Titre, lieu, description..." value="<?php echo isset($_GET['search']) ? $this->escape($_GET['search']) : ''; ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Calendar View Toggle -->
    <div class="d-flex justify-content-end mb-3">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary active" id="list-view-btn">
                <i class="fas fa-list"></i> Liste
            </button>
            <button type="button" class="btn btn-outline-primary" id="calendar-view-btn">
                <i class="fas fa-calendar-alt"></i> Calendrier
            </button>
        </div>
    </div>

    <!-- List View -->
    <div id="list-view">
        <?php if (empty($events)): ?>
            <div class="alert alert-info">
                Aucun événement trouvé. Veuillez modifier vos critères de recherche ou <a href="<?php echo $this->url('events/create'); ?>">créer un nouvel événement</a>.
            </div>
        <?php else: ?>
            <!-- Event Cards -->
            <div class="row">
                <?php foreach ($events as $event): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between">
                                <span class="badge <?php
                                switch($event['type']) {
                                    case 'Seminaire':
                                        echo 'bg-info';
                                        break;
                                    case 'Conference':
                                        echo 'bg-primary';
                                        break;
                                    case 'Workshop':
                                        echo 'bg-success';
                                        break;
                                    default:
                                        echo 'bg-secondary';
                                }
                                ?>"><?php echo $this->escape($event['type']); ?></span>

                                <?php
                                // Display appropriate date based on event type
                                $eventDate = '';
                                if ($event['type'] === 'Seminaire') {
                                    $eventDate = $this->formatDate($event['date'] ?? '', 'd/m/Y');
                                } else {
                                    $startDate = $this->formatDate($event['dateDebut'] ?? '', 'd/m/Y');
                                    $endDate = isset($event['dateFin']) ? $this->formatDate($event['dateFin'], 'd/m/Y') : '';
                                    $eventDate = $startDate . ($endDate && $startDate !== $endDate ? ' - ' . $endDate : '');
                                }
                                ?>
                                <span><?php echo $eventDate; ?></span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $this->escape($event['titre']); ?></h5>
                                <p class="card-text"><?php echo $this->truncate($event['description'], 100); ?></p>
                                <p class="card-text">
                                    <strong><i class="fas fa-map-marker-alt"></i></strong> <?php echo $this->escape($event['lieu']); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="<?php echo $this->url('events/' . $event['id']); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> Détails
                                </a>

                                <?php if ($auth->getUser() && ($auth->hasPermission('edit_event') || $event['createurId'] == $auth->getUser()['id'])): ?>
                                    <a href="<?php echo $this->url('events/edit/' . $event['id']); ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Calendar View (hidden by default) -->
    <div id="calendar-view" style="display: none;">
        <div class="card">
            <div class="card-body">
                <div id="events-calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Include FullCalendar library -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // FullCalendar initialization
        var calendarEl = document.getElementById('events-calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: '<?php echo $this->url('events/json'); ?>',
            eventClick: function(info) {
                window.location.href = info.event.url;
            },
            eventColor: '#3788d8',
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            firstDay: 1, // Monday as first day
            locale: 'fr'
        });

        calendar.render();

        // View toggle handlers
        document.getElementById('list-view-btn').addEventListener('click', function() {
            document.getElementById('list-view').style.display = 'block';
            document.getElementById('calendar-view').style.display = 'none';
            this.classList.add('active');
            document.getElementById('calendar-view-btn').classList.remove('active');
        });

        document.getElementById('calendar-view-btn').addEventListener('click', function() {
            document.getElementById('list-view').style.display = 'none';
            document.getElementById('calendar-view').style.display = 'block';
            this.classList.add('active');
            document.getElementById('list-view-btn').classList.remove('active');
            calendar.updateSize(); // Ensure calendar renders properly
        });
    });
</script>