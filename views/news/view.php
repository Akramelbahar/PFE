
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Message de contact</h1>
            <div>
                <a href="<?= BASE_URL ?>/admin/contacts" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Retour à la liste
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Supprimer
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

        <div class="row">
            <div class="col-md-8">
                <!-- Message details -->
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                        <h5 class="mb-0">Informations du message</h5>
                        <span class="badge <?= $contact['status'] === 'Non lu' ? 'bg-danger' : ($contact['status'] === 'Lu' ? 'bg-warning' : 'bg-success') ?>">
                        <?= htmlspecialchars($contact['status']) ?>
                    </span>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h4 class="h5 mb-3"><?= htmlspecialchars($contact['sujet'] ?? 'Sans sujet') ?></h4>
                            <div class="d-flex flex-wrap mb-3">
                                <div class="me-4 mb-2">
                                    <strong>Expéditeur:</strong><br>
                                    <?= htmlspecialchars($contact['nom']) ?><br>
                                    <a href="mailto:<?= htmlspecialchars($contact['email']) ?>"><?= htmlspecialchars($contact['email']) ?></a>
                                    <?php if (isset($contact['telephone']) && !empty($contact['telephone'])): ?>
                                        <br>
                                        <a href="tel:<?= htmlspecialchars($contact['telephone']) ?>"><?= htmlspecialchars($contact['telephone']) ?></a>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <strong>Date d'envoi:</strong><br>
                                    <?= date('d/m/Y H:i', strtotime($contact['dateEnvoi'])) ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Message:</strong>
                                <div class="p-3 bg-light rounded mt-2">
                                    <?= nl2br(htmlspecialchars($contact['message'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reply form -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Répondre</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?= BASE_URL ?>/admin/contacts/<?= $contact['id'] ?>/reply" method="post">
                            <div class="mb-3">
                                <label for="reponse" class="form-label">Message</label>
                                <textarea class="form-control" id="reponse" name="reponse" rows="6" required></textarea>
                                <div class="form-text">
                                    Cette réponse sera envoyée à <?= htmlspecialchars($contact['email']) ?>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Previous responses -->
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Historique des réponses</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($responses)): ?>
                            <p class="text-muted">Aucune réponse n'a été envoyée.</p>
                        <?php else: ?>
                            <?php foreach ($responses as $response): ?>
                                <div class="border-bottom pb-3 mb-3 <?= $loop->last ? 'border-0 pb-0 mb-0' : '' ?>">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <strong><?= htmlspecialchars($response['repondeurPrenom'] . ' ' . $response['repondeurNom']) ?></strong>
                                        </div>
                                        <div class="text-muted small">
                                            <?= date('d/m/Y H:i', strtotime($response['dateReponse'])) ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($response['reponse'])): ?>
                                        <div>
                                            <?= nl2br(htmlspecialchars($response['reponse'])) ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted fst-italic">
                                            <small>Message marqué comme lu (sans réponse)</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce message ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="<?= BASE_URL ?>/admin/contacts/<?= $contact['id'] ?>/delete" method="post">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
