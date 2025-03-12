<?php
require_once './core/Controller.php';
require_once './models/Contact.php';
require_once './models/users/Admin.php';

/**
 * Contact Controller
 * Manages contact form submissions and responses
 */
class ContactController extends Controller {
    /**
     * Contact form page
     */
    public function index() {
        $this->render('contact/index', [
            'pageTitle' => 'Contact'
        ]);
    }

    /**
     * Process contact form
     */
    public function send() {
        if (!$this->isPost()) {
            $this->redirect('contact');
            return;
        }

        // Get form data
        $nom = $this->getInput('nom');
        $email = $this->getInput('email');
        $telephone = $this->getInput('telephone');
        $sujet = $this->getInput('sujet');
        $message = $this->getInput('message');

        // Validate input
        $validation = $this->validate(
            [
                'nom' => $nom,
                'email' => $email,
                'sujet' => $sujet,
                'message' => $message
            ],
            [
                'nom' => 'required|max:255',
                'email' => 'required|email',
                'sujet' => 'required|max:255',
                'message' => 'required'
            ]
        );

        if ($validation !== true) {
            $this->render('contact/index', [
                'pageTitle' => 'Contact',
                'errors' => $validation,
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'sujet' => $sujet,
                'message' => $message
            ]);
            return;
        }

        // Store contact message
        $contactModel = new Contact();
        $contactId = $contactModel->create([
            'nom' => $nom,
            'email' => $email,
            'telephone' => $telephone,
            'sujet' => $sujet,
            'message' => $message,
            'dateEnvoi' => date('Y-m-d H:i:s'),
            'status' => 'Non lu'
        ]);

        if ($contactId) {
            // Send notification to admins
            $contact = $contactModel->find($contactId);
            $contactModel->sendNotification($contact);

            $this->setFlash('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.');
        }

        $this->redirect('contact');
    }

    /**
     * List contact messages (admin only)
     */
    public function list() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get filter parameters
        $status = $this->getInput('status');
        $search = $this->getInput('search');
        $dateFrom = $this->getInput('date_from');
        $dateTo = $this->getInput('date_to');

        // Get contacts
        $contactModel = new Contact();
        $contacts = $this->getFilteredContacts($status, $search, $dateFrom, $dateTo);

        $this->render('contact/list', [
            'pageTitle' => 'Messages de contact',
            'contacts' => $contacts,
            'filters' => [
                'status' => $status,
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        ]);
    }

    /**
     * Get filtered contacts
     * @param string|null $status
     * @param string|null $search
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    private function getFilteredContacts($status = null, $search = null, $dateFrom = null, $dateTo = null) {
        $db = Db::getInstance();

        $params = [];
        $conditions = [];

        $query = "SELECT * FROM Contact";

        // Add filters
        if ($status) {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }

        if ($search) {
            $conditions[] = "(nom LIKE :search OR email LIKE :search OR sujet LIKE :search OR message LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($dateFrom) {
            $conditions[] = "dateEnvoi >= :dateFrom";
            $params['dateFrom'] = $dateFrom . ' 00:00:00';
        }

        if ($dateTo) {
            $conditions[] = "dateEnvoi <= :dateTo";
            $params['dateTo'] = $dateTo . ' 23:59:59';
        }

        // Add WHERE clause if there are conditions
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Add ORDER BY
        $query .= " ORDER BY dateEnvoi DESC";

        $stmt = $db->prepare($query);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * View contact message (admin only)
     * @param int $id Contact ID
     */
    public function view($id) {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        $contactModel = new Contact();
        $contact = $contactModel->find($id);

        if (!$contact) {
            $this->renderNotFound();
            return;
        }

        // Mark as read if not already
        if ($contact['status'] === 'Non lu') {
            $contactModel->update($id, ['status' => 'Lu']);
            $contactModel->markAsRead($id, $this->auth->getUser()['id']);
        }

        // Get response if exists
        $db = Db::getInstance();
        $stmt = $db->prepare("
            SELECT cr.*, u.nom as repondeurNom, u.prenom as repondeurPrenom
            FROM ContactReponse cr
            LEFT JOIN Utilisateur u ON cr.userId = u.id
            WHERE cr.contactId = :contactId
            ORDER BY cr.dateReponse DESC
        ");
        $stmt->execute(['contactId' => $id]);
        $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->render('contact/view', [
            'pageTitle' => 'Message: ' . $contact['sujet'],
            'contact' => $contact,
            'responses' => $responses
        ]);
    }

    /**
     * Reply to contact message (admin only)
     * @param int $id Contact ID
     */
    public function reply($id) {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('admin/contacts/' . $id);
            return;
        }

        $contactModel = new Contact();
        $contact = $contactModel->find($id);

        if (!$contact) {
            $this->renderNotFound();
            return;
        }

        // Get reply message
        $reponse = $this->getInput('reponse');

        // Validate input
        if (empty($reponse)) {
            $this->setFlash('error', 'Le message de réponse ne peut pas être vide.');
            $this->redirect('admin/contacts/' . $id);
            return;
        }

        // Store reply
        $contactModel->markAsRead($id, $this->auth->getUser()['id'], $reponse);
        $contactModel->update($id, ['status' => 'Répondu']);

        // In a real application, send the response email here
        // Example code:
        // mail($contact['email'], 'Re: ' . $contact['sujet'], $reponse);

        $this->setFlash('success', 'Votre réponse a été envoyée avec succès.');
        $this->redirect('admin/contacts');
    }

    /**
     * Delete contact message (admin only)
     * @param int $id Contact ID
     */
    public function delete($id) {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        $contactModel = new Contact();
        $contact = $contactModel->find($id);

        if (!$contact) {
            $this->renderNotFound();
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('admin/contacts/' . $id);
            return;
        }

        // First delete any responses
        $db = Db::getInstance();
        $stmt = $db->prepare("DELETE FROM ContactReponse WHERE contactId = :id");
        $stmt->execute(['id' => $id]);

        // Then delete the contact message
        $deleted = $contactModel->delete($id);

        if ($deleted) {
            $this->setFlash('success', 'Le message a été supprimé avec succès.');
            $this->redirect('admin/contacts');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression du message.');
            $this->redirect('admin/contacts/' . $id);
        }
    }

    /**
     * Bulk delete contact messages (admin only)
     */
    public function bulkDelete() {
        // Ensure user is admin
        if (!$this->requireAuth('admin')) {
            return;
        }

        if (!$this->isPost()) {
            $this->redirect('admin/contacts');
            return;
        }

        // Get IDs to delete
        $ids = $this->getInput('ids', []);

        if (empty($ids)) {
            $this->setFlash('error', 'Aucun message sélectionné.');
            $this->redirect('admin/contacts');
            return;
        }

        // Delete messages
        $db = Db::getInstance();

        // First delete responses
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("DELETE FROM ContactReponse WHERE contactId IN ($placeholders)");
        $stmt->execute($ids);

        // Then delete contacts
        $stmt = $db->prepare("DELETE FROM Contact WHERE id IN ($placeholders)");
        $deleted = $stmt->execute($ids);

        if ($deleted) {
            $this->setFlash('success', count($ids) . ' message(s) ont été supprimés avec succès.');
        } else {
            $this->setFlash('error', 'Une erreur est survenue lors de la suppression des messages.');
        }

        $this->redirect('admin/contacts');
    }
}