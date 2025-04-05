<?php
require_once './models/Model.php';

/**
 * Actualite Class
 */
class Actualite extends Model {
    protected $table = 'Actualite';

    /**
     * Get news with author details
     * @param int $id
     * @return array|false
     */
    public function findWithAuthor($id) {
        $query = "
            SELECT a.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM {$this->table} a
            LEFT JOIN Utilisateur u ON a.auteurId = u.id
            WHERE a.id = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all news with author details
     * @param string $orderBy Optional order clause
     * @return array
     */
    public function getAllWithAuthorDetails($orderBy = 'a.datePublication DESC') {
        $query = "
            SELECT a.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM {$this->table} a
            LEFT JOIN Utilisateur u ON a.auteurId = u.id
            ORDER BY {$orderBy}
        ";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search news by keyword
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $query = "
            SELECT a.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM {$this->table} a
            LEFT JOIN Utilisateur u ON a.auteurId = u.id
            WHERE a.titre LIKE :keyword 
               OR a.contenu LIKE :keyword
            ORDER BY a.datePublication DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get news by author
     * @param int $auteurId
     * @return array
     */
    public function getByAuthor($auteurId) {
        return $this->where('auteurId', $auteurId);
    }

    /**
     * Get news by event
     * @param int $evenementId
     * @return array
     */
    public function getByEvent($evenementId) {
        return $this->where('evenementId', $evenementId);
    }

    /**
     * Get recent news
     * @param int $limit
     * @return array
     */
    public function getRecent($limit = 5) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.nom as auteurNom, u.prenom as auteurPrenom
            FROM {$this->table} a
            LEFT JOIN Utilisateur u ON a.auteurId = u.id
            ORDER BY a.datePublication DESC 
            LIMIT :limit
        ");

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}