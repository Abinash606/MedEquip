<?php

namespace Config;

use CodeIgniter\Config\Services;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
/** @var Router $routes */
// $routes = service('router');
$routes = Services::routes();


// Default route directs to the authentication page
$routes->get('/', 'AuthController::login');

// Authentication endpoints
// $routes->match(['GET', 'POST'], 'login', 'AuthController::login');
// $routes->get('logout', 'AuthController::logout');
// $routes->match(['GET', 'POST'], 'forgot', 'AuthController::forgot');
// $routes->match(['GET', 'POST'], 'reset/(:any)', 'AuthController::reset/$1');

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->get('forgot', 'AuthController::forgot');
$routes->post('forgot', 'AuthController::forgot');
$routes->get('reset/(:any)', 'AuthController::reset/$1');
$routes->post('reset/(:any)', 'AuthController::reset/$1');

$routes->get('debug/testLogin', 'DebugController::testLogin');


// Routes for Super Admin (company owner)
$routes->group('admin', ['filter' => 'role:super_admin'], static function ($routes) {
    $routes->get('dashboard', 'Admin\\DashboardController::index');
    $routes->get('customers', 'Admin\\CustomersController::index');

    $routes->get('data-operations/generate-backup', 'Admin\DataOperationController::generateBackup');
    $routes->get('data-operations/download-backup/(:any)', 'Admin\DataOperationController::downloadBackup/$1');
    // Customer CRUD routes

    // Customers
    $routes->get('customers', 'Admin\CustomersController::index');
    $routes->post('customers/add', 'Admin\CustomersController::add');
    $routes->get('customers/edit/(:num)', 'Admin\CustomersController::getCustomerData/$1');
    $routes->post('customers/update/(:num)', 'Admin\CustomersController::update/$1');
    $routes->get('customers/delete/(:num)', 'Admin\CustomersController::delete/$1');
    $routes->get('customers/view/(:num)', 'Admin\CustomersController::view/$1'); // Optional: for viewing customer details
    $routes->post('customers/check-email', 'Admin\CustomersController::checkEmail');
    $routes->get('customers/search', 'Admin\CustomersController::search');


    $routes->get('scheduling', 'Admin\SchedulingController::index');
    $routes->get('technicians', 'Admin\\TechniciansController::index');
    $routes->get('states', 'Admin\\TechniciansController::states');
    $routes->get('technicians/data', 'Admin\\TechniciansController::getData');
    $routes->get('technicians/(:num)', 'Admin\\TechniciansController::show/$1');
    $routes->post('technicians/store', 'Admin\\TechniciansController::store');
    $routes->post('technicians/update/(:num)', 'Admin\\TechniciansController::update/$1');
    $routes->delete('technicians/delete/(:num)', 'Admin\\TechniciansController::delete/$1');

    $routes->get('inspection-reports', 'Admin\InspectionReportsController::index');
    $routes->get('inventory', 'Admin\InventoryController::index');
    $routes->get('inventory/data', 'Admin\InventoryController::data');
    $routes->get('inventory/(:num)', 'Admin\InventoryController::show/$1');
    $routes->post('inventory/store', 'Admin\InventoryController::store');
    $routes->post('inventory/update/(:num)', 'Admin\InventoryController::update/$1');
    $routes->delete('inventory/delete/(:num)', 'Admin\InventoryController::delete/$1');
    $routes->get('financials', 'Admin\FinancialController::index');
    $routes->get('data-ops', 'Admin\DataOperationController::index');
    // $routes->get('settings', 'Admin\SettingController::index');


    // Sites
    $routes->get('sites', 'Admin\SitesController::index');
    $routes->post('sites/add', 'Admin\SitesController::add');
    $routes->post('sites/update/(:num)', 'Admin\SitesController::update/$1');

    $routes->get('sites/delete/(:num)', 'Admin\SitesController::delete/$1');
    $routes->get('sites/(:num)', 'Admin\SitesController::view/$1'); // Site detail view with tabs

    $routes->get('equipment', 'Admin\EquipmentController::index');

    $routes->post('equipment/create', 'Admin\EquipmentController::create');
    $routes->post('equipment/update/(:num)', 'Admin\EquipmentController::update/$1');
    $routes->get('equipment/delete/(:num)', 'Admin\EquipmentController::delete/$1');

    $routes->post('inspections/create', 'Admin\InspectionsController::create');
    $routes->post('inspections/update/(:num)', 'Admin\InspectionsController::update/$1');
    $routes->get('inspections/delete/(:num)', 'Admin\InspectionsController::delete/$1');

    $routes->get('inspections/searchByAssetTag',  'Admin\InspectionsController::searchByAssetTag');
    $routes->get('inspections/searchByModel',     'Admin\InspectionsController::searchByModel');

    $routes->post('work-orders/create', 'Admin\WorkOrdersController::create');
    $routes->post('work-orders/update/(:num)', 'Admin\WorkOrdersController::update/$1');
    $routes->get('work-orders/delete/(:num)', 'Admin\WorkOrdersController::delete/$1');
    $routes->get('work-orders', 'Admin\\WorkOrdersController::index');

    $routes->get('settings', 'Admin\SystemSettings::index');
    $routes->post('settings/update', 'Admin\SystemSettings::update');

    // Admins (users table)
    $routes->get('settings/admins/list', 'Admin\SystemSettings::adminsList');
    $routes->get('settings/admins/(:num)', 'Admin\SystemSettings::adminGet/$1');
    $routes->post('settings/admins/save', 'Admin\SystemSettings::adminSave');
    $routes->delete('settings/admins/delete/(:num)', 'Admin\SystemSettings::adminDelete/$1');
});

// Routes for Customer role
$routes->group('customer', ['filter' => 'role:customer'], static function ($routes) {
    $routes->get('dashboard', 'Customer\\DashboardController::index');


    $routes->get('assets', 'Customer\\AssetsController::index');
    $routes->get('inspections', 'Customer\\InspectionsController::index');
    $routes->get('documents', 'Customer\\DocumentsController::index');
});

// Routes for Technician role
$routes->group('technician', ['filter' => 'role:technician'], static function ($routes) {
    $routes->get('dashboard', 'Technician\\DashboardController::index');
    $routes->get('customers', 'Technician\\CustomerController::index');
    $routes->get('inspections', 'Technician\\InspectionController::index');
    $routes->get('sites', 'Technician\\SitesController::index');
    $routes->get('reports', 'Technician\\ReportsController::index');
    $routes->get('service-history', 'Technician\\ServiceHistoryController::index');
});

// Auto routing is disabled for security. Explicitly define your routes above.