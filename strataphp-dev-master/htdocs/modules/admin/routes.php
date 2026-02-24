<?php
use App\Controllers\AdminController;
// Admin User Management Routes
$router->get('/admin/users', [\App\Modules\Admin\Controllers\UserAdminController::class, 'index']);
$router->get('/admin/users/add', [\App\Modules\Admin\Controllers\UserAdminController::class, 'add']);
$router->post('/admin/users/add', [\App\Modules\Admin\Controllers\UserAdminController::class, 'add']);
$router->get('/admin/users/edit/{id}', [\App\Modules\Admin\Controllers\UserAdminController::class, 'edit']);
$router->post('/admin/users/edit/{id}', [\App\Modules\Admin\Controllers\UserAdminController::class, 'edit']);
$router->get('/admin/users/suspend/{id}', [\App\Modules\Admin\Controllers\UserAdminController::class, 'suspend']);
$router->get('/admin/users/unsuspend/{id}', [\App\Modules\Admin\Controllers\UserAdminController::class, 'unsuspend']);
$router->get('/admin/users/delete/{id}', [\App\Modules\Admin\Controllers\UserAdminController::class, 'delete']);

$router->get('/admin/reset-password', [AdminController::class, 'resetPassword']);
$router->post('/admin/reset-password', [AdminController::class, 'resetPassword']);
$router->get('/admin/reset-request', [AdminController::class, 'resetRequest']);
$router->post('/admin/reset-request', [AdminController::class, 'resetRequest']);

$router->get('/admin/sessions', [\App\Modules\Admin\Controllers\AdminSessionsController::class, 'index']);
$router->post('/admin/sessions/revoke', [\App\Modules\Admin\Controllers\AdminSessionsController::class, 'revoke']);
$router->post('/admin/sessions/update-device', [\App\Modules\Admin\Controllers\AdminSessionsController::class, 'updateDevice']);
