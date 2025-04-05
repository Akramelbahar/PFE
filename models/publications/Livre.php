<?php
require_once './models/publications/Publication.php';

/**
 * Livre Class (inherits from Publication)
 */
class Livre extends Publication {
    protected $table = 'Livre';
    protected $primaryKey = 'publicationId';

    /**
     * Create book from existing publication
     * @param int $publicationId
     * @return int|false
     */
    public function createFromPublication($publicationId) {
        return $this->create(['publicationId' => $publicationId]);
    }

    /**
     * Get all books with publication details
     * @return array
     */
    public function getAllWithDetails() {
        $query = "
            SELECT l.*, p.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM Livre l
            JOIN Publication p ON l.publicationId = p.id
            LEFT JOIN Utilisateur u ON p.auteurId = u.id
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all chapters of this book
     * @param int $livreId
     * @return array
     */
    public function getChapters($livreId) {
        $query = "
            SELECT c.*, p.*
            FROM Chapitre c
            JOIN Publication p ON c.publicationId = p.id
            WHERE c.LivrePere = :livreId
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['livreId' => $livreId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}