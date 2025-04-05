<?php
require_once './models/Model.php';

/**
 * Publication Base Class
 */
class Publication extends Model {
    protected $table = 'Publication';

    /**
     * Get a publication with author details
     * @param int $id
     * @return array|false
     */
    public function findWithAuthor($id) {
        $query = "
            SELECT p.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM Publication p
            LEFT JOIN Utilisateur u ON p.auteurId = u.id
            WHERE p.id = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get publications related to a specific project
     * @param int $projetId
     * @return array
     */
    public function getByProject($projetId) {
        return $this->where('projetId', $projetId);
    }

    /**
     * Get publications related to a specific event
     * @param int $evenementId
     * @return array
     */
    public function getByEvent($evenementId) {
        return $this->where('evenementId', $evenementId);
    }

    /**
     * Get publications by author
     * @param int $auteurId
     * @return array
     */
    public function getByAuthor($auteurId) {
        return $this->where('auteurId', $auteurId);
    }

    /**
     * Get all publications with their specific types
     * @return array
     */
    public function getAllWithTypes() {
        $query = "
            SELECT p.*, 
                CASE 
                    WHEN a.publicationId IS NOT NULL THEN 'Article' 
                    WHEN l.publicationId IS NOT NULL THEN 'Livre'
                    WHEN c.publicationId IS NOT NULL THEN 'Chapitre'
                    ELSE 'Standard'
                END as type
            FROM Publication p
            LEFT JOIN Article a ON p.id = a.publicationId
            LEFT JOIN Livre l ON p.id = l.publicationId
            LEFT JOIN Chapitre c ON p.id = c.publicationId
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}