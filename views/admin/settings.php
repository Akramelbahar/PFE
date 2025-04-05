<!-- views/admin/settings.php -->
<div class="admin-settings">
    <h1 class="mb-4">Paramètres du système</h1>

    <div class="row">
        <div class="col">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                        <i class="fas fa-cog"></i> Général
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mail-tab" data-bs-toggle="tab" data-bs-target="#mail" type="button" role="tab" aria-controls="mail" aria-selected="false">
                        <i class="fas fa-envelope"></i> Email
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                        <i class="fas fa-shield-alt"></i> Sécurité
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="paths-tab" data-bs-toggle="tab" data-bs-target="#paths" type="button" role="tab" aria-controls="paths" aria-selected="false">
                        <i class="fas fa-folder"></i> Chemins
                    </button>
                </li>
            </ul>

            <!-- Tab content -->
            <div class="tab-content" id="settingsTabsContent">
                <form action="<?php echo $this->url('admin/settings'); ?>" method="post">
                    <!-- General Tab -->
                    <div class="tab-pane fade show active p-4 bg-white border border-top-0 rounded-bottom" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <h3 class="mb-4">Paramètres généraux</h3>

                        <div class="mb-3 row">
                            <label for="app_name" class="col-sm-3 col-form-label">Nom de l'application</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="app_name" name="app_name" value="<?php echo $this->escape($config['app']['name']); ?>" required>
                                <div class="form-text">Le nom qui sera affiché dans l'interface et les emails.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="app_url" class="col-sm-3 col-form-label">URL de l'application</label>
                            <div class="col-sm-9">
                                <input type="url" class="form-control" id="app_url" name="app_url" value="<?php echo $this->escape($config['app']['url']); ?>" required>
                                <div class="form-text">L'URL de base de votre application (ex: http://exemple.com).</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="timezone" class="col-sm-3 col-form-label">Fuseau horaire</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="timezone" name="timezone">
                                    <?php
                                    $timezones = [
                                        'Africa/Casablanca' => 'Afrique/Casablanca',
                                        'Europe/Paris' => 'Europe/Paris',
                                        'UTC' => 'UTC',
                                        'America/New_York' => 'Amérique/New York',
                                        'Asia/Tokyo' => 'Asie/Tokyo'
                                    ];
                                    foreach ($timezones as $value => $label):
                                        ?>
                                        <option value="<?php echo $value; ?>" <?php echo $config['app']['timezone'] === $value ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Définit le fuseau horaire pour les dates et heures.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="locale" class="col-sm-3 col-form-label">Langue par défaut</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="locale" name="locale">
                                    <option value="fr_FR.UTF-8" <?php echo $config['app']['locale'] === 'fr_FR.UTF-8' ? 'selected' : ''; ?>>Français</option>
                                    <option value="en_US.UTF-8" <?php echo $config['app']['locale'] === 'en_US.UTF-8' ? 'selected' : ''; ?>>English</option>
                                    <option value="ar_MA.UTF-8" <?php echo $config['app']['locale'] === 'ar_MA.UTF-8' ? 'selected' : ''; ?>>العربية</option>
                                </select>
                                <div class="form-text">Langue par défaut de l'application.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="debug" class="col-sm-3 col-form-label">Mode debug</label>
                            <div class="col-sm-9">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="debug" name="debug" <?php echo $config['app']['debug'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="debug">Activer le mode debug</label>
                                </div>
                                <div class="form-text">Le mode debug affiche les erreurs détaillées. À désactiver en production.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="session_lifetime" class="col-sm-3 col-form-label">Durée de session</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" value="<?php echo $config['app']['session_lifetime']; ?>" min="300" required>
                                <div class="form-text">Durée des sessions en secondes (7200 = 2 heures).</div>
                            </div>
                        </div>
                    </div>

                    <!-- Mail Tab -->
                    <div class="tab-pane fade p-4 bg-white border border-top-0 rounded-bottom" id="mail" role="tabpanel" aria-labelledby="mail-tab">
                        <h3 class="mb-4">Paramètres email</h3>

                        <div class="mb-3 row">
                            <label for="from_name" class="col-sm-3 col-form-label">Nom de l'expéditeur</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="from_name" name="from_name" value="<?php echo $this->escape($config['mail']['from_name']); ?>">
                                <div class="form-text">Nom affiché comme expéditeur des emails.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="from_email" class="col-sm-3 col-form-label">Email de l'expéditeur</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" id="from_email" name="from_email" value="<?php echo $this->escape($config['mail']['from_email']); ?>">
                                <div class="form-text">Adresse email utilisée comme expéditeur.</div>
                            </div>
                        </div>

                        <h4 class="mt-4 mb-3">Configuration SMTP</h4>

                        <div class="mb-3 row">
                            <label for="smtp_host" class="col-sm-3 col-form-label">Serveur SMTP</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?php echo $this->escape($config['mail']['smtp_host']); ?>">
                                <div class="form-text">Serveur SMTP pour l'envoi d'emails (ex: smtp.gmail.com).</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="smtp_port" class="col-sm-3 col-form-label">Port SMTP</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo $this->escape($config['mail']['smtp_port']); ?>">
                                <div class="form-text">Port du serveur SMTP (généralement 587, 465 ou 25).</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="smtp_username" class="col-sm-3 col-form-label">Utilisateur SMTP</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="<?php echo $this->escape($config['mail']['smtp_username']); ?>">
                                <div class="form-text">Utilisateur pour l'authentification SMTP.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="smtp_password" class="col-sm-3 col-form-label">Mot de passe SMTP</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="<?php echo $this->escape($config['mail']['smtp_password']); ?>">
                                <div class="form-text">Mot de passe pour l'authentification SMTP.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="smtp_secure" class="col-sm-3 col-form-label">Sécurité SMTP</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="smtp_secure" name="smtp_secure">
                                    <option value="" <?php echo $config['mail']['smtp_secure'] === '' ? 'selected' : ''; ?>>Aucune</option>
                                    <option value="tls" <?php echo $config['mail']['smtp_secure'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                    <option value="ssl" <?php echo $config['mail']['smtp_secure'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                </select>
                                <div class="form-text">Type de sécurité pour la connexion SMTP.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="button" class="btn btn-info" id="testMailBtn">
                                    <i class="fas fa-paper-plane"></i> Tester la configuration email
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade p-4 bg-white border border-top-0 rounded-bottom" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <h3 class="mb-4">Paramètres de sécurité</h3>

                        <div class="mb-3 row">
                            <label for="password_hash_algo" class="col-sm-3 col-form-label">Algorithme de hachage</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="password_hash_algo" name="password_hash_algo">
                                    <option value="<?php echo PASSWORD_DEFAULT; ?>" <?php echo $config['security']['password_hash_algo'] === PASSWORD_DEFAULT ? 'selected' : ''; ?>>Par défaut (recommandé)</option>
                                    <option value="<?php echo PASSWORD_BCRYPT; ?>" <?php echo $config['security']['password_hash_algo'] === PASSWORD_BCRYPT ? 'selected' : ''; ?>>Bcrypt</option>
                                </select>
                                <div class="form-text">Algorithme utilisé pour le hachage des mots de passe.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="token_lifetime" class="col-sm-3 col-form-label">Durée des tokens</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="token_lifetime" name="token_lifetime" value="<?php echo $config['security']['token_lifetime']; ?>" min="300" required>
                                <div class="form-text">Durée de validité des tokens de réinitialisation de mot de passe (en secondes).</div>
                            </div>
                        </div>
                    </div>

                    <!-- Paths Tab -->
                    <div class="tab-pane fade p-4 bg-white border border-top-0 rounded-bottom" id="paths" role="tabpanel" aria-labelledby="paths-tab">
                        <h3 class="mb-4">Chemins d'accès</h3>

                        <div class="mb-3 row">
                            <label for="uploads" class="col-sm-3 col-form-label">Répertoire des uploads</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="uploads" name="uploads" value="<?php echo $this->escape($config['paths']['uploads']); ?>" required>
                                <div class="form-text">Chemin relatif vers le répertoire des fichiers téléchargés.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="templates" class="col-sm-3 col-form-label">Répertoire des templates</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="templates" name="templates" value="<?php echo $this->escape($config['paths']['templates']); ?>" required>
                                <div class="form-text">Chemin relatif vers le répertoire des templates.</div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="logs" class="col-sm-3 col-form-label">Répertoire des logs</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="logs" name="logs" value="<?php echo $this->escape($config['paths']['logs']); ?>" required>
                                <div class="form-text">Chemin relatif vers le répertoire des logs.</div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Assurez-vous que ces répertoires existent et ont les permissions d'écriture appropriées.
                        </div>
                    </div>

                    <!-- Submit Button (Fixed at the Bottom) -->
                    <div class="position-sticky bottom-0 pt-3 pb-3 bg-light border-top mt-4" style="z-index: 100;">
                        <div class="container">
                            <div class="d-flex justify-content-between">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo"></i> Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer les paramètres
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testMailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tester la configuration email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="test_email" class="form-label">Adresse email de test</label>
                    <input type="email" class="form-control" id="test_email" placeholder="votre@email.com" required>
                    <div class="form-text">Un email de test sera envoyé à cette adresse.</div>
                </div>
                <div id="testMailResult"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="sendTestMailBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Envoyer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Keep tab active after form submission
        const activeTab = localStorage.getItem('settingsActiveTab');
        if (activeTab) {
            document.querySelector(activeTab).click();
        }

        // Save active tab
        document.querySelectorAll('#settingsTabs button').forEach(tab => {
            tab.addEventListener('click', function() {
                localStorage.setItem('settingsActiveTab', '#' + this.id);
            });
        });

        // Test email
        document.getElementById('testMailBtn').addEventListener('click', function() {
            $('#testMailModal').modal('show');
        });

        document.getElementById('sendTestMailBtn').addEventListener('click', function() {
            const testEmail = document.getElementById('test_email').value;
            const resultDiv = document.getElementById('testMailResult');
            const spinner = this.querySelector('.spinner-border');

            // Validate email
            if (!testEmail) {
                resultDiv.innerHTML = '<div class="alert alert-danger">Veuillez entrer une adresse email valide.</div>';
                return;
            }

            // Show spinner
            spinner.classList.remove('d-none');
            this.disabled = true;
            resultDiv.innerHTML = '';

            // Get mail config from form
            const mailConfig = {
                from_name: document.getElementById('from_name').value,
                from_email: document.getElementById('from_email').value,
                smtp_host: document.getElementById('smtp_host').value,
                smtp_port: document.getElementById('smtp_port').value,
                smtp_username: document.getElementById('smtp_username').value,
                smtp_password: document.getElementById('smtp_password').value,
                smtp_secure: document.getElementById('smtp_secure').value,
                test_email: testEmail
            };

            // Send AJAX request
            fetch('<?php echo $this->url("admin/test-mail"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(mailConfig)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
                    } else {
                        resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + data.message + '</div>';
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Erreur de connexion: ' + error.message + '</div>';
                })
                .finally(() => {
                    // Hide spinner
                    spinner.classList.add('d-none');
                    this.disabled = false;
                });
        });
    });
</script>