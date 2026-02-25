<?php
namespace App\Modules\Admin\Controllers;

use App\DB;

class AdminController
{
    public function dashboard()
    {
        // Load config and set up DB
        $config = include dirname(__DIR__, 3) . '/app/config.php';
        $db = new DB($config);
        $rank_users = $db->fetchAll("SELECT `user_id` FROM `rank`");
        $rank_user_ist = [];
        foreach ($rank_users as $ru) {
            $rank_user_ist[] = $ru['user_id'];
        }
        // If there are users to exclude, build the NOT IN clause
        $notInClause = '';
        if (!empty($rank_user_ist)) {
            $escapedIds = array_map(function ($id) use ($db) {
                return "'" . $db->escapeString($id) . "'";
            }, $rank_user_ist);
            $notInClause = "WHERE id NOT IN (" . implode(",", $escapedIds) . ")";
        }
        $userCountRow = $db->fetch("SELECT COUNT(*) FROM users $notInClause");
        $userCount = $userCountRow ? array_values($userCountRow)[0] : 0;

        // Count profiles
        $profileCountRow = $db->fetch("SELECT COUNT(*) FROM profile");
        $profileCount = $profileCountRow ? array_values($profileCountRow)[0] : 0;

        // Pass variables to the view
        include dirname(__DIR__, 3) . '/views/admin/admin_dashboard.php';
    }
    public function index()
    {
        // Default admin landing page
        include dirname(__DIR__, 3) . '/views/admin/admin_login.php';
    }
}
