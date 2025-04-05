<?php
require_once './models/publications/Publication.php';

/**
 * Article Class (inherits from Publication)
 */
class Article extends Publication {
    protected $table = 'Article';
    protected $primaryKey = 'publicationId';

    /**
     * Create article from existing publication
     * @param int $publicationId
     * @return int|false
     */
    public function createFromPublication($publicationId) {
        return $this->create(['publicationId' => $publicationId]);
    }

    /**
     * Get all articles with publication details
     * @return array
     */
    public function getAllWithDetails() {
        $query = "
            SELECT a.*, p.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM Article a
            JOIN Publication p ON a.publicationId = p.id
            LEFT JOIN Utilisateur u ON p.auteurId = u.id
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}