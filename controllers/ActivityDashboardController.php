<?php
require_once './core/Controller.php';
require_once './models/UserActivity.php';

/**
 * Activity Dashboard Controller
 * Manages user activity tracking and reporting
 */
class ActivityDashboardController extends Controller {
    /**
     * Activity dashboard index
     */
    public function index() {
        // Ensure only admin can access
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Initialize activity model
        $activityModel = new UserActivity();

        // Get filter parameters
        $filters = [
            'activity' => $this->getInput('activity'),
            'start_date' => $this->getInput('start_date'),
            'end_date' => $this->getInput('end_date'),
            'user' => $this->getInput('user')
        ];

        // Get activity statistics
        $activityTypeStats = $activityModel->getActivityStatistics();

        // Get recent system activities with filters
        $recentActivities = $activityModel->getRecentSystemActivities(20, $filters);

        // Calculate dashboard statistics
        $totalActivities = count($recentActivities);
        $todayLogins = $this->countTodayLogins();
        $newUsers = $this->countNewUsers();
        $failedLogins = $this->countFailedLogins();

        // Render dashboard view
        $this->render('admin/activities/dashboard', [
            'pageTitle' => 'Tableau de bord des activités',
            'recentActivities' => $recentActivities,
            'activityTypeStats' => $activityTypeStats,
            'totalActivities' => $totalActivities,
            'todayLogins' => $todayLogins,
            'newUsers' => $newUsers,
            'failedLogins' => $failedLogins,
            'filters' => $filters
        ]);
    }

    /**
     * Detailed activity view
     * @param int $activityId
     */
    public function details($activityId) {
        // Ensure only admin can access
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get activity model
        $activityModel = new UserActivity();

        // Fetch specific activity details
        $activity = $this->fetchActivityDetails($activityId);

        if (!$activity) {
            $this->renderNotFound();
            return;
        }

        // Render detailed view
        $this->render('admin/activities/details', [
            'pageTitle' => 'Détails de l\'activité',
            'activity' => $activity
        ]);
    }

    /**
     * Export activity logs
     */
    public function export() {
        // Ensure only admin can access
        if (!$this->requireAuth('admin')) {
            return;
        }

        // Get filter parameters
        $filters = [
            'activity' => $this->getInput('activity'),
            'start_date' => $this->getInput('start_date'),
            'end_date' => $this->getInput('end_date'),
            'user' => $this->getInput('user')
        ];

        // Get activity model
        $activityModel = new UserActivity();

        // Fetch activities for export
        $activities = $activityModel->getRecentSystemActivities(null, $filters);

        // Generate CSV
        $this->exportToCsv($activities);
    }

    /**
     * Count logins for today
     * @return int
     */
    private function countTodayLogins() {
        $db = Db::getInstance();
        $stmt = $db->query("
            SELECT COUNT(*) 
            FROM UserActivity 
            WHERE activity = 'login_success' 
            AND DATE(dateCreation) = CURDATE()
        ");
        return $stmt->fetchColumn();
    }

    /**
     * Count new users
     * @return int
     */
    private function countNewUsers() {
        $db = Db::getInstance();
        $stmt = $db->query("
            SELECT COUNT(*) 
            FROM Utilisateur 
            WHERE DATE(dateInscription) = CURDATE()
        ");
        return $stmt->fetchColumn();
    }

    /**
     * Count failed login attempts
     * @return int
     */
    private function countFailedLogins() {
        $db = Db::getInstance();
        $stmt = $db->query("
            SELECT COUNT(*) 
            FROM UserActivity 
            WHERE activity = 'login_failed' 
            AND DATE(dateCreation) = CURDATE()
        ");
        return $stmt->fetchColumn();
    }

    /**
     * Fetch detailed activity information
     * @param int $activityId
     * @return array|null
     */
    private function fetchActivityDetails($activityId) {
        $db = Db::getInstance();
        $stmt = $db->prepare("
            SELECT ua.*, u.prenom, u.nom, u.email
            FROM UserActivity ua
            JOIN Utilisateur u ON ua.utilisateurId = u.id
            WHERE ua.id = :id
        ");
        $stmt->execute(['id' => $activityId]);

        $activity = $stmt->fetch(PDO::FETCH_ASSOC);

        // Parse metadata if exists
        if ($activity && !empty($activity['metadata'])) {
            $activity['metadata_parsed'] = json_decode($activity['metadata'], true);
        }

        return $activity;
    }

    /**
     * Export activities to CSV
     * @param array $activities
     */
    private function exportToCsv($activities) {
        // Set headers for file download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=user_activities_' . date('Y-m-d') . '.csv');

        // Create a file pointer
        $output = fopen('php://output', 'w');

        // Output the column headings
        fputcsv($output, [
            'ID',
            'Utilisateur',
            'Email',
            'Activité',
            'Description',
            'Date',
            'Métadonnées'
        ]);

        // Loop over the activities and output
        foreach ($activities as $activity) {
            fputcsv($output, [
                $activity['id'],
                $activity['prenom'] . ' ' . $activity['nom'],
                $activity['email'],
                $activity['activity'],
                $activity['description'],
                $activity['dateCreation'],
                json_encode($activity['metadata'] ?? [])
            ]);
        }

        // Close the file pointer
        fclose($output);
        exit;
    }
}