<!-- Add this to views/publications/view.php -->
<!-- Publication Status Section -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Statut de la publication</h5>
    </div>
    <div class="card-body">
        <?php
        $statusBadgeClass = 'bg-secondary';
        $statusLabel = '';

        switch($publication['status']) {
            case 'draft':
                $statusBadgeClass = 'bg-secondary';
                $statusLabel = 'Brouillon';
                break;
            case 'submitted':
                $statusBadgeClass = 'bg-info';
                $statusLabel = 'Soumise pour approbation';
                break;
            case 'under_review':
                $statusBadgeClass = 'bg-warning text-dark';
                $statusLabel = 'En cours de révision';
                break;
            case 'approved':
                $statusBadgeClass = 'bg-success';
                $statusLabel = 'Approuvée';
                break;
            case 'rejected':
                $statusBadgeClass = 'bg-danger';
                $statusLabel = 'Rejetée';
                break;
            case 'published':
                $statusBadgeClass = 'bg-primary';
                $statusLabel = 'Publiée';
                break;
        }
        ?>

        <div class="d-flex align-items-center mb-3">
            <span class="badge <?php echo $statusBadgeClass; ?> fs-6 me-2"><?php echo $statusLabel; ?></span>

            <?php if ($publication['status'] === 'draft' && $publication['auteurId'] == $auth->getUser()['id']): ?>
                <form action="<?php echo $this->url('publications/submit/' . $publication['id']); ?>" method="post" class="ms-2">
                    <?php echo CSRF::tokenField(); ?>
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Soumettre pour approbation
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (!empty($publication['commentairesRevision'])): ?>
            <div class="alert alert-light">
                <h6>Commentaires de l'évaluateur</h6>
                <p><?php echo nl2br($this->escape($publication['commentairesRevision'])); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($publication['reviewerId']) && isset($publication['dateRevision'])): ?>
            <div class="small text-muted">
                <?php if (isset($reviewer)): ?>
                    Révisé par <?php echo $this->escape($reviewer['prenom'] . ' ' . $reviewer['nom']); ?>
                <?php endif; ?>
                le <?php echo $this->formatDate($publication['dateRevision']); ?>
            </div>
        <?php endif; ?>
    </div>
</div>