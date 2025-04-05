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
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY dateEnvoi DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in getRecent: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get unread contacts
     * @return array
     */
    public function getUnread() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = 'Non lu' ORDER BY dateEnvoi DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in getUnread: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mark contact as read
     * @param int $id
     * @param int $userId
     * @param string|null $reponse
     * @return bool
     */
    public function markAsRead($id, $userId, $reponse = null) {
        if (!$this->tableExists('ContactReponse')) {
            // Create the table if it doesn't exist
            $this->createContactResponseTable();
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO ContactReponse (contactId, userId, reponse, dateReponse)
                VALUES (:contactId, :userId, :reponse, NOW())
            ");

            return $stmt->execute([
                'contactId' => $id,
                'userId' => $userId,
                'reponse' => $reponse
            ]);
        } catch (PDOException $e) {
            error_log('Error in markAsRead: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a table exists
     * @param string $table
     * @return bool
     */
    private function tableExists($table) {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE '$table'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error in tableExists: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create ContactReponse table
     * @return bool
     */
    public function createContactResponseTable() {
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
            error_log('Error in createContactResponseTable: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get contact responses
     * @param int $contactId
     * @return array
     */
    public function getResponses($contactId) {
        if (!$this->tableExists('ContactReponse')) {
            return [];
        }

        try {
            $stmt = $this->db->prepare("
                SELECT cr.*, u.nom as repondeurNom, u.prenom as repondeurPrenom
                FROM ContactReponse cr
                LEFT JOIN Utilisateur u ON cr.userId = u.id
                WHERE cr.contactId = :contactId
                ORDER BY cr.dateReponse DESC
            ");
            $stmt->execute(['contactId' => $contactId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in getResponses: ' . $e->getMessage());
            return [];
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

        if (!$admins) {
            return false;
        }

        $to = [];
        foreach ($admins as $admin) {
            if (isset($admin['email'])) {
                $to[] = $admin['email'];
            }
        }

        if (empty($to)) {
            return false;
        }

        $subject = 'Nouveau message de contact - ' . htmlspecialchars($contact['nom']);
        $message = "Un nouveau message a été envoyé via le formulaire de contact :\n\n";
        $message .= "Nom : " . htmlspecialchars($contact['nom']) . "\n";
        $message .= "Email : " . htmlspecialchars($contact['email']) . "\n";

        if (isset($contact['telephone']) && !empty($contact['telephone'])) {
            $message .= "Téléphone : " . htmlspecialchars($contact['telephone']) . "\n";
        }

        if (isset($contact['sujet']) && !empty($contact['sujet'])) {
            $message .= "Sujet : " . htmlspecialchars($contact['sujet']) . "\n";
        }

        $message .= "Message : " . htmlspecialchars($contact['message']) . "\n\n";
        $message .= "Date : " . $contact['dateEnvoi'] . "\n";

        // This is where you would implement the actual email sending
        // Example: mail(implode(',', $to), $subject, $message);

        return true;
    }

    /**
     * Add status column to Contact table if it doesn't exist
     * @return bool
     */
    public function ensureStatusColumn() {
        try {
            // Check if status column exists
            $stmt = $this->db->prepare("SHOW COLUMNS FROM {$this->table} LIKE 'status'");
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                // Add status column
                $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN status ENUM('Non lu', 'Lu', 'Répondu') DEFAULT 'Non lu'");

                // Update existing records
                $this->db->exec("UPDATE {$this->table} SET status = 'Non lu'");
            }

            return true;
        } catch (PDOException $e) {
            error_log('Error in ensureStatusColumn: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Add sujet column to Contact table if it doesn't exist
     * @return bool
     */
    public function ensureSujetColumn() {
        try {
            // Check if sujet column exists
            $stmt = $this->db->prepare("SHOW COLUMNS FROM {$this->table} LIKE 'sujet'");
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                // Add sujet column
                $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN sujet VARCHAR(255)");
            }

            return true;
        } catch (PDOException $e) {
            error_log('Error in ensureSujetColumn: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Add telephone column to Contact table if it doesn't exist
     * @return bool
     */
    public function ensureTelephoneColumn() {
        try {
            // Check if telephone column exists
            $stmt = $this->db->prepare("SHOW COLUMNS FROM {$this->table} LIKE 'telephone'");
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                // Add telephone column
                $this->db->exec("ALTER TABLE {$this->table} ADD COLUMN telephone VARCHAR(50)");
            }

            return true;
        } catch (PDOException $e) {
            error_log('Error in ensureTelephoneColumn: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ensure all required columns exist
     * @return bool
     */
    public function ensureTableStructure() {
        return $this->ensureStatusColumn() &&
            $this->ensureSujetColumn() &&
            $this->ensureTelephoneColumn();
    }

    /**
     * Search contacts by criteria
     * @param array $criteria
     * @return array
     */
    public function search($criteria = []) {
        $conditions = [];
        $params = [];

        // Build query conditions
        if (!empty($criteria['search'])) {
            $searchFields = ['nom', 'email', 'sujet', 'message', 'telephone'];
            $searchConditions = [];

            foreach ($searchFields as $field) {
                if ($this->columnExists($this->table, $field)) {
                    $searchConditions[] = "$field LIKE :search";
                }
            }

            if (!empty($searchConditions)) {
                $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
                $params['search'] = '%' . $criteria['search'] . '%';
            }
        }

        if (!empty($criteria['status'])) {
            $conditions[] = 'status = :status';
            $params['status'] = $criteria['status'];
        }

        if (!empty($criteria['date_from'])) {
            $conditions[] = 'dateEnvoi >= :date_from';
            $params['date_from'] = $criteria['date_from'] . ' 00:00:00';
        }

        if (!empty($criteria['date_to'])) {
            $conditions[] = 'dateEnvoi <= :date_to';
            $params['date_to'] = $criteria['date_to'] . ' 23:59:59';
        }

        // Build the final query
        $query = "SELECT * FROM {$this->table}";

        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= ' ORDER BY dateEnvoi DESC';

        try {
            $stmt = $this->db->prepare($query);

            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error in search: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if a column exists in a table
     * @param string $table
     * @param string $column
     * @return bool
     */
    private function columnExists($table, $column) {
        try {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM `$table` LIKE :column");
            $stmt->execute(['column' => $column]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error in columnExists: ' . $e->getMessage());
            return false;
        }
    }
}