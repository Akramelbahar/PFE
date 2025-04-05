<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-handshake"></i> Nos Partenaires
                <a href="<?php echo $this->url('partners/create'); ?>" class="btn btn-primary float-end">
                    <i class="fas fa-plus"></i> Ajouter un Partenaire
                </a>
            </h1>

            <?php if (empty($partners)): ?>
                <div class="alert alert-info">
                    Aucun partenaire n'a été trouvé.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Site Web</th>
                            <th>Contact</th>
                            <th>Logo</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($partners as $partner): ?>
                            <tr>
                                <td><?php echo $partner['id']; ?></td>
                                <td><?php echo $this->escape($partner['nom']); ?></td>
                                <td>
                                    <a href="<?php echo $this->escape($partner['siteweb']); ?>" target="_blank">
                                        <?php echo $this->escape($partner['siteweb']); ?>
                                    </a>
                                </td>
                                <td><?php echo $this->escape($partner['contact'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php if (!empty($partner['logo'])): ?>
                                        <img src="<?php echo $this->escape($partner['logo']); ?>"
                                             alt="Logo <?php echo $this->escape($partner['nom']); ?>"
                                             style="max-width: 100px; max-height: 50px;">
                                    <?php else: ?>
                                        Pas de logo
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo $this->url('partners/' . $partner['id']); ?>"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo $this->url('partners/edit/' . $partner['id']); ?>"
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo $this->url('partners/delete/' . $partner['id']); ?>"
                                              method="post" class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce partenaire ?');">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>