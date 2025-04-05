<?php
/**
 * Partner Controller
 * Handles CRUD operations for partners
 */
class PartnerController extends Controller {
    private $partnerModel;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->partnerModel = new Partner();
    }

    /**
     * List all partners
     */
    public function index() {
        // Require admin authentication
        $this->requireAuth('admin');

        // Get all partners
        $partners = $this->partnerModel->getAll();

        // Render view
        $this->render('partners/index', [
            'pageTitle' => 'Nos Partenaires',
            'partners' => $partners
        ]);
    }

    /**
     * Show partner creation form
     */
    public function create() {
        // Require admin authentication
        $this->requireAuth('admin');

        // Render view
        $this->render('partners/create', [
            'pageTitle' => 'Ajouter un Partenaire'
        ]);
    }

    /**
     * Store new partner
     */
    public function store() {
        // Require admin authentication
        $this->requireAuth('admin');

        // Check if it's a POST request
        if (!$this->isPost()) {
            $this->redirect('partners/create');
            return;
        }

        // Get input data
        $data = $this->getInput();

        // Validate input
        $validationRules = [
            'nom' => 'required|min:2|max:255',
            'siteweb' => 'required|min:5',
            'contact' => 'min:5|max:255',
            'logo' => 'min:5|max:255'
        ];

        $validationResult = $this->validate($data, $validationRules);

        if ($validationResult === true) {
            // Attempt to create partner
            $partnerId = $this->partnerModel->create($data);

            if ($partnerId) {
                // Success
                $this->setFlash('success', 'Partenaire ajouté avec succès.');
                $this->redirect('partners');
            } else {
                // Failure
                $this->setFlash('error', 'Erreur lors de l\'ajout du partenaire.');
                $this->render('partners/create', [
                    'pageTitle' => 'Ajouter un Partenaire',
                    'data' => $data
                ]);
            }
        } else {
            // Validation failed
            $this->render('partners/create', [
                'pageTitle' => 'Ajouter un Partenaire',
                'errors' => $validationResult,
                'data' => $data
            ]);
        }
    }

    /**
     * Show partner details
     * @param int $id Partner ID
     */
    public function view($id) {
        // Attempt to find partner
        $partner = $this->partnerModel->find($id);

        if (!$partner) {
            $this->renderNotFound();
            return;
        }

        // Render view
        $this->render('partners/view', [
            'pageTitle' => 'Détails du Partenaire',
            'partner' => $partner
        ]);
    }

    /**
     * Show partner edit form
     * @param int $id Partner ID
     */
    public function edit($id) {
        // Require admin authentication
        $this->requireAuth('admin');

        // Attempt to find partner
        $partner = $this->partnerModel->find($id);

        if (!$partner) {
            $this->renderNotFound();
            return;
        }

        // Render view
        $this->render('partners/edit', [
            'pageTitle' => 'Modifier le Partenaire',
            'partner' => $partner
        ]);
    }

    /**
     * Update partner
     * @param int $id Partner ID
     */
    public function update($id) {
        // Require admin authentication
        $this->requireAuth('admin');

        // Check if it's a POST request
        if (!$this->isPost()) {
            $this->redirect('partners/edit/' . $id);
            return;
        }

        // Get input data
        $data = $this->getInput();

        // Validate input
        $validationRules = [
            'nom' => 'required|min:2|max:255',
            'siteweb' => 'required|min:5',
            'contact' => 'min:5|max:255',
            'logo' => 'min:5|max:255'
        ];

        $validationResult = $this->validate($data, $validationRules);

        if ($validationResult === true) {
            // Attempt to update partner
            $updateResult = $this->partnerModel->update($id, $data);

            if ($updateResult) {
                // Success
                $this->setFlash('success', 'Partenaire mis à jour avec succès.');
                $this->redirect('partners');
            } else {
                // Failure
                $this->setFlash('error', 'Erreur lors de la mise à jour du partenaire.');
                $this->render('partners/edit', [
                    'pageTitle' => 'Modifier le Partenaire',
                    'partner' => $data
                ]);
            }
        } else {
            // Validation failed
            $this->render('partners/edit', [
                'pageTitle' => 'Modifier le Partenaire',
                'errors' => $validationResult,
                'partner' => $data
            ]);
        }
    }

    /**
     * Delete partner
     * @param int $id Partner ID
     */
    public function delete($id) {
        // Require admin authentication
        $this->requireAuth('admin');

        // Check if it's a POST request
        if (!$this->isPost()) {
            $this->redirect('partners');
            return;
        }

        // Attempt to delete partner
        $deleteResult = $this->partnerModel->delete($id);

        if ($deleteResult) {
            // Success
            $this->setFlash('success', 'Partenaire supprimé avec succès.');
        } else {
            // Failure
            $this->setFlash('error', 'Erreur lors de la suppression du partenaire.');
        }

        $this->redirect('partners');
    }
}