<div class="card shadow h-100">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Messages récents</h5>
        <a href="<?= $this->url('admin/contacts') ?>" class="btn btn-sm btn-primary">
            Tous les messages
            <?php if ($unreadCount > 0): ?>
                <span class="badge bg-danger ms-1"><?= $unreadCount ?></span>
            <?php endif; ?>
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentContacts)): ?>
            <div class="p-3 text-center text-muted">
                Aucun message de contact récent.
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($recentContacts as $contact): ?>
                    <a href="<?= $this->url('admin/contacts/' . $contact['id']) ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                <?= $this->escape($contact['nom']) ?>
                                <?php if ($contact['status'] === 'Non lu'): ?>
                                    <span class="badge bg-danger">Nouveau</span>
                                <?php endif; ?>
                            </h6>
                            <small class="text-muted"><?= $this->formatDate($contact['dateEnvoi'], 'd/m/Y') ?></small>
                        </div>
                        <p class="mb-1 text-truncate"><?= $this->escape($contact['sujet'] ?? 'Sans sujet') ?></p>
                        <small class="text-muted">
                            <?= $this->truncate($this->escape($contact['message']), 70) ?>
                        </small>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>