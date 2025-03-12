<?php
require_once './models/Model.php';

/**
 * Contact Class
 */
class Contact extends Model {
    protected $table = 'Contact';

    /**
     * Get recent contacts
     * @param int $limit
     * @return array
     */
    public function getRecent($limit = 10) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY dateEnvoi DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get unread contacts
     * @return array
     */
    public function getUnread() {
        $query = "
            SELECT c.*
            FROM Contact c
            LEFT JOIN ContactReponse cr ON c.id = cr.contactId
            WHERE cr.id IS NULL
            ORDER BY c.dateEnvoi DESC
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark contact as read
     * @param int $id
     * @param int $userId
     * @param string|null $reponse
     * @return bool
     */
    public function markAsRead($id, $userId, $reponse = null) {
        if (!$this->getContactResponseTable()) {
            // Create the table if it doesn't exist
            $this->createContactResponseTable();
        }

        $stmt = $this->db->prepare("
            INSERT INTO ContactReponse (contactId, userId, reponse, dateReponse)
            VALUES (:contactId, :userId, :reponse, NOW())
        ");

        return $stmt->execute([
            'contactId' => $id,
            'userId' => $userId,
            'reponse' => $reponse
        ]);
    }

    /**
     * Check if ContactReponse table exists
     * @return bool
     */
    private function getContactResponseTable() {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'ContactReponse'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create ContactReponse table
     * @return bool
     */
    private function createContactResponseTable() {
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS ContactReponse (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    contactId INT NOT NULL,
                    userId INT NOT NULL,
                    reponse TEXT,
                    dateReponse DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (contactId) REFERENCES Contact(id) ON DELETE CASCADE,
                    FOREIGN KEY (userId) REFERENCES Utilisateur(id) ON DELETE CASCADE
                )
            ");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Send email notification for new contact
     * @param array $contact
     * @return bool
     */
    public function sendNotification($contact) {
        // This is a placeholder for email functionality
        // In a real application, you would use a proper email library

        // Get admin emails
        $adminModel = new Admin();
        $admins = $adminModel->getAllWithUserDetails();

        $to = [];
        foreach ($admins as $admin) {
            $to[] = $admin['email'];
        }

        $subject = 'Nouveau message de contact - ' . $contact['nom'];
        $message = "Un nouveau message a été envoyé via le formulaire de contact :\n\n";
        $message .= "Nom : " . $contact['nom'] . "\n";
        $message .= "Email : " . $contact['email'] . "\n";
        $message .= "Message : " . $contact['message'] . "\n\n";
        $message .= "Date : " . $contact['dateEnvoi'] . "\n";

        // This is where you would implement the actual email sending
        // Example: mail(implode(',', $to), $subject, $message);

        return true;
    }
}