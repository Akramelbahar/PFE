<?php
require_once './core/Controller.php';
require_once './models/users/MembreBureauExecutif.php';
require_once './models/users/Chercheur.php';

/**
 * MembreBureauExecutif Controller
 */
class MembreBureauExecutifController extends Controller {
    /**
     * Display list of bureau members
     */
    public function index() {
        // Ensure user has permission
        if (!$this->requireAuth(['admin', 'membreBureauExecutif'])) {
            return;
        }

        $membreModel = new MembreBureauExecutif();
        $bureauMembers = $membreModel->getAllWithUserDetails();

        $this->render('bureau/index', [
            'pageTitle' => 'Membres du Bureau Exécutif',
            'bureauMembers' => $bureauMembers
        ]);
    }

    /**
     * Display form to create a new bureau member
     */
    public function create() {
        // Ensure user has permission
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get all eligible users who aren't already bureau members
        $userModel = new Utilisateur();
        $eligibleUsers = $userModel->getEligibleForBureau();

        $this->render('bureau/create', [
            'pageTitle' => 'Ajouter un Membre au Bureau Exécutif',
            'eligibleUsers' => $eligibleUsers
        ]);
    }

    /**
     * Process form to add a user as bureau member
     */
    public function store() {
        // Ensure user has permission
        if (!$this->requireAuth('admin')) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('admin/bureau');
            return;
        }

        // Get form data
        $userId = $this->getInput('user_id');
        $role = $this->getInput('role');
        $mandat = $this->getInput('mandat');
        $permissions = $this->getInput('permissions', []);
        $isChercheur = $this->getInput('is_chercheur') === 'on';

        // Validate input
        $validation = $this->validate(
            [
                'user_id' => $userId,
                'role' => $role,
                'mandat' => $mandat
            ],
            [
                'user_id' => 'required|numeric',
                'role' => 'required',
                'mandat' => 'numeric'
            ]
        );

        if ($validation !== true) {
            $this->setFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            $this->redirect('admin/bureau/create');
            return;
        }

        // Convert permissions array to comma-separated string
        $permissionsStr = implode(',', $permissions);

        // Check if user is already a chercheur
        $chercheurModel = new Chercheur();
        $chercheur = $chercheurModel->findWithDetails($userId);
        $chercheurId = null;

        if ($isChercheur) {
            if (!$chercheur) {
                // Create chercheur entry if doesn't exist
                $domaineRecherche = $this->getInput('domaine_recherche');
                $bio = $this->getInput('bio');

                $chercheurId = $chercheurModel->createFromUser($userId, $domaineRecherche, $bio);
            } else {
                $chercheurId = $userId;
            }
        }

        // Create bureau member
        $membreModel = new MembreBureauExecutif();
        $created = $membreModel->createFromUser($userId, $role, $mandat, $permissionsStr, $chercheurId);

        if ($created) {
            $this->setFlash('success', 'Le membre a été ajouté au bureau exécutif avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de l\'ajout du membre.');
        }

        $this->redirect('admin/bureau');
    }

    /**
     * Display form to edit a bureau member
     * @param int $id User ID
     */
    public function edit($id) {
        // Ensure user has permission
        if (!$this->requireAuth('admin')) {
            return;
        }

        $membreModel = new MembreBureauExecutif();
        $member = $membreModel->findWithDetails($id);

        if (!$member) {
            $this->renderNotFound();
            return;
        }

        // Get all permissions
        $permissions = PermissionsHelper::getAllPermissions();
        $permissionCategories = PermissionsHelper::getPermissionCategories();

        $this->render('bureau/edit', [
            'pageTitle' => 'Modifier le Membre du Bureau',
            'member' => $member,
            'permissions' => $permissions,
            'permissionCategories' => $permissionCategories
        ]);
    }

    /**
     * Update bureau member
     * @param int $id User ID
     */
    public function update($id) {
        // Ensure user has permission
        if (!$this->requireAuth('admin')) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('admin/bureau/edit/' . $id);
            return;
        }

        $membreModel = new MembreBureauExecutif();
        $member = $membreModel->findWithDetails($id);

        if (!$member) {
            $this->renderNotFound();
            return;
        }

        // Get form data
        $role = $this->getInput('role');
        $mandat = $this->getInput('mandat');
        $permissions = $this->getInput('permissions', []);
        $isChercheur = $this->getInput('is_chercheur') === 'on';

        // Convert permissions array to comma-separated string
        $permissionsStr = implode(',', $permissions);

        // Handle chercheur status
        $chercheurId = null;
        if ($isChercheur) {
            $chercheurModel = new Chercheur();
            $chercheur = $chercheurModel->findWithDetails($id);

            if (!$chercheur) {
                // Create chercheur entry if doesn't exist
                $domaineRecherche = $this->getInput('domaine_recherche');
                $bio = $this->getInput('bio');

                $chercheurId = $chercheurModel->createFromUser($id, $domaineRecherche, $bio);
            } else {
                $chercheurId = $id;

                // Update chercheur details
                $domaineRecherche = $this->getInput('domaine_recherche');
                $bio = $this->getInput('bio');

                $chercheurModel->update($id, [
                    'domaineRecherche' => $domaineRecherche,
                    'bio' => $bio
                ]);
            }
        } else if ($member['chercheurId'] !== null) {
            // If user was a chercheur but no longer is, keep the chercheur record
            // but disconnect it from the bureau role
            $chercheurId = null;
        }

        // Update bureau member
        $updated = $membreModel->update($id, [
            'role' => $role,
            'Mandat' => $mandat,
            'permissions' => $permissionsStr,
            'chercheurId' => $chercheurId
        ]);

        if ($updated) {
            $this->setFlash('success', 'Le membre du bureau a été mis à jour avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la mise à jour.');
        }

        $this->redirect('admin/bureau');
    }

    /**
     * Remove a member from the bureau
     * @param int $id User ID
     */
    public function delete($id) {
        // Ensure user has permission
        if (!$this->requireAuth('admin')) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('admin/bureau');
            return;
        }

        $membreModel = new MembreBureauExecutif();
        $member = $membreModel->find($id);

        if (!$member) {
            $this->renderNotFound();
            return;
        }

        // Delete bureau member (but keep the chercheur record if it exists)
        $deleted = $membreModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Le membre a été retiré du bureau exécutif avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression.');
        }

        $this->redirect('admin/bureau');
    }
}