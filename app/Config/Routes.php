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
$routes->get('customer/reset-password/(:any)', 'AuthController::resetPassword/$1');
$routes->post('customer/update-password', 'AuthController::updatePassword');

$routes->get('reset/(:any)', 'AuthController::reset/$1');
$routes->post('reset/(:any)', 'AuthController::reset/$1');

$routes->get('debug/testLogin', 'DebugController::testLogin');


// Routes for Super Admin (company owner)
$routes->group('admin', ['filter' => 'role:super_admin'], static function ($routes) {
    $routes->get('dashboard', 'Admin\\DashboardController::index');
    $routes->get('customers', 'Admin\\CustomersController::index');

    $routes->get('data-operations/generate-backup', 'Admin\DataOperationController::generateBackup');
    $routes->get('data-operations/download-backup/(:any)', 'Admin\DataOperationController::downloadBackup/$1');

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
    $routes->get('equipment/show/(:num)', 'Admin\EquipmentController::show/$1');

    $routes->get('equipment-db/(:num)', 'Admin\EquipmentController::show/$1');
    $routes->post('equipment-db/save', 'Admin\EquipmentController::save');
    $routes->post('equipment-db/delete/(:num)', 'Admin\EquipmentController::deletedb/$1');

    $routes->post(
        'data-operations/import-equipment',
        'Admin\DataOperationController::importEquipment'
    );

    $routes->post('inspections/create', 'Admin\InspectionsController::create');
    $routes->post('inspections/update/(:num)', 'Admin\InspectionsController::update/$1');
    $routes->get('inspections/delete/(:any)', 'Admin\InspectionsController::delete/$1');
    $routes->post('inspections/deleteById/(:any)', 'Admin\InspectionsController::deleteById/$1');

    $routes->get('inspections/searchByAssetTag',  'Admin\InspectionsController::searchByAssetTag');
    $routes->get('inspections/searchByModel',     'Admin\InspectionsController::searchByModel');

    $routes->get('inspections/searchBySerial', 'Admin\InspectionsController::searchBySerial');
    $routes->get('inspections/searchByModel', 'Admin\InspectionsController::searchByModel');
    // $routes->post('create', 'Admin\InspectionsController::create');
    $routes->get('inspections/group/(:any)', 'Admin\InspectionsController::getInspectionGroup/$1');
    $routes->get('inspections/getByGroupId/', 'Admin\InspectionsController::getByGroupId');

    // In your admin routes section, add:
    $routes->get('inspections/getInspectionById/(:num)', 'Admin\InspectionsController::getInspectionById/$1');
    $routes->post('inspections/updateInspection', 'Admin\InspectionsController::updateInspection');

   
    $routes->get('inspections/reportData', 'Admin\InspectionsController::reportData');
    $routes->get('inspections/reportData/(:any)', 'Admin\InspectionsController::reportData/$1');

        // PDF endpoint for inspection reports
    $routes->get('inspections/reportPdf/(:any)', 'Admin\InspectionsController::reportPdf/$1');




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


    $routes->get('settings/iq-notes', 'Admin\SystemSettings::iqNotes');
    $routes->get('settings/iq-notes/(:num)', 'Admin\SystemSettings::iqNoteGet/$1');
    $routes->post('settings/iq-notes/save', 'Admin\SystemSettings::iqNoteSave');
    // $routes->delete('iq-notes/delete/(:num)', 'Admin\SystemSettings::iqNoteDelete/$1');
    $routes->post('settings/iq-notes/delete', 'Admin\SystemSettings::iqNoteDelete');

    $routes->get('settings/equipment', 'Admin\SystemSettings::equipmentList');
    $routes->post('settings/equipment/save', 'Admin\SystemSettings::equipmentSave');
    $routes->get('site-inspection/get-equipment', 'Admin\SiteInspectionWorkflowController::getEquipment');
    $routes->post('settings/equipment/toggle', 'Admin\SystemSettings::equipmentToggle');
    $routes->post('site-inspection/record', 'Admin\SiteInspectionWorkflowController::recordInspection');
    $routes->delete('settings/equipment/delete/(:num)', 'Admin\SystemSettings::equipmentDelete/$1');
});

// Routes for Customer role
$routes->group('customer', ['filter' => 'role:customer'], static function ($routes) {
    $routes->get('dashboard', 'Customer\\DashboardController::index');
    $routes->post('assets/store', 'Customer\AssetsController::store');


    $routes->get('assets', 'Customer\\AssetsController::index');
    $routes->get('inspections', 'Customer\\InspectionsController::index');
    $routes->get('documents', 'Customer\\DocumentsController::index');
});

// Routes for Technician role
$routes->group('technician', ['filter' => 'role:technician'], static function ($routes) {
    $routes->get('dashboard',                                   'Technician\\DashboardController::index');
    $routes->get('customers',                                   'Technician\\CustomerController::index');

    // ── Inspections ─────────────────────────────────────────────
    $routes->get('inspections',                                 'Technician\\InspectionController::index');
    $routes->get('inspections/getEquipment',                    'Technician\\InspectionController::getEquipment');
    $routes->get('inspections/groupItems',                      'Technician\\InspectionController::groupItems');
    $routes->get('inspections/siteInventory',                   'Technician\\InspectionController::siteInventory');
    $routes->get('inspections/groupWorkOrders',                 'Technician\\InspectionController::groupWorkOrders');
    $routes->post('inspections/record',                         'Technician\\InspectionController::record');
    $routes->post('inspections/deleteRecord/(:num)',            'Technician\\InspectionController::deleteRecord/$1');
    $routes->get('inspections/reportPreview/(:any)',            'Technician\\InspectionController::reportPreview/$1');
    $routes->get('inspections/reportPdf/(:any)',                'Technician\\InspectionController::reportPdf/$1');

    // ── Inspection Workflow (new — routed to SiteInspectionWorkflowController) ──
    $routes->get('site-inspection/get-equipment',               'Technician\\SiteInspectionWorkflowController::getEquipment');
    $routes->post('site-inspection/record',                     'Technician\\SiteInspectionWorkflowController::recordInspection');
    $routes->post('inspections/create',                         'Technician\\SiteInspectionWorkflowController::create');
    $routes->get('inspections/searchBySerial',                  'Technician\\SiteInspectionWorkflowController::searchBySerial');
    $routes->get('inspections/searchByModel',                   'Technician\\SiteInspectionWorkflowController::searchByModel');
    $routes->get('inspections/getByGroupId',                    'Technician\\SiteInspectionWorkflowController::getByGroupId');
    $routes->get('inspections/getInspectionById/(:num)',        'Technician\\SiteInspectionWorkflowController::getInspectionById/$1');
    $routes->post('inspections/updateInspection',               'Technician\\SiteInspectionWorkflowController::updateInspection');
    $routes->post('inspections/deleteById/(:any)',              'Technician\\SiteInspectionWorkflowController::deleteById/$1');
    $routes->get('inspections/reportData',                      'Technician\\SiteInspectionWorkflowController::reportData');
    $routes->get('inspections/reportData/(:any)',               'Technician\\SiteInspectionWorkflowController::reportData/$1');
    $routes->post('inspections/deleteGroup', 'Technician\\SiteInspectionWorkflowController::deleteGroup');
    // ── Work Orders ────────────────────────────────────────────
    $routes->get('work-orders',                                 'Technician\\WorkOrdersController::index');
    $routes->post('work-orders/create',                         'Technician\\SitesController::workOrderCreate');
    $routes->post('work-orders/update/(:num)',                  'Technician\\SitesController::workOrderUpdate/$1');
    $routes->get('work-orders/delete/(:num)',                   'Technician\\SitesController::workOrderDelete/$1');

    // ── Sites ──────────────────────────────────────────────────
    $routes->get('sites',                                       'Technician\\SitesController::index');
    $routes->get('sites/view/(:num)',                           'Technician\\SitesController::view/$1');

    // ── Equipment ──────────────────────────────────────────────
    $routes->get('equipment/show/(:num)',                       'Technician\\SitesController::equipmentShow/$1');
    $routes->post('equipment/create',                           'Technician\\SitesController::equipmentCreate');
    $routes->post('equipment/update/(:num)',                    'Technician\\SitesController::equipmentUpdate/$1');
    $routes->get('equipment/delete/(:num)',                     'Technician\\SitesController::equipmentDelete/$1');

    // ── Reports / Service History ──────────────────────────────
    $routes->get('reports',                                     'Technician\\ReportsController::index');
    $routes->get('service-history',                             'Technician\\ServiceHistoryController::index');
    $routes->post('service-history/create',                     'Technician\\ServiceHistoryController::create');
});

// Auto routing is disabled for security. Explicitly define your routes above.