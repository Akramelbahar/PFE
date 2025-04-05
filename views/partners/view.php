<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">
                        <i class="fas fa-handshake"></i> Détails du Partenaire
                    </h2>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <?php if (!empty($partner['logo'])): ?>
                            <img src="<?php echo $this->escape($partner['logo']); ?>"
                                 alt="Logo <?php echo $this->escape($partner['nom']); ?>"
                                 class="img-fluid mb-3"
                                 style="max-height: 200px;">
                        <?php else: ?>
                            <div class="alert alert-secondary">
                                Aucun logo disponible
                            </div>
                        <?php endif; ?>
                    </div>

                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th class="w-25">Nom</th>
                            <td><?php echo $this->escape($partner['nom']); ?></td>
                        </tr>
                        <tr>
                            <th>Site Web</th>
                            <td>
                                <a href="<?php echo $this->escape($partner['siteweb']); ?>"
                                   target="_blank"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i>
                                    <?php echo $this->escape($partner['siteweb']); ?>
                                </a>
                            </td>
                        </tr>
                        <?php if (!empty($partner['contact'])): ?>
                            <tr>
                                <th>Contact</th>
                                <td><?php echo $this->escape($partner['contact']); ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo $this->url('partners'); ?>" class="btn btn-secondary me-md-2">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                        <?php if ($auth->hasRole('admin')): ?>
                            <a href="<?php echo $this->url('partners/edit/' . $partner['id']); ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>