<?php
/**
 * PermissionsHelper Class
 * Helper for managing bureau executive member permissions
 */
class PermissionsHelper {
    /**
     * Get all available permissions
     * @return array
     */
    public static function getAllPermissions() {
        $permissions = [
            // Publications
            'view_publications' => 'Voir les publications',
            'add_publication' => 'Ajouter une publication',
            'edit_publication' => 'Modifier toute publication',
            'delete_publication' => 'Supprimer toute publication',

            // Events
            'view_events' => 'Voir les événements',
            'add_event' => 'Ajouter un événement',
            'edit_event' => 'Modifier tout événement',
            'delete_event' => 'Supprimer tout événement',

            // Projects
            'view_projects' => 'Voir les projets',
            'create_project' => 'Créer un projet',
            'edit_project' => 'Modifier tout projet',
            'delete_project' => 'Supprimer tout projet',

            // News
            'view_news' => 'Voir les actualités',
            'create_news' => 'Créer une actualité',
            'edit_news' => 'Modifier toute actualité',
            'delete_news' => 'Supprimer toute actualité',

            // Research Ideas
            'view_ideas' => 'Voir les idées de recherche',
            'approve_ideas' => 'Approuver/Refuser les idées',

            // Users
            'view_users' => 'Voir les utilisateurs',
            'create_user' => 'Créer un utilisateur',
            'edit_user' => 'Modifier un utilisateur',

            // Contacts
            'view_contacts' => 'Voir les messages de contact',
            'reply_contacts' => 'Répondre aux messages',
            'delete_contacts' => 'Supprimer les messages',

            // Special
            'admin_access' => 'Accès au tableau de bord admin',
            'manage_bureau' => 'Gérer le bureau exécutif',
            'manage_settings' => 'Gérer les paramètres'
        ];

        return $permissions;
    }

    /**
     * Get permission categories
     * @return array
     */
    public static function getPermissionCategories() {
        return [
            'publications' => 'Publications',
            'events' => 'Événements',
            'projects' => 'Projets',
            'news' => 'Actualités',
            'ideas' => 'Idées de recherche',
            'users' => 'Utilisateurs',
            'contacts' => 'Contacts',
            'admin' => 'Administration'
        ];
    }

    /**
     * Get permissions by category
     * @return array
     */
    public static function getPermissionsByCategory() {
        $allPermissions = self::getAllPermissions();
        $categorized = [
            'publications' => [],
            'events' => [],
            'projects' => [],
            'news' => [],
            'ideas' => [],
            'users' => [],
            'contacts' => [],
            'admin' => []
        ];

        foreach ($allPermissions as $key => $label) {
            if (strpos($key, 'publication') !== false) {
                $categorized['publications'][$key] = $label;
            } elseif (strpos($key, 'event') !== false) {
                $categorized['events'][$key] = $label;
            } elseif (strpos($key, 'project') !== false) {
                $categorized['projects'][$key] = $label;
            } elseif (strpos($key, 'news') !== false) {
                $categorized['news'][$key] = $label;
            } elseif (strpos($key, 'idea') !== false) {
                $categorized['ideas'][$key] = $label;
            } elseif (strpos($key, 'user') !== false) {
                $categorized['users'][$key] = $label;
            } elseif (strpos($key, 'contact') !== false) {
                $categorized['contacts'][$key] = $label;
            } else {
                $categorized['admin'][$key] = $label;
            }
        }

        return $categorized;
    }
}