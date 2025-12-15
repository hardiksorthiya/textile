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
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\BusinessFirmController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Admin Routes - Require Admin or Super Admin role
    Route::middleware(['role:Admin|Super Admin'])->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
        
        // User Management Routes
        Route::resource('users', UserController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
        
        // Role Management Routes
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        
        // Machine Category Routes
        Route::resource('machine-categories', MachineCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        
        // Seller Routes
        Route::resource('sellers', SellerController::class)->only(['index', 'store', 'update', 'destroy']);
        
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
        
        // Lead Management Routes
        Route::resource('businesses', BusinessController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('states', StateController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('cities', CityController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('areas', AreaController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('statuses', StatusController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('leads', LeadController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('/leads/{lead}/convert-to-contract', [LeadController::class, 'convertToContract'])->name('leads.convert-to-contract');
        Route::post('/leads/{lead}/convert-to-contract', [LeadController::class, 'storeContract'])->name('leads.store-contract');
        Route::get('/leads/cities/{state_id}', [LeadController::class, 'getCities'])->name('leads.cities');
        Route::get('/leads/areas/{city_id}', [LeadController::class, 'getAreas'])->name('leads.areas');
        Route::get('/leads/machine-models/{brand_id}', [LeadController::class, 'getMachineModels'])->name('leads.machine-models');
        Route::get('/leads/category-items/{category_id}', [LeadController::class, 'getCategoryItems'])->name('leads.category-items');
        
        // Business Firm Routes
        Route::resource('business-firms', BusinessFirmController::class)->only(['index', 'store', 'update', 'destroy']);

        // Admin Settings
        Route::get('/admin/settings', [SettingController::class, 'edit'])
            ->name('settings.edit')
            ->middleware('permission:view settings');
        Route::post('/admin/settings', [SettingController::class, 'update'])
            ->name('settings.update')
            ->middleware('permission:edit settings');
    });
    
    // Permission-based Routes Examples
    Route::middleware(['permission:view reports'])->group(function () {
        Route::get('/reports', function () {
            return view('reports.index');
        })->name('reports.index');
    });
});

require __DIR__.'/auth.php';
