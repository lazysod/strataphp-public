<?php
// Admin User Management Routes
$router->get('/admin/users', ['UserAdminController', 'index']);
$router->get('/admin/users/add', ['UserAdminController', 'add']);
$router->post('/admin/users/add', ['UserAdminController', 'add']);
$router->get('/admin/users/edit/{id}', ['UserAdminController', 'edit']);
$router->post('/admin/users/edit/{id}', ['UserAdminController', 'edit']);
$router->get('/admin/users/suspend/{id}', ['UserAdminController', 'suspend']);
$router->get('/admin/users/unsuspend/{id}', ['UserAdminController', 'unsuspend']);
$router->get('/admin/users/delete/{id}', ['UserAdminController', 'delete']);

$router->get('/admin/reset-password', ['AdminController', 'resetPassword']);
$router->post('/admin/reset-password', ['AdminController', 'resetPassword']);
$router->get('/admin/reset-request', ['AdminController', 'resetRequest']);
$router->post('/admin/reset-request', ['AdminController', 'resetRequest']);
