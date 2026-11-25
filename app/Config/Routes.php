<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');

$routes->group('barangay', ['filter' => 'auth'], function ($routes) {

    $routes->get('dashboard', 'Barangay::dashboard');
    $routes->get('reports', 'Barangay::reports');
    $routes->get('reports/household/(:segment)', 'Barangay::reportHousehold/$1');
    $routes->get('reports/(:segment)', 'Barangay::reportModule/$1');
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
    $routes->get('requests/print/(:segment)', 'Barangay::printRequest/$1');
    $routes->get('payments/print/(:segment)', 'Barangay::printReceipt/$1');
    $routes->post('requests/release/(:segment)', 'Barangay::releaseRequest/$1');
    $routes->post('residents/reset-login/(:segment)', 'Barangay::resetResidentLogin/$1');
    $routes->post('officials/new-term', 'Barangay::newOfficialsTerm');

    // generic module CRUD — covers residents, households, certificates,
    // requests, cases, meetings, documents, payments, officials
    // (users and backup/restore are handled above, admin-only)
    $routes->get('(:segment)', 'Barangay::index/$1');
    $routes->post('(:segment)/store', 'Barangay::store/$1');
    $routes->post('(:segment)/update/(:segment)', 'Barangay::update/$1/$2');
    $routes->post('(:segment)/delete/(:segment)', 'Barangay::delete/$1/$2');

});

// Shared download for ordinance / resolution files (any logged-in account)
$routes->get('files/document/(:segment)', 'Files::document/$1', ['filter' => 'auth']);
$routes->get('files/resident/(:segment)', 'Files::resident/$1', ['filter' => 'auth']);

// Resident portal
$routes->group('resident', ['filter' => 'resident'], function ($routes) {
    $routes->get('/', 'Resident::dashboard');
    $routes->get('dashboard', 'Resident::dashboard');
    $routes->get('request', 'Resident::requestForm');
    $routes->post('request', 'Resident::submitRequest');
    $routes->get('requests', 'Resident::requests');
    $routes->get('certificates', 'Resident::certificates');
    $routes->get('household', 'Resident::household');
    $routes->get('documents', 'Resident::documents');
    $routes->get('account', 'Resident::account');
    $routes->post('account', 'Resident::changePassword');
});
