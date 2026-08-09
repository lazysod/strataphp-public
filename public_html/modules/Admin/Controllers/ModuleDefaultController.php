<?php
// Controller to handle setting the default module from the admin UI

namespace App\Modules\Admin\Controllers;
use App\Logger;
require_once __DIR__ . '/../../../app/updateDefaultModule.php';

class ModuleDefaultController
{
    /**
     * Set the default module from the admin UI.
     * Handles POST request and updates the default module.
     * @throws \Exception
     */
    public function setDefault()
    {
        $logger = Logger::getInstance();
        $logger->info('ModuleDefaultController::setDefault called');
        $logger->info('POST: ' . print_r($_POST, true));
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['default_module'])) {
            $newDefault = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['default_module']); // sanitize
            try {
                updateDefaultModule($newDefault);
                $logger->info('Default module updated to: ' . $newDefault);
                $_SESSION['success'] = 'Default module updated to ' . htmlspecialchars($newDefault);
            } catch (\Exception $e) {
                $logger->error('Failed to update default module: ' . $e->getMessage());
                $_SESSION['error'] = 'Failed to update default module: ' . $e->getMessage();
            }
        } else {
            $logger->warning('POST missing or default_module not set');
        }
        header('Location: /admin/modules');
        exit;
    }
}
// Usage: Route POST /admin/modules/set-default to ModuleDefaultController::setDefault
