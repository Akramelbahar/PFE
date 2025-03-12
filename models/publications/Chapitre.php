<?php
require_once './models/publications/Publication.php';

/**
 * Chapitre Class (inherits from Publication)
 */
class Chapitre extends Publication {
    protected $table = 'Chapitre';
    protected $primaryKey = 'publicationId';

    /**
     * Create chapter from existing publication
     * @param int $publicationId
     * @param int|null $livrePere
     * @return int|false
     */
    public function createFromPublication($publicationId, $livrePere = null) {
        return $this->create([
            'publicationId' => $publicationId,
            'LivrePere' => $livrePere
        ]);
    }

    /**
     * Get all chapters with publication details
     * @return array
     */
    public function getAllWithDetails() {
        $query = "
            SELECT c.*, p.*, l.publicationId as livreId, lp.titre as livreTitre,
                   u.nom as auteurNom, u.prenom as auteurPrenom
            FROM Chapitre c
            JOIN Publication p ON c.publicationId = p.id
            LEFT JOIN Livre l ON c.LivrePere = l.publicationId
            LEFT JOIN Publication lp ON l.publicationId = lp.id
            LEFT JOIN Utilisateur u ON p.auteurId = u.id
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}