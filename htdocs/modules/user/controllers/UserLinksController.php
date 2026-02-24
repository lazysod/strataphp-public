<?php

/**
 * User Links Controller
 *
 * Handles user links management for /user/links.
 */

namespace App\Modules\User\Controllers;

use App\App;
use App\DB;
use App\User;

class UserLinksController
{
    /**
     * Display and manage user links
     * @return void
     */
    /**
     * Display and manage user links
     * Handles grouped and ungrouped links, with error handling and session checks.
     * @return void
     */
    public function index()
    {
        global $config;
        require_once dirname(__DIR__, 4) . '/bootstrap.php';
        $sessionPrefix = $config['session_prefix'] ?? 'app_';
        try {
            if (!isset($_SESSION[$sessionPrefix . 'user_id']) || $_SESSION[$sessionPrefix . 'user_id'] < 1) {
                header('Location: /user/login');
                exit;
            }
            $db = new DB($config);
            $userModel = new User($db, $config);
            $userId = $_SESSION[$sessionPrefix . 'user_id'];
            $profile_id = $_SESSION[$sessionPrefix . 'active_profile'] ?? null;
            if (!$profile_id) {
                header('Location: /user/profile_settings');
                exit;
            }
            // Fetch all links for this profile
            $profile = $userModel->get_selected_profile($userId);
            if (!$profile || !is_array($profile)) {
                $profile = [
                    'bio' => '',
                    'pride_logo' => 0,
                    'verified' => 0,
                    'profile_name' => '',
                    'locked' => 0,
                ];
            }
            $LinkGroups = [];
            $LinkList = [];
            $lnkGroups = isset($_SESSION[$sessionPrefix . 'profile']['lnk_groups']) ? $_SESSION[$sessionPrefix . 'profile']['lnk_groups'] : 0;
            if ($lnkGroups > 0) {
                // 1. Get all groups for this profile
                $LinkGroups = $db->fetchAll("SELECT * FROM link_groups WHERE profile_id = ? ORDER BY group_order ASC", [$profile_id]);
                // Filter out duplicate groups by group_name (fixes duplicate 'default' rendering)
                $uniqueGroups = [];
                foreach ($LinkGroups as $g) {
                    if (!isset($uniqueGroups[$g['group_name']])) {
                        $uniqueGroups[$g['group_name']] = $g;
                    }
                }
                $LinkGroups = array_values($uniqueGroups);
                // 2. For each group, get links in that group using links_to_group join
                foreach ($LinkGroups as &$group) {
                    $group['links'] = $db->fetchAll(
                        "SELECT l.*, ltg.group_order FROM links l
                         INNER JOIN links_to_group ltg ON l.link_id = ltg.link_id
                         WHERE l.profile_id = ? AND ltg.group_id = ?
                         ORDER BY ltg.group_order ASC",
                        [$profile_id, $group['id']]
                    );
                }
            } else {
                // Not grouped: just get all links
                $LinkList = $db->fetchAll("SELECT * FROM links WHERE profile_id = ? ORDER BY link_order ASC", [$profile_id]);
            }
            // Get all profiles for the user
            $userClass = new User($db, $config);
            $profile_list = $userClass->get_profiles($userId);
        } catch (\Throwable $e) {
            // Handle any unexpected errors gracefully
            error_log('UserLinksController error: ' . $e->getMessage());
            $error = 'An unexpected error occurred while loading your links. Please try again later.';
            $LinkGroups = [];
            $LinkList = [];
            include dirname(__DIR__, 3) . '/views/user/user_dashboard.php';
        }
    }
}
