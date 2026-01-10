<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MachineCategoryController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\MachineModelController;
use App\Http\Controllers\MachineSizeController;
use App\Http\Controllers\FlangeSizeController;
use App\Http\Controllers\FeederController;
use App\Http\Controllers\FeederBrandController;
use App\Http\Controllers\MachineHookController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\MachineNozzleController;
use App\Http\Controllers\MachineDropinController;
use App\Http\Controllers\MachineBeamController;
use App\Http\Controllers\MachineClothRollerController;
use App\Http\Controllers\MachineSoftwareController;
use App\Http\Controllers\HsnCodeController;
use App\Http\Controllers\WirController;
use App\Http\Controllers\MachineShaftController;
use App\Http\Controllers\MachineLeverController;
use App\Http\Controllers\MachineChainController;
use App\Http\Controllers\MachineHealdWireController;
use App\Http\Controllers\MachineEReadController;
use App\Http\Controllers\DeliveryTermController;
use App\Http\Controllers\MachineStatusController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\BusinessFirmController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProformaInvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PortOfDestinationController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (Request $request) {
    // Get month from request or use current month
    $month = $request->input('month', now()->format('Y-m'));
    $selectedDate = \Carbon\Carbon::createFromFormat('Y-m', $month);
    
    $startOfMonth = $selectedDate->copy()->startOfMonth();
    $endOfMonth = $selectedDate->copy()->endOfMonth();
    
    $tasks = \App\Models\Task::where('user_id', auth()->id())
        ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
        ->where('status', '!=', 'completed')
        ->with('lead')
        ->orderBy('due_date', 'asc')
        ->get()
        ->groupBy(function($task) {
            return $task->due_date ? $task->due_date->format('Y-m-d') : null;
        });
    
    // Prepare tasks data for JavaScript
    $tasksForJs = $tasks->map(function($dayTasks, $date) {
        return [
            'date' => $date,
            'tasks' => $dayTasks->map(function($task) use ($date) {
                $dueDate = $task->due_date ? $task->due_date : null;
                $lead = $task->lead;
                $scheduledTime = $lead && $lead->scheduled_time ? $lead->scheduled_time : null;
                
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'due_date' => $dueDate ? $dueDate->format('Y-m-d') : null,
                    'due_date_formatted' => $dueDate ? $dueDate->format('l, F d, Y') : null,
                    'event_type' => $lead ? 'Meeting' : 'Task',
                    'scheduled_time' => $scheduledTime ? \Carbon\Carbon::parse($scheduledTime)->format('h:i A') : null,
                    'lead_id' => $task->lead_id,
                ];
            })->values()->toArray()
        ];
    })->values()->toArray();
    
    return view('dashboard', compact('tasks', 'selectedDate', 'tasksForJs'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Management Routes - Permission based
    Route::middleware(['permission:view users'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
    
    Route::middleware(['permission:create users'])->group(function () {
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
    
    Route::middleware(['permission:edit users'])->group(function () {
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
    });
    
    Route::middleware(['permission:delete users'])->group(function () {
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
    // Role Management Routes - Permission based
    Route::middleware(['permission:view roles'])->group(function () {
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    });
    
    Route::middleware(['permission:create roles'])->group(function () {
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    });
    
    Route::middleware(['permission:edit roles'])->group(function () {
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });
    
    Route::middleware(['permission:delete roles'])->group(function () {
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
    
    // Admin Routes - Require Admin or Super Admin role
    Route::middleware(['role:Admin|Super Admin'])->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
        
        // Machine Category Routes
        Route::resource('machine-categories', MachineCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Seller Routes
        Route::resource('sellers', SellerController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // PI Layout Routes (excluding index - use admin settings page instead)
        Route::resource('pi-layouts', \App\Http\Controllers\PILayoutController::class)
            ->except(['index'])
            ->parameters(['pi-layouts' => 'piLayout']);
        Route::get('pi-layouts/{piLayout}/preview', [\App\Http\Controllers\PILayoutController::class, 'preview'])
            ->name('pi-layouts.preview');
        
        // Country Routes
        Route::resource('countries', CountryController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Brand Routes
        Route::resource('brands', BrandController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Model Routes
        Route::resource('machine-models', MachineModelController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Size Routes
        Route::resource('machine-sizes', MachineSizeController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Flange Size Routes
        Route::resource('flange-sizes', FlangeSizeController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Feeder Routes
        Route::resource('feeders', FeederController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Feeder Brand Routes
        Route::post('/feeder-brands', [FeederBrandController::class, 'store'])->name('feeder-brands.store');
        
        // Machine Hook Routes
        Route::resource('machine-hooks', MachineHookController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Spare Routes
        Route::resource('spares', \App\Http\Controllers\SpareController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/spares/import', [\App\Http\Controllers\SpareController::class, 'showImport'])->name('spares.show-import');
        Route::post('/spares/import', [\App\Http\Controllers\SpareController::class, 'import'])->name('spares.import');
        Route::get('/spares/download-template', [\App\Http\Controllers\SpareController::class, 'downloadTemplate'])->name('spares.download-template');
        
        // Color Routes
        Route::resource('colors', ColorController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Nozzle Routes
        Route::resource('machine-nozzles', MachineNozzleController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Dropin Routes
        Route::resource('machine-dropins', MachineDropinController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Beam Routes
        Route::resource('machine-beams', MachineBeamController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Cloth Roller Routes
        Route::resource('machine-cloth-rollers', MachineClothRollerController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Software Routes
        Route::resource('machine-softwares', MachineSoftwareController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // HSN Code Routes
        Route::resource('hsn-codes', HsnCodeController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // WIR Routes
        Route::resource('wirs', WirController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Shaft Routes
        Route::resource('machine-shafts', MachineShaftController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Lever Routes
        Route::resource('machine-levers', MachineLeverController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Chain Routes
        Route::resource('machine-chains', MachineChainController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine Heald Wire Routes
        Route::resource('machine-heald-wires', MachineHealdWireController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Machine E-Read Routes
        Route::resource('machine-e-reads', MachineEReadController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Delivery Term Routes
        Route::resource('delivery-terms', DeliveryTermController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Lead Management Routes (Admin only for setup)
        Route::resource('businesses', BusinessController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('states', StateController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('cities', CityController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('areas', AreaController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('statuses', StatusController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Contract Routes (moved outside role middleware, will be permission-based)
        
        // Contract Approval Routes (with permissions)
        Route::middleware(['permission:view contract approvals'])->group(function () {
            Route::get('/contracts/pending-approval', [ContractController::class, 'pendingApproval'])->name('contracts.pending-approval');
        });
        Route::middleware(['permission:approve contracts'])->group(function () {
            Route::post('/contracts/{contract}/approve', [ContractController::class, 'approve'])->name('contracts.approve');
        });
        Route::middleware(['permission:reject contracts'])->group(function () {
            Route::post('/contracts/{contract}/reject', [ContractController::class, 'reject'])->name('contracts.reject');
        });
        
        // Business Firm Routes
        Route::resource('business-firms', BusinessFirmController::class)->only(['index', 'store', 'update', 'destroy']);

        // Admin Settings
        Route::get('/admin/settings', [SettingController::class, 'edit'])
            ->name('settings.edit')
            ->middleware('permission:view settings');
        Route::post('/admin/settings', [SettingController::class, 'update'])
            ->name('settings.update')
            ->middleware('permission:edit settings');
        Route::get('/admin/contract-details-settings', [SettingController::class, 'contractDetails'])
            ->name('settings.contract-details')
            ->middleware('permission:view settings');
        Route::post('/admin/contract-details-settings', [SettingController::class, 'updateContractDetails'])
            ->name('settings.update-contract-details')
            ->middleware('permission:edit settings');
        Route::get('/admin/pi-layouts-settings', [SettingController::class, 'piLayouts'])
            ->name('settings.pi-layouts')
            ->middleware('permission:view settings');
        Route::get('/admin/port-of-destinations-settings', [PortOfDestinationController::class, 'index'])
            ->name('settings.port-of-destinations')
            ->middleware('permission:view settings');
        Route::get('/admin/port-of-destinations', [PortOfDestinationController::class, 'index'])
            ->name('port-of-destinations.index')
            ->middleware('permission:view settings');
        Route::post('/admin/port-of-destinations', [PortOfDestinationController::class, 'store'])
            ->name('port-of-destinations.store')
            ->middleware('permission:edit settings');
        Route::put('/admin/port-of-destinations/{portOfDestination}', [PortOfDestinationController::class, 'update'])
            ->name('port-of-destinations.update')
            ->middleware('permission:edit settings');
        Route::delete('/admin/port-of-destinations/{portOfDestination}', [PortOfDestinationController::class, 'destroy'])
            ->name('port-of-destinations.destroy')
            ->middleware('permission:edit settings');
    });
    
    // Lead Management Routes - Permission based
    // Note: create route must come before {lead} route to avoid route conflicts
    Route::middleware(['permission:create leads'])->group(function () {
        Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
        Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    });
    
    Route::middleware(['permission:view leads'])->group(function () {
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    });
    
    Route::middleware(['permission:edit leads'])->group(function () {
        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    });
    
    Route::middleware(['permission:delete leads'])->group(function () {
        Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    });
    
    Route::middleware(['permission:convert contract'])->group(function () {
        Route::get('/leads/{lead}/convert-to-contract', [LeadController::class, 'convertToContract'])->name('leads.convert-to-contract');
        Route::post('/leads/{lead}/convert-to-contract', [LeadController::class, 'storeContract'])->name('leads.store-contract');
    });
    
    // Customer Routes - Show approved contracts as customers
    Route::middleware(['permission:view customers'])->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    });
    
    Route::middleware(['permission:delete customers'])->group(function () {
        Route::delete('/customers/{contract}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });
    
    // Contract Routes - Permission based (moved from Admin role group)
    // Users with "convert contract" or "view contract approvals" can view contracts
    Route::middleware(['permission:view contract approvals|convert contract'])->group(function () {
        Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::get('/contracts/over-invoice', [ContractController::class, 'overInvoice'])->name('contracts.over-invoice');
        Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
        Route::get('/contracts/{contract}/download-pdf', [ContractController::class, 'downloadPdf'])->name('contracts.download-pdf');
        Route::get('/machine-statuses', [MachineStatusController::class, 'index'])->name('machine-statuses.index');
        Route::get('/machine-statuses/create', [MachineStatusController::class, 'create'])->name('machine-statuses.create');
        Route::post('/machine-statuses', [MachineStatusController::class, 'store'])->name('machine-statuses.store');
        Route::get('/machine-statuses/get-pis', [MachineStatusController::class, 'getPINumbersBySalesManager'])->name('machine-statuses.get-pis');
        Route::get('/machine-statuses/get-contracts', [MachineStatusController::class, 'getContractsBySalesManager'])->name('machine-statuses.get-contracts');
    });
    
    // Users with "convert contract" or "view contract approvals" can edit contracts they created
    Route::middleware(['permission:view contract approvals|convert contract'])->group(function () {
        Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
        Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
        Route::get('/contracts/{contract}/signature', [ContractController::class, 'signature'])->name('contracts.signature');
        Route::post('/contracts/{contract}/signature', [ContractController::class, 'storeSignature'])->name('contracts.store-signature');
    });
    
    // Only users with "view contract approvals" can delete contracts
    Route::middleware(['permission:view contract approvals'])->group(function () {
        Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
    });
    
    // Proforma Invoice Routes
    Route::middleware(['permission:view proforma invoices|create proforma invoices'])->group(function () {
        Route::get('/proforma-invoices/create', [ProformaInvoiceController::class, 'create'])->name('proforma-invoices.create');
    });
    
    Route::middleware(['permission:view proforma invoices'])->group(function () {
        Route::get('/proforma-invoices', [ProformaInvoiceController::class, 'index'])->name('proforma-invoices.index');
        Route::get('/proforma-invoices/{proformaInvoice}', [ProformaInvoiceController::class, 'show'])->name('proforma-invoices.show');
        Route::get('/proforma-invoices/{proformaInvoice}/download-pdf', [ProformaInvoiceController::class, 'downloadPdf'])->name('proforma-invoices.download-pdf');
        Route::get('/proforma-invoices-delivery-details', [ProformaInvoiceController::class, 'deliveryDetailsIndex'])->name('proforma-invoices.delivery-details-index');
    });
    
    Route::middleware(['permission:create proforma invoices'])->group(function () {
        Route::post('/proforma-invoices', [ProformaInvoiceController::class, 'store'])->name('proforma-invoices.store');
        Route::get('/contracts/{contract}/contract-details', [ProformaInvoiceController::class, 'getContractDetails'])->name('proforma-invoices.contract-details');
    });
    
    Route::middleware(['permission:edit proforma invoices'])->group(function () {
        Route::get('/proforma-invoices/{proformaInvoice}/edit', [ProformaInvoiceController::class, 'edit'])->name('proforma-invoices.edit');
        Route::put('/proforma-invoices/{proformaInvoice}', [ProformaInvoiceController::class, 'update'])->name('proforma-invoices.update');
        Route::get('/proforma-invoices/{proformaInvoice}/delivery-details', [ProformaInvoiceController::class, 'deliveryDetails'])->name('proforma-invoices.delivery-details');
        Route::post('/proforma-invoices/{proformaInvoice}/delivery-details', [ProformaInvoiceController::class, 'storeDeliveryDetails'])->name('proforma-invoices.store-delivery-details');
    });
    
    Route::middleware(['permission:delete proforma invoices'])->group(function () {
        Route::delete('/proforma-invoices/{proformaInvoice}', [ProformaInvoiceController::class, 'destroy'])->name('proforma-invoices.destroy');
    });
    
    // Pre Erection Routes
    Route::middleware(['permission:view proforma invoices|edit proforma invoices'])->group(function () {
        Route::get('/pre-erection', [\App\Http\Controllers\PreErectionController::class, 'index'])->name('pre-erection.index');
        Route::get('/pre-erection/get-pis', [\App\Http\Controllers\PreErectionController::class, 'getPINumbersBySalesManager'])->name('pre-erection.get-pis');
        Route::get('/pre-erection/get-customers', [\App\Http\Controllers\PreErectionController::class, 'getCustomersBySalesManager'])->name('pre-erection.get-customers');
    });
    
    Route::middleware(['permission:edit proforma invoices'])->group(function () {
        Route::get('/pre-erection/{proformaInvoice}', [\App\Http\Controllers\PreErectionController::class, 'show'])->name('pre-erection.show');
        Route::post('/pre-erection/{proformaInvoice}', [\App\Http\Controllers\PreErectionController::class, 'store'])->name('pre-erection.store');
    });
    
    // MS Unloading Image Routes
    Route::middleware(['permission:view proforma invoices|edit proforma invoices'])->group(function () {
        Route::get('/ms-unloading-images', [\App\Http\Controllers\MsUnloadingImageController::class, 'index'])->name('ms-unloading-images.index');
        Route::get('/ms-unloading-images/get-pis', [\App\Http\Controllers\MsUnloadingImageController::class, 'getPINumbersBySalesManager'])->name('ms-unloading-images.get-pis');
        Route::get('/ms-unloading-images/get-customers', [\App\Http\Controllers\MsUnloadingImageController::class, 'getCustomersBySalesManager'])->name('ms-unloading-images.get-customers');
    });
    
    Route::middleware(['permission:edit proforma invoices'])->group(function () {
        Route::get('/ms-unloading-images/{proformaInvoice}', [\App\Http\Controllers\MsUnloadingImageController::class, 'show'])->name('ms-unloading-images.show');
        Route::post('/ms-unloading-images/{proformaInvoice}', [\App\Http\Controllers\MsUnloadingImageController::class, 'store'])->name('ms-unloading-images.store');
        Route::delete('/ms-unloading-images/{msUnloadingImage}', [\App\Http\Controllers\MsUnloadingImageController::class, 'destroy'])->name('ms-unloading-images.destroy');
    });
    
    // Machine Erection Routes
    Route::middleware(['permission:view proforma invoices|edit proforma invoices'])->group(function () {
        Route::get('/machine-erection', [\App\Http\Controllers\MachineErectionController::class, 'index'])->name('machine-erection.index');
        Route::get('/machine-erection/get-pis', [\App\Http\Controllers\MachineErectionController::class, 'getPINumbersBySalesManager'])->name('machine-erection.get-pis');
        Route::get('/machine-erection/get-customers', [\App\Http\Controllers\MachineErectionController::class, 'getCustomersBySalesManager'])->name('machine-erection.get-customers');
    });
    
    Route::middleware(['permission:edit proforma invoices'])->group(function () {
        Route::get('/machine-erection/{proformaInvoice}', [\App\Http\Controllers\MachineErectionController::class, 'show'])->name('machine-erection.show');
        Route::post('/machine-erection/{proformaInvoice}', [\App\Http\Controllers\MachineErectionController::class, 'store'])->name('machine-erection.store');
    });
    
    // IA Fitting Routes
    Route::middleware(['permission:view proforma invoices|edit proforma invoices'])->group(function () {
        Route::get('/ia-fitting', [\App\Http\Controllers\IAFittingController::class, 'index'])->name('ia-fitting.index');
        Route::get('/ia-fitting/get-pis', [\App\Http\Controllers\IAFittingController::class, 'getPINumbersBySalesManager'])->name('ia-fitting.get-pis');
        Route::get('/ia-fitting/get-customers', [\App\Http\Controllers\IAFittingController::class, 'getCustomersBySalesManager'])->name('ia-fitting.get-customers');
    });
    
    Route::middleware(['permission:edit proforma invoices'])->group(function () {
        Route::get('/ia-fitting/{proformaInvoice}', [\App\Http\Controllers\IAFittingController::class, 'show'])->name('ia-fitting.show');
        Route::post('/ia-fitting/{proformaInvoice}', [\App\Http\Controllers\IAFittingController::class, 'store'])->name('ia-fitting.store');
    });
    
    // Damage Detail Routes - Order matters! More specific routes must come first
    Route::middleware(['permission:view proforma invoices|edit proforma invoices'])->group(function () {
        // Static routes first
        Route::get('/damage-details', [\App\Http\Controllers\DamageDetailController::class, 'index'])->name('damage-details.index');
        Route::get('/damage-details/get-pis', [\App\Http\Controllers\DamageDetailController::class, 'getPINumbersBySalesManager'])->name('damage-details.get-pis');
        Route::get('/damage-details/get-customers', [\App\Http\Controllers\DamageDetailController::class, 'getCustomersBySalesManager'])->name('damage-details.get-customers');
        
        // Specific routes with /edit and /image must come before generic routes
        Route::middleware(['permission:edit proforma invoices'])->group(function () {
            Route::get('/damage-details/{damageDetail}/edit', [\App\Http\Controllers\DamageDetailController::class, 'edit'])->name('damage-details.edit');
            Route::put('/damage-details/{damageDetail}', [\App\Http\Controllers\DamageDetailController::class, 'update'])->name('damage-details.update');
            Route::delete('/damage-details/{damageDetail}', [\App\Http\Controllers\DamageDetailController::class, 'destroy'])->name('damage-details.destroy');
            Route::delete('/damage-details-image/{damageImage}', [\App\Http\Controllers\DamageDetailController::class, 'destroyImage'])->name('damage-details.destroy-image');
        });
        
        // Generic routes come last
        Route::get('/damage-details/{proformaInvoice}', [\App\Http\Controllers\DamageDetailController::class, 'show'])->name('damage-details.show');
        Route::middleware(['permission:edit proforma invoices'])->group(function () {
            Route::post('/damage-details/{proformaInvoice}', [\App\Http\Controllers\DamageDetailController::class, 'store'])->name('damage-details.store');
        });
    });
    
    // Serial Number Routes
    Route::middleware(['permission:view proforma invoices|edit proforma invoices'])->group(function () {
        Route::get('/serial-numbers', [\App\Http\Controllers\SerialNumberController::class, 'index'])->name('serial-numbers.index');
        Route::get('/serial-numbers/get-pis', [\App\Http\Controllers\SerialNumberController::class, 'getPINumbersBySalesManager'])->name('serial-numbers.get-pis');
        Route::get('/serial-numbers/get-customers', [\App\Http\Controllers\SerialNumberController::class, 'getCustomersBySalesManager'])->name('serial-numbers.get-customers');
    });
    
    Route::middleware(['permission:edit proforma invoices'])->group(function () {
        Route::get('/serial-numbers/{proformaInvoice}', [\App\Http\Controllers\SerialNumberController::class, 'show'])->name('serial-numbers.show');
        Route::post('/serial-numbers/{proformaInvoice}', [\App\Http\Controllers\SerialNumberController::class, 'store'])->name('serial-numbers.store');
    });
    
    // Payment Routes
    Route::middleware(['permission:view proforma invoices|view contract approvals'])->group(function () {
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/collect-payment', [PaymentController::class, 'collectPayment'])->name('payments.collect-payment');
        Route::get('/payments/return-payment', [PaymentController::class, 'returnPayment'])->name('payments.return-payment');
        Route::get('/payments/get-contracts', [PaymentController::class, 'getContractsBySalesManager'])->name('payments.get-contracts');
        Route::get('/payments/get-proforma-invoices', [PaymentController::class, 'getProformaInvoicesBySalesManager'])->name('payments.get-proforma-invoices');
        Route::get('/payments/get-sellers-by-country', [PaymentController::class, 'getSellersByCountry'])->name('payments.get-sellers-by-country');
        Route::get('/payments/get-bank-details-by-seller', [PaymentController::class, 'getBankDetailsBySeller'])->name('payments.get-bank-details-by-seller');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::get('/payments/{payment}/download-pdf', [PaymentController::class, 'downloadPdf'])->name('payments.download-pdf');
    });
    
    // Purchase Order Routes
    Route::middleware(['permission:view proforma invoices|view contract approvals'])->group(function () {
        Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::get('/purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
        Route::put('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
        Route::delete('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');
        Route::get('/purchase-orders/get-first-payment/{proformaInvoiceId}', [PurchaseOrderController::class, 'getFirstPaymentTransaction'])->name('purchase-orders.get-first-payment');
        Route::delete('/purchase-orders/attachments/{attachment}', [PurchaseOrderController::class, 'deleteAttachment'])->name('purchase-orders.delete-attachment');
    });
    
    // Lead helper routes (available to anyone with view leads permission)
    Route::middleware(['permission:view leads|create leads|edit leads'])->group(function () {
        Route::get('/leads/cities/{state_id}', [LeadController::class, 'getCities'])->name('leads.cities');
        Route::get('/leads/areas/{city_id}', [LeadController::class, 'getAreas'])->name('leads.areas');
        Route::get('/leads/machine-models/{brand_id}', [LeadController::class, 'getMachineModels'])->name('leads.machine-models');
        Route::get('/leads/category-items/{category_id}', [LeadController::class, 'getCategoryItems'])->name('leads.category-items');
    });
    
    // Permission-based Routes Examples
    Route::middleware(['permission:view reports'])->group(function () {
        Route::get('/reports', function () {
            return view('reports.index');
        })->name('reports.index');
    });
    
    // Tasks Routes - Available to all authenticated users
    Route::resource('tasks', TaskController::class);
});

require __DIR__.'/auth.php';
