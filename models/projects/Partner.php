<?php
require_once './models/Model.php';

/**
 * Partner Model
 * Handles operations related to partner organizations
 */
class Partner extends Model {
    /**
     * Get all partners
     * @return array List of partners
     */
    public function getAll() {
        try {
            $query = "SELECT * FROM Partner ORDER BY nom ASC";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Return empty array if table doesn't exist or query fails
            return [];
        }
    }

    /**
     * Get a specific partner by ID
     * @param int $id Partner ID
     * @return array|bool Partner data or false if not found
     */
    public function find($id) {
        try {
            $query = "SELECT * FROM Partner WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Create a new partner
     * @param array $data Partner data (nom, logo, siteweb, contact)
     * @return int|bool New partner ID or false on failure
     */
    public function create($data) {
        try {
            $query = "INSERT INTO Partner (nom, logo, siteweb, contact) 
                      VALUES (:nom, :logo, :siteweb, :contact)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':logo', $data['logo']);
            $stmt->bindParam(':siteweb', $data['siteweb']);
            $stmt->bindParam(':contact', $data['contact']);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update a partner
     * @param int $id Partner ID
     * @param array $data Partner data to update
     * @return bool Success status
     */
    public function update($id, $data) {
        try {
            $query = "UPDATE Partner SET 
                      nom = :nom, 
                      logo = :logo, 
                      siteweb = :siteweb, 
                      contact = :contact 
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':logo', $data['logo']);
            $stmt->bindParam(':siteweb', $data['siteweb']);
            $stmt->bindParam(':contact', $data['contact']);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete a partner
     * @param int $id Partner ID
     * @return bool Success status
     */
    public function delete($id) {
        try {
            $query = "DELETE FROM Partner WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get featured partners
     * @param int $limit Number of partners to return
     * @return array List of featured partners
     */
    public function getFeatured($limit = 4) {
        try {
            $query = "SELECT * FROM Partner WHERE featured = 1 ORDER BY nom ASC LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Return some default partners if the table doesn't exist
            return $this->getDefaultPartners($limit);
        }
    }

    /**
     * Get default partners when database table doesn't exist
     * @param int $limit Number of partners to return
     * @return array Default partners
     */
    private function getDefaultPartners($limit = 4) {
        $partners = [
            [
                'id' => 1,
                'nom' => 'Université Cadi Ayyad',
                'logo' => 'assets/images/partners/uca.png',
                'siteweb' => 'https://www.uca.ma',
                'contact' => 'Université publique marocaine'
            ],
            [
                'id' => 2,
                'nom' => 'CNRST',
                'logo' => 'assets/images/partners/cnrst.png',
                'siteweb' => 'https://www.cnrst.ma',
                'contact' => 'Centre National pour la Recherche Scientifique et Technique'
            ],
            [
                'id' => 3,
                'nom' => 'Ministère de l\'Éducation',
                'logo' => 'assets/images/partners/mesrsfc.png',
                'siteweb' => 'https://www.enssup.gov.ma',
                'contact' => 'Ministère de l\'Enseignement Supérieur'
            ],
            [
                'id' => 4,
                'nom' => 'AMSIC',
                'logo' => 'assets/images/partners/amsic.png',
                'siteweb' => 'https://www.amsic.ma',
                'contact' => 'Association Marocaine pour la Science, l\'Innovation et la Collaboration'
            ]
        ];

        // Return only the requested number of partners
        return array_slice($partners, 0, $limit);
    }
}