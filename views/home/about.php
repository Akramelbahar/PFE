<!-- views/home/about.php -->

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h1 class="mb-3">À propos de notre association</h1>
                <p class="lead">Découvrez notre histoire, notre mission et nos objectifs pour promouvoir la recherche et l'innovation à l'EST de Safi</p>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <!-- About Section -->
    <section class="mb-5">
        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0">
                <h2 class="section-title-left">Qui sommes-nous ?</h2>
                <p>
                    L'Association Recherche et Innovation de l'École Supérieure de Technologie de Safi est une organisation dynamique fondée en 2020 par un groupe d'enseignants-chercheurs et d'étudiants passionnés par la recherche scientifique et l'innovation technologique.
                </p>
                <p>
                    Affiliée à l'Université Cadi Ayyad, notre association a pour objectif de promouvoir la recherche scientifique, de favoriser l'innovation technologique et de renforcer les liens entre le monde académique et le tissu socio-économique régional et national.
                </p>
                <p>
                    Notre association regroupe des chercheurs, des enseignants et des étudiants de différentes disciplines, ce qui nous permet d'aborder des problématiques de recherche avec une approche multidisciplinaire et innovante.
                </p>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h4 class="card-title">Notre bureau exécutif</h4>
                        <div class="timeline">
                            <?php if (empty($boardMembers)): ?>
                                <!-- Default board members if no data from database -->
                                <div class="timeline-item">
                                    <div class="d-flex">
                                        <div class="avatar avatar-md bg-primary me-3">MK</div>
                                        <div>
                                            <h5 class="mb-0">Prof. Mohammed Karim</h5>
                                            <p class="text-muted mb-1">Président</p>
                                            <p class="small mb-0">Département d'Informatique</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="d-flex">
                                        <div class="avatar avatar-md bg-info me-3">SA</div>
                                        <div>
                                            <h5 class="mb-0">Dr. Samira Amrani</h5>
                                            <p class="text-muted mb-1">Vice-présidente</p>
                                            <p class="small mb-0">Département de Génie Industriel</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="d-flex">
                                        <div class="avatar avatar-md bg-success me-3">HB</div>
                                        <div>
                                            <h5 class="mb-0">Prof. Hassan Bennani</h5>
                                            <p class="text-muted mb-1">Secrétaire Général</p>
                                            <p class="small mb-0">Département d'Électronique</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="d-flex">
                                        <div class="avatar avatar-md bg-warning me-3">FA</div>
                                        <div>
                                            <h5 class="mb-0">Dr. Fatima Alaoui</h5>
                                            <p class="text-muted mb-1">Trésorière</p>
                                            <p class="small mb-0">Département de Mathématiques Appliquées</p>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Display board members from database -->
                                <?php foreach ($boardMembers as $index => $member): ?>
                                    <div class="timeline-item">
                                        <div class="d-flex">
                                            <?php
                                            // Generate avatar color based on role
                                            $colors = ['primary', 'info', 'success', 'warning', 'danger', 'secondary'];
                                            $bgColor = $colors[$index % count($colors)];

                                            // Generate initials for avatar
                                            $initials = strtoupper(substr($member['prenom'], 0, 1) . substr($member['nom'], 0, 1));
                                            ?>
                                            <div class="avatar avatar-md bg-<?php echo $bgColor; ?> me-3"><?php echo $initials; ?></div>
                                            <div>
                                                <h5 class="mb-0"><?php echo $this->escape($member['prenom'] . ' ' . $member['nom']); ?></h5>
                                                <p class="text-muted mb-1"><?php echo $this->escape($member['role']); ?></p>
                                                <p class="small mb-0">
                                                    <?php
                                                    if (isset($member['departement'])) {
                                                        echo 'Département de ' . $this->escape($member['departement']);
                                                    } else {
                                                        echo 'Membre du bureau exécutif';
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission and Vision -->
    <section class="mb-5 py-5 bg-light rounded-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="me-3">
                                    <i class="fas fa-bullseye text-primary fa-2x"></i>
                                </div>
                                <h3 class="mb-0">Notre mission</h3>
                            </div>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div>
                                            Promouvoir la recherche scientifique et l'innovation au sein de l'EST de Safi
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-3">
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div>
                                            Favoriser les échanges et les collaborations entre chercheurs
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-3">
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div>
                                            Valoriser les résultats des travaux de recherche
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-3">
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div>
                                            Organiser des événements scientifiques et technologiques
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex">
                                        <div class="me-2">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div>
                                            Établir des partenariats avec les acteurs socio-économiques
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                <div class="me-3">
                                    <i class="fas fa-eye text-primary fa-2x"></i>
                                </div>
                                <h3 class="mb-0">Notre vision</h3>
                            </div>
                            <p>
                                Nous aspirons à faire de l'EST de Safi un pôle d'excellence dans la recherche scientifique et l'innovation technologique, reconnu au niveau national et international.
                            </p>
                            <p>
                                Notre vision est de créer un écosystème dynamique où chercheurs, étudiants et professionnels collaborent pour relever les défis actuels et futurs de notre société à travers des solutions innovantes et durables.
                            </p>
                            <p>
                                Nous croyons fermement que la recherche scientifique et l'innovation sont des moteurs essentiels pour le développement économique et social de notre région et de notre pays.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Axes de Recherche -->
    <section class="mb-5">
        <h2 class="section-title">Nos axes de recherche</h2>
        <div class="row mt-4">
            <?php
            // Check if domains data is available
            if (isset($domains) && !empty($domains)):
                // Loop through domains from database
                foreach ($domains as $domain):
                    // Default icon if not set
                    $icon = isset($domain['icon']) ? $domain['icon'] : 'flask';
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <i class="fas fa-<?php echo $icon; ?> text-primary fa-2x"></i>
                                    </div>
                                    <h4 class="mb-0"><?php echo $this->escape($domain['nom']); ?></h4>
                                </div>
                                <p>
                                    <?php echo $this->escape($domain['description']); ?>
                                </p>
                                <?php if (isset($domain['keywords'])): ?>
                                    <ul class="list-unstyled mt-3">
                                        <?php
                                        $keywords = explode(',', $domain['keywords']);
                                        foreach ($keywords as $keyword):
                                            ?>
                                            <li class="mb-2">
                                                <i class="fas fa-angle-right text-primary me-2"></i> <?php echo $this->escape(trim($keyword)); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
            else:
                // Default domains if not available from database
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-laptop-code text-primary fa-2x"></i>
                                </div>
                                <h4 class="mb-0">Intelligence Artificielle et Data Science</h4>
                            </div>
                            <p>
                                Développement d'algorithmes d'apprentissage automatique, analyse de données massives, systèmes de recommandation, vision par ordinateur, traitement du langage naturel.
                            </p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2">
                                    <i class="fas fa-angle-right text-primary me-2"></i> Machine learning et deep learning
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-angle-right text-primary me-2"></i> Big data et analytics
                                </li>
                                <li>
                                    <i class="fas fa-angle-right text-primary me-2"></i> Systèmes de décision intelligents
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-microchip text-primary fa-2x"></i>
                                </div>
                                <h4 class="mb-0">Systèmes Embarqués et IoT</h4>
                            </div>
                            <p>
                                Conception et développement de systèmes embarqués, Internet des objets, réseaux de capteurs, smart cities, domotique, cybersécurité des objets connectés.
                            </p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2">
                                    <i class="fas fa-angle-right text-primary me-2"></i> Réseaux de capteurs sans fil
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-angle-right text-primary me-2"></i> Smart cities et bâtiments intelligents
                                </li>
                                <li>
                                    <i class="fas fa-angle-right text-primary me-2"></i> Sécurité des systèmes IoT
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-cogs text-primary fa-2x"></i>
                                </div>
                                <h4 class="mb-0">Génie Industriel et Optimisation</h4>
                            </div>
                            <p>
                                Optimisation des processus industriels, logistique et chaîne d'approvisionnement, maintenance prédictive, industrie 4.0, gestion de la qualité.
                            </p>
                            <ul class="list-unstyled mt-3">
                                <li class="mb-2">
                                    <i class="fas fa-angle-right text-primary me-2"></i> Industrie 4.0 et fabrication intelligente
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-angle-right text-primary me-2"></i> Chaîne logistique et supply chain
                                </li>
                                <li>
                                    <i class="fas fa-angle-right text-primary me-2"></i> Maintenance prédictive
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Réalisations -->
    <section class="mb-5 py-5 bg-light rounded-3">
        <div class="container">
            <h2 class="section-title">Nos réalisations</h2>
            <div class="row mt-4">
                <div class="col-md-3 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <div class="display-4 text-primary mb-3">
                                <i class="fas fa-flask"></i>
                            </div>
                            <h3 class="display-4 mb-0"><?php echo isset($stats['projects']) ? $stats['projects'] : '12+'; ?></h3>
                            <p class="text-muted">Projets de recherche</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <div class="display-4 text-success mb-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <h3 class="display-4 mb-0"><?php echo isset($stats['publications']) ? $stats['publications'] : '30+'; ?></h3>
                            <p class="text-muted">Publications scientifiques</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <div class="display-4 text-info mb-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h3 class="display-4 mb-0"><?php echo isset($stats['events']) ? $stats['events'] : '15+'; ?></h3>
                            <p class="text-muted">Événements organisés</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <div class="display-4 text-warning mb-3">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h3 class="display-4 mb-0"><?php echo isset($stats['partners']) ? $stats['partners'] : '8+'; ?></h3>
                            <p class="text-muted">Partenariats établis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Partenaires -->
    <section class="mb-5">
        <h2 class="section-title">Nos partenaires</h2>
        <div class="row mt-4 justify-content-center">
            <div class="col-md-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row row-cols-2 row-cols-md-4 g-4 align-items-center">
                            <?php if (isset($partners) && !empty($partners)): ?>
                                <?php foreach ($partners as $partner): ?>
                                    <div class="col text-center">
                                        <?php if (isset($partner['logo']) && !empty($partner['logo'])): ?>
                                            <a href="<?php echo $this->escape($partner['siteweb']); ?>" target="_blank" title="<?php echo $this->escape($partner['nom']); ?>">
                                                <img src="<?php echo $this->escape($partner['logo']); ?>" alt="<?php echo $this->escape($partner['nom']); ?>" class="img-fluid" style="max-height: 80px;">
                                            </a>
                                        <?php else: ?>
                                            <div class="partner-placeholder p-3 bg-light rounded text-primary">
                                                <a href="<?php echo $this->escape($partner['siteweb']); ?>" target="_blank" class="text-decoration-none">
                                                    <h5 class="mb-0"><?php echo $this->escape($partner['nom']); ?></h5>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <p class="mt-2 mb-0 small fw-bold">
                                            <a href="<?php echo $this->escape($partner['siteweb']); ?>" target="_blank" class="text-decoration-none text-dark">
                                                <?php echo $this->escape($partner['nom']); ?>
                                            </a>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Default partners if not available from database -->
                                <div class="col text-center">
                                    <img src="<?php echo $this->url('assets/images/partners/uca.png'); ?>" alt="Université Cadi Ayyad" class="img-fluid" style="max-height: 80px;">
                                    <p class="mt-2 mb-0 small fw-bold">Université Cadi Ayyad</p>
                                </div>
                                <div class="col text-center">
                                    <img src="<?php echo $this->url('assets/images/partners/cnrst.png'); ?>" alt="CNRST" class="img-fluid" style="max-height: 80px;">
                                    <p class="mt-2 mb-0 small fw-bold">CNRST</p>
                                </div>
                                <div class="col text-center">
                                    <img src="<?php echo $this->url('assets/images/partners/mesrsfc.png'); ?>" alt="Ministère de l'Éducation" class="img-fluid" style="max-height: 80px;">
                                    <p class="mt-2 mb-0 small fw-bold">Ministère de l'Éducation</p>
                                </div>
                                <div class="col text-center">
                                    <img src="<?php echo $this->url('assets/images/partners/amsic.png'); ?>" alt="AMSIC" class="img-fluid" style="max-height: 80px;">
                                    <p class="mt-2 mb-0 small fw-bold">AMSIC</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>