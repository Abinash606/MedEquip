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

$routes->post('impersonation/restore', 'Admin\\TechniciansController::restoreAdminSession', ['filter' => 'auth']);

// Routes for Super Admin (company owner)
$routes->group('admin', ['filter' => 'role:super_admin'], static function ($routes) {
    $routes->get('dashboard', 'Admin\\DashboardController::index');
    $routes->get('dashboard/search',                'Admin\\DashboardController::search');
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
    $routes->get('scheduling/events', 'Admin\\SchedulingController::events');
    $routes->post('scheduling/store', 'Admin\\SchedulingController::store');
    $routes->post('scheduling/reschedule', 'Admin\\\\SchedulingController::reschedule');
    $routes->post('scheduling/send-reminder', 'Admin\\\\SchedulingController::sendReminder');
    $routes->get('customers/filter-sites/(:num)', 'Admin\\CustomersController::filterSites/$1');
    $routes->get('technicians', 'Admin\\TechniciansController::index');
    $routes->get('states', 'Admin\\TechniciansController::states');
    $routes->get('technicians/data', 'Admin\\TechniciansController::getData');
    $routes->get('technicians/(:num)', 'Admin\\TechniciansController::show/$1');
    $routes->post('technicians/store', 'Admin\\TechniciansController::store');
    $routes->post('technicians/update/(:num)', 'Admin\\TechniciansController::update/$1');
    $routes->delete('technicians/delete/(:num)', 'Admin\\TechniciansController::delete/$1');
    $routes->post('technicians/login-as/(:num)',           'Admin\\TechniciansController::loginAs/$1');
    $routes->get('technicians/open-as/(:alphanum)',          'Admin\\TechniciansController::openAs/$1');

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
    $routes->get('sites', 'Admin\\SitesController::index');
    $routes->get('sites/equipment-data/(:num)', 'Admin\\SitesController::equipmentData/$1');
    $routes->get('sites/work-orders-data/(:num)', 'Admin\\SitesController::workOrdersData/$1');
    $routes->post('sites/add', 'Admin\SitesController::add');
    $routes->post('sites/update/(:num)', 'Admin\SitesController::update/$1');

    $routes->get('sites/delete/(:num)', 'Admin\SitesController::delete/$1');
    $routes->get('sites/(:num)', 'Admin\SitesController::view/$1'); // Site detail view with tabs

    $routes->get('equipment', 'Admin\EquipmentController::index');
    $routes->get('equipment/dropdown-options', 'Admin\EquipmentController::getDropdownOptions');
    $routes->post('equipment/create', 'Admin\EquipmentController::create');
    $routes->post('equipment/update/(:num)', 'Admin\EquipmentController::update/$1');
    $routes->get('equipment/delete/(:num)', 'Admin\EquipmentController::delete/$1');
    $routes->get('equipment/show/(:num)', 'Admin\EquipmentController::show/$1');

    $routes->get('equipment-db/(:num)', 'Admin\EquipmentDbController::show/$1');
    $routes->post('equipment-db/save', 'Admin\EquipmentDbController::save');
    $routes->post('equipment-db/delete/(:num)', 'Admin\EquipmentDbController::deleteRecord/$1');
    $routes->post('equipment/bulk-import', 'Admin\\EquipmentController::bulkImport');

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
    $routes->post('inspections/updateGroupTitle',  'Admin\\InspectionsController::updateGroupTitle');
    $routes->post('inspections/updateGroupStatus', 'Admin\\InspectionsController::updateGroupStatus');

    // PDF endpoint for inspection reports
    $routes->get('inspections/reportPdf/(:any)', 'Admin\InspectionsController::reportPdf/$1');
    $routes->get('inspections/reportPreview/(:any)', 'Admin\InspectionsController::reportPreview/$1');



    $routes->post('work-orders/create', 'Admin\WorkOrdersController::create');
    $routes->post('work-orders/update/(:num)', 'Admin\WorkOrdersController::update/$1');
    $routes->get('work-orders/show/(:num)', 'Admin\WorkOrdersController::show/$1');
    $routes->get('work-orders/findByGroup', 'Admin\WorkOrdersController::findByGroup');
    $routes->get('work-orders/delete/(:num)', 'Admin\WorkOrdersController::delete/$1'); // keep for redirect-style deletes
    $routes->post('work-orders/delete/(:num)', 'Admin\WorkOrdersController::delete/$1');
    $routes->get('work-orders', 'Admin\WorkOrdersController::index');


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
    $routes->get('site-inspection/last-device', 'Admin\\SiteInspectionWorkflowController::getLastDeviceForSite');
    $routes->get('inspection-reports/list', 'Admin\\InspectionReportsController::listData');
    $routes->post('settings/equipment/toggle', 'Admin\SystemSettings::equipmentToggle');
    $routes->post('site-inspection/record', 'Admin\SiteInspectionWorkflowController::recordInspection');
    $routes->post('site-inspection/add-device', 'Admin\\SiteInspectionWorkflowController::addDevice');
    $routes->delete('settings/equipment/delete/(:num)', 'Admin\SystemSettings::equipmentDelete/$1');

    // Labor Codes (Settings > Labor Codes tab)
    $routes->get('settings/labor-codes',                  'Admin\\LaborCodesController::index');
    $routes->get('settings/labor-codes/(:num)',           'Admin\\LaborCodesController::get/$1');
    $routes->post('settings/labor-codes/save',            'Admin\\LaborCodesController::save');
    $routes->delete('settings/labor-codes/delete/(:num)', 'Admin\\LaborCodesController::delete/$1');

    // Work Order Invoice + Packing Slip
    // IMPORTANT: literal route 'labor-codes-list' must be BEFORE (:num) routes
    $routes->get('work-orders/labor-codes-list',               'Admin\\WorkOrderInvoiceController::laborCodesList');
    $routes->get('work-orders/(:num)/invoice/data',            'Admin\\WorkOrderInvoiceController::getData/$1');
    $routes->post('work-orders/(:num)/invoice/save',           'Admin\\WorkOrderInvoiceController::save/$1');
    $routes->get('work-orders/(:num)/invoice/download',        'Admin\\WorkOrderInvoiceController::downloadInvoice/$1');
    $routes->get('work-orders/(:num)/packing-slip/download',   'Admin\\WorkOrderInvoiceController::downloadPackingSlip/$1');
});

// Routes for Customer role
$routes->group('customer', ['filter' => 'role:customer'], static function ($routes) {
    $routes->get('dashboard', 'Customer\\DashboardController::index');
    $routes->post('assets/store', 'Customer\AssetsController::store');


    $routes->get('assets', 'Customer\\AssetsController::index');
    $routes->post('assets/report-issue', 'Customer\\AssetsController::reportIssue');
    $routes->post('assets/bulk-import', 'Customer\\AssetsController::bulkImport');
    $routes->get('inspections', 'Customer\\InspectionsController::index');
    $routes->get('inspections/reportPdf/(:any)', 'Customer\\InspectionsController::reportPdf/$1');
    $routes->get('documents', 'Customer\\DocumentsController::index');
});

// Routes for Technician role
$routes->group('technician', ['filter' => 'role:technician'], static function ($routes) {
    $routes->get('dashboard',                                   'Technician\\DashboardController::index');
    $routes->get('search',                                       'Technician\\DashboardController::search');
    $routes->get('scheduling',                                   'Technician\\SchedulingController::index');
    $routes->get('scheduling/events',                            'Technician\\SchedulingController::events');
    $routes->get('customers',                                   'Technician\\CustomerController::index');
    $routes->post('sites/add', 'Technician\\SitesController::siteCreate');
    $routes->get('customers/filter-sites/(:num)', 'Technician\\CustomerController::filterSites/$1');
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
    $routes->get('inspections/listData',                        'Technician\\InspectionController::listData');

    // ── Inspection Workflow (new — routed to SiteInspectionWorkflowController) ──
    $routes->post(
        'site-inspection/add-device',
        'Technician\\SiteInspectionWorkflowController::addDevice'
    );
    $routes->get('site-inspection/get-equipment',               'Technician\\SiteInspectionWorkflowController::getEquipment');
    $routes->get('site-inspection/last-device',                 'Technician\\SiteInspectionWorkflowController::getLastDeviceForSite');
    $routes->get('iq-notes',                                    'Technician\\SiteInspectionWorkflowController::getIqNotes');
    $routes->post('iq-notes/save',                               'Admin\\SystemSettings::iqNoteSave');
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
    $routes->post('inspections/updateGroupTitle',               'Admin\\InspectionsController::updateGroupTitle');
    $routes->post('inspections/updateGroupStatus',              'Admin\\InspectionsController::updateGroupStatus');
    $routes->post('inspections/deleteGroup', 'Technician\\SiteInspectionWorkflowController::deleteGroup');
    // ── Work Orders ────────────────────────────────────────────
    $routes->get('work-orders',                                 'Technician\\WorkOrdersController::index');
    $routes->get('work-orders/view/(:num)',                     'Technician\\WorkOrdersController::show/$1');
    $routes->post('work-orders/create',       'Technician\SitesController::workOrderCreate');
    $routes->get('work-orders/show/(:num)',   'Technician\SitesController::workOrderShow/$1');
    $routes->get('work-orders/findByGroup', 'Technician\SitesController::workOrderFindByGroup');
    $routes->post('work-orders/update/(:num)','Technician\SitesController::workOrderUpdate/$1');
    $routes->get('work-orders/delete/(:num)', 'Technician\SitesController::workOrderDelete/$1'); // keep for redirect-style deletes
    $routes->post('work-orders/delete/(:num)','Technician\SitesController::workOrderDelete/$1');


    // ── Sites ──────────────────────────────────────────────────
    $routes->get('sites',                                       'Technician\\SitesController::index');
    $routes->get('sites/view/(:num)',                           'Technician\\SitesController::view/$1');
    $routes->get('sites/equipment-data/(:num)',                 'Technician\SitesController::equipmentData/$1');
    $routes->get('sites/work-orders-data/(:num)',               'Technician\SitesController::workOrdersData/$1');

    // ── Equipment ──────────────────────────────────────────────
    $routes->get('equipment/show/(:num)',                       'Technician\\SitesController::equipmentShow/$1');
    $routes->get('equipment/dropdown-options',                  'Technician\\SitesController::equipmentDropdownOptions');
    $routes->post('equipment/create',                           'Technician\\SitesController::equipmentCreate');
    $routes->post('equipment/update/(:num)',                    'Technician\\SitesController::equipmentUpdate/$1');
    $routes->get('equipment/delete/(:num)',                     'Technician\\SitesController::equipmentDelete/$1');

    // ── Reports / Service History ──────────────────────────────
    $routes->get('reports',                                     'Technician\\ReportsController::index');
    $routes->get('service-history',                             'Technician\\ServiceHistoryController::index');
    $routes->post('service-history/create',                     'Technician\\ServiceHistoryController::create');
    $routes->get('inventory',                                   'Technician\\InventoryController::index');

    // Technician: Work Order Invoice + Packing Slip (reuses Admin controller)
    $routes->get('work-orders/labor-codes-list',               'Admin\\WorkOrderInvoiceController::laborCodesList');
    $routes->get('work-orders/(:num)/invoice/data',            'Admin\\WorkOrderInvoiceController::getData/$1');
    $routes->post('work-orders/(:num)/invoice/save',           'Admin\\WorkOrderInvoiceController::save/$1');
    $routes->get('work-orders/(:num)/invoice/download',        'Admin\\WorkOrderInvoiceController::downloadInvoice/$1');
    $routes->get('work-orders/(:num)/packing-slip/download',   'Admin\\WorkOrderInvoiceController::downloadPackingSlip/$1');
});


// Auto routing is disabled for security. Explicitly define your routes above.
