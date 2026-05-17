<?php
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\ClientPortalController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\KpiController;
use App\Http\Controllers\Api\PaymentsAgendaController;
use App\Http\Controllers\Api\PriceController;
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\VehicleProxyController;
use App\Http\Controllers\Api\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Rutas publicas
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/surveys/{token}', [SurveyController::class, 'showByToken']);
    Route::post('/surveys', [SurveyController::class, 'store']);
    Route::get('/portal/{token}', [ClientPortalController::class, 'show']);

    // Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {

        // Auth — accesible por cualquier usuario autenticado
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::put('/auth/me', [AuthController::class, 'updateProfile']);
        Route::put('/auth/password', [AuthController::class, 'updatePassword']);

        // Work Orders
        Route::middleware('permission:work-orders.view')->group(function () {
            Route::get('/work-orders', [WorkOrderController::class, 'index']);
            Route::get('/work-orders/{id}', [WorkOrderController::class, 'show']);
        });
        Route::middleware('permission:work-orders.create')->group(function () {
            Route::post('/work-orders', [WorkOrderController::class, 'store']);
            Route::patch('/work-orders/{id}/client', [WorkOrderController::class, 'updateClient']);
            Route::patch('/work-orders/{id}/vehicle', [WorkOrderController::class, 'updateVehicle']);
        });
        Route::middleware('permission:work-orders.edit')->group(function () {
            Route::patch('/work-orders/{id}/close', [WorkOrderController::class, 'close']);
            Route::patch('/work-orders/{id}/status', [WorkOrderController::class, 'updateStatus']);
            Route::patch('/work-orders/{id}/diagnosis', [WorkOrderController::class, 'updateDiagnosis']);
            Route::post('/work-orders/{id}/checklist', [WorkOrderController::class, 'addChecklist']);
            Route::patch('/work-orders/{id}/checklist/{item}', [WorkOrderController::class, 'toggleChecklist']);
            Route::post('/work-orders/{id}/photos', [WorkOrderController::class, 'uploadPhoto']);
            Route::post('/work-orders/{id}/notes', [WorkOrderController::class, 'addNote']);
            Route::post('/work-orders/{id}/parts', [WorkOrderController::class, 'addPart']);
            Route::post('/work-orders/{id}/services', [WorkOrderController::class, 'addService']);
        });
        Route::middleware('permission:work-orders.delete')->group(function () {
            Route::delete('/work-orders/{id}', [WorkOrderController::class, 'destroy']);
        });

        // Clientes
        Route::middleware('permission:clients.view')->group(function () {
            Route::get('/clients', [ClientController::class, 'index']);
            Route::get('/clients/{client}', [ClientController::class, 'show']);
        });
        Route::middleware('permission:clients.create')->group(function () {
            Route::post('/clients', [ClientController::class, 'store']);
        });
        Route::middleware('permission:clients.edit')->group(function () {
            Route::put('/clients/{client}', [ClientController::class, 'update']);
        });
        Route::middleware('permission:clients.delete')->group(function () {
            Route::delete('/clients/{client}', [ClientController::class, 'destroy']);
        });

        // Vehiculos (usan permisos de clientes ya que son parte del mismo módulo)
        Route::middleware('permission:clients.view')->group(function () {
            Route::get('/vehicles', [VehicleController::class, 'index']);
        });
        Route::middleware('permission:clients.create')->group(function () {
            Route::post('/vehicles', [VehicleController::class, 'store']);
        });
        Route::middleware('permission:clients.edit')->group(function () {
            Route::put('/vehicles/{id}', [VehicleController::class, 'update']);
        });
        Route::middleware('permission:clients.delete')->group(function () {
            Route::delete('/vehicles/{id}', [VehicleController::class, 'destroy']);
        });

        // NHTSA Proxies — accesible por cualquier usuario autenticado
        Route::get('/vehicles/makes', [VehicleProxyController::class, 'makes']);
        Route::get('/vehicles/makes/{make}/models', [VehicleProxyController::class, 'models']);
        Route::get('/vehicles/decode/{vin}', [VehicleProxyController::class, 'decodeVin']);
        Route::get('/vehicles/recalls', [VehicleProxyController::class, 'recalls']);

        // Inventario
        Route::middleware('permission:inventory.view')->group(function () {
            Route::get('/inventory', [InventoryController::class, 'index']);
            Route::get('/inventory/{id}/movements', [InventoryController::class, 'movements']);
            Route::get('/inventory/custody', [InventoryController::class, 'custodyIndex']);
        });
        Route::middleware('permission:inventory.edit')->group(function () {
            Route::post('/inventory/{id}/movements', [InventoryController::class, 'addMovement']);
            Route::post('/inventory/custody', [InventoryController::class, 'custodyStore']);
            Route::patch('/inventory/custody/{id}/deliver', [InventoryController::class, 'custodyDeliver']);
        });
        Route::middleware('permission:inventory.create')->group(function () {
            Route::post('/inventory', [InventoryController::class, 'store']);
            Route::post('/inventory/{id}/photo', [InventoryController::class, 'uploadPhoto']);
        });
        Route::middleware('permission:inventory.edit')->group(function () {
            Route::put('/inventory/{id}', [InventoryController::class, 'update']);
        });

        // Finanzas
        Route::middleware('permission:finance.view')->group(function () {
            Route::get('/finance/summary', [FinanceController::class, 'summary']);
            Route::get('/finance/comparative', [FinanceController::class, 'comparative']);
            Route::get('/finance/cash', [FinanceController::class, 'cashIndex']);
            Route::get('/finance/receivable', [FinanceController::class, 'receivableIndex']);
            Route::get('/finance/payable', [FinanceController::class, 'payableIndex']);
            Route::get('/finance/reports/{type}', [FinanceController::class, 'reports']);
        });
        Route::middleware('permission:finance.create')->group(function () {
            Route::post('/finance/cash', [FinanceController::class, 'cashStore']);
        });
        Route::middleware('permission:finance.edit')->group(function () {
            Route::delete('/finance/cash/{id}', [FinanceController::class, 'cashDestroy']);
            Route::patch('/finance/receivable/{id}/payment', [FinanceController::class, 'receivablePayment']);
            Route::patch('/finance/payable/{id}/payment', [FinanceController::class, 'payablePayment']);
        });

        // Agenda de pagos
        Route::middleware('permission:finance.view')->group(function () {
            Route::get('/payments-agenda', [PaymentsAgendaController::class, 'index']);
        });
        Route::middleware('permission:finance.create')->group(function () {
            Route::post('/payments-agenda', [PaymentsAgendaController::class, 'store']);
        });
        Route::middleware('permission:finance.edit')->group(function () {
            Route::put('/payments-agenda/{id}', [PaymentsAgendaController::class, 'update']);
            Route::patch('/payments-agenda/{id}/confirm', [PaymentsAgendaController::class, 'confirm']);
            Route::post('/payments-agenda/{id}/receipt', [PaymentsAgendaController::class, 'uploadReceipt']);
        });

        // Lista de precios
        Route::middleware('permission:settings.manage')->group(function () {
            Route::get('/prices', [PriceController::class, 'index']);
            Route::post('/prices', [PriceController::class, 'store']);
            Route::put('/prices/{id}', [PriceController::class, 'update']);
        });

        // Actividades
        Route::middleware('permission:work-orders.view')->group(function () {
            Route::apiResource('/activities', ActivityController::class);
            Route::post('/activities/{id}/comments', [ActivityController::class, 'addComment']);
        });

        // KPIs
        Route::middleware('permission:finance.view')->group(function () {
            Route::get('/kpis', [KpiController::class, 'index']);
            Route::get('/kpi-activities', [KpiController::class, 'kpiActivitiesIndex']);
            Route::get('/organization', [KpiController::class, 'organizationInfo']);
        });
        Route::middleware('permission:settings.manage')->group(function () {
            Route::post('/kpis', [KpiController::class, 'store']);
            Route::put('/kpis/{id}', [KpiController::class, 'update']);
            Route::post('/kpi-activities', [KpiController::class, 'kpiActivitiesStore']);
            Route::put('/kpi-activities/{id}', [KpiController::class, 'kpiActivitiesUpdate']);
            Route::delete('/kpi-activities/{id}', [KpiController::class, 'kpiActivitiesDestroy']);
            Route::put('/organization', [KpiController::class, 'updateOrganizationInfo']);
        });

        // Empleados
        Route::middleware('permission:employees.view')->group(function () {
            Route::get('/employees', [EmployeeController::class, 'index']);
            Route::get('/employees/{employee}', [EmployeeController::class, 'show']);
        });
        Route::middleware('permission:employees.manage')->group(function () {
            Route::post('/employees', [EmployeeController::class, 'store']);
            Route::put('/employees/{employee}', [EmployeeController::class, 'update']);
            Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy']);
        });

        // Usuarios del sistema
        Route::middleware('permission:settings.manage')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/roles', [UserController::class, 'roles']);
            Route::post('/users', [UserController::class, 'store']);
            Route::delete('/users/{id}', [UserController::class, 'destroy']);
        });

        // Contactos / Proveedores
        Route::middleware('permission:finance.view')->group(function () {
            Route::get('/contacts', [ContactController::class, 'index']);
            Route::get('/contacts/{contact}', [ContactController::class, 'show']);
        });
        Route::middleware('permission:settings.manage')->group(function () {
            Route::post('/contacts', [ContactController::class, 'store']);
            Route::put('/contacts/{contact}', [ContactController::class, 'update']);
            Route::delete('/contacts/{contact}', [ContactController::class, 'destroy']);
        });

        // Portal cliente
        Route::middleware('permission:work-orders.create')->group(function () {
            Route::post('/work-orders/{id}/portal-token', [ClientPortalController::class, 'regenerateToken']);
            Route::post('/work-orders/{id}/portal-share', [ClientPortalController::class, 'shareByEmail']);
        });
    });
});
