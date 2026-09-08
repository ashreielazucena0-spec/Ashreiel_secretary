<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->post('register', 'Auth::doRegister');
$routes->get('logout', 'Auth::logout');

$routes->group('barangay', ['filter' => 'auth'], function ($routes) {

    $routes->get('dashboard', 'Barangay::dashboard');
    $routes->get('reports', 'Barangay::reports');
    $routes->get('activity-logs', 'Barangay::activityLogs');

    // Admin-only area: user management + backup & restore, and every
    // sensitive action underneath them. Placed ahead of the generic
    // module routes below so these specific paths win the match.
    $routes->group('', ['filter' => 'admin'], function ($routes) {

        $routes->get('backup', 'Barangay::backup');
        $routes->get('backup/export', 'Barangay::backupExport');
        $routes->post('backup/import', 'Barangay::backupImport');
        $routes->post('backup/reset', 'Barangay::backupReset');

        $routes->get('users', 'Barangay::index/users');
        $routes->post('users/store', 'Barangay::store/users');
        $routes->post('users/update/(:segment)', 'Barangay::update/users/$1');
        $routes->post('users/delete/(:segment)', 'Barangay::delete/users/$1');
        $routes->post('users/toggle-status/(:segment)', 'Barangay::toggleUserStatus/$1');

    });

    $routes->get('certificates/print/(:segment)', 'Barangay::printCertificate/$1');

    // generic module CRUD — covers residents, households, certificates,
    // requests, cases, meetings, documents, payments, officials
    // (users and backup/restore are handled above, admin-only)
    $routes->get('(:segment)', 'Barangay::index/$1');
    $routes->post('(:segment)/store', 'Barangay::store/$1');
    $routes->post('(:segment)/update/(:segment)', 'Barangay::update/$1/$2');
    $routes->post('(:segment)/delete/(:segment)', 'Barangay::delete/$1/$2');

});