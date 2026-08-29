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

    $routes->get('backup', 'Barangay::backup');
    $routes->get('backup/export', 'Barangay::backupExport');
    $routes->post('backup/import', 'Barangay::backupImport');
    $routes->post('backup/reset', 'Barangay::backupReset');

    $routes->get('certificates/print/(:segment)', 'Barangay::printCertificate/$1');
    $routes->post('users/toggle-status/(:segment)', 'Barangay::toggleUserStatus/$1');

    // generic module CRUD — covers residents, households, certificates,
    // requests, cases, meetings, documents, payments, officials, users
    $routes->get('(:segment)', 'Barangay::index/$1');
    $routes->post('(:segment)/store', 'Barangay::store/$1');
    $routes->post('(:segment)/update/(:segment)', 'Barangay::update/$1/$2');
    $routes->post('(:segment)/delete/(:segment)', 'Barangay::delete/$1/$2');

});