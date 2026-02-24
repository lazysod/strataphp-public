<?php
namespace App\Modules\Admin\Controllers;
use App\DB;
use App\User;

class ProfileAdminController
{
    public function unverify($id)
    {
        global $config;
        $db = new \App\DB($config['db']);
        $db->query("UPDATE profile SET verified = 0 WHERE profile_id = ?", [$id]);
        header('Location: /admin/admin_profiles');
        exit;
    }
    public function index()
    {
        // Fetch profiles from DB (stub)
        $profiles = [];
        // Pagination logic
        global $config;
        $db = new \App\DB($config['db']);
        $userModel = new \App\User($db, $config);
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $totalProfiles = $userModel->countAllProfiles();
        $profiles = $userModel->getPaginatedProfiles($perPage, $offset);
        $totalPages = ceil($totalProfiles / $perPage);
        include __DIR__ . '/../profiles/views/list.php';
        }

        public function edit($id)
        {
            global $config;
            $db = new \App\DB($config['db']);
            $userModel = new \App\User($db, $config);
            $profile = $userModel->get_selected_profile($id);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $profile_name = trim($_POST['profile_name'] ?? '');
                $bio = trim($_POST['bio'] ?? '');
                $verified = isset($_POST['verified']) ? 1 : 0;
                $locked = isset($_POST['locked']) ? 1 : 0;
                // Optionally add more fields as needed
                $db->query(
                    "UPDATE profile SET profile_name = ?, bio = ?, verified = ?, admin_locked = ? WHERE profile_id = ?",
                    [$profile_name, $bio, $verified, $locked, $id]
                );
                    header('Location: /admin/admin_profiles');
                exit;
            }
            include __DIR__ . '/../profiles/views/edit.php';
        }

        public function verify($id)
        {
            global $config;
            $db = new \App\DB($config['db']);
            $db->query("UPDATE profile SET verified = 1 WHERE profile_id = ?", [$id]);
                header('Location: /admin/admin_profiles');
            exit;
        }

        public function suspend($id)
        {
            global $config;
            $db = new \App\DB($config['db']);
            $db->query("UPDATE profile SET admin_locked = 1 WHERE profile_id = ?", [$id]);
                header('Location: /admin/admin_profiles');
            exit;
        }

        public function unsuspend($id)
        {
            global $config;
            $db = new \App\DB($config['db']);
            $db->query("UPDATE profile SET admin_locked = 0 WHERE profile_id = ?", [$id]);
                header('Location: /admin/admin_profiles');
            exit;
        }

        public function delete($id)
        {
            global $config;
            $db = new \App\DB($config['db']);
            $db->query("DELETE FROM profile WHERE profile_id = ?", [$id]);
                header('Location: /admin/admin_profiles');
            exit;
        }
}
