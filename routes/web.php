<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\CompanyController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Main Investment Research Dashboard - accessible to all
Route::get('/', function () {
    $auth = auth()->check() ? [
        'user' => auth()->user()->load('roles', 'permissions')
    ] : ['user' => null];
    
    return Inertia::render('InvestmentDashboard', [
        'auth' => $auth
    ]);
})->name('dashboard');

// Authenticated Dashboard - same as main but shows user context
Route::get('/dashboard', function () {
    return Inertia::render('InvestmentDashboard', [
        'auth' => [
            'user' => auth()->user()->load('roles', 'permissions')
        ]
    ]);
})->middleware(['auth', 'verified'])->name('auth.dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('users', [AdminController::class, 'users'])->name('users');
    Route::get('roles', [AdminController::class, 'roles'])->name('roles');
    Route::get('permissions', [AdminController::class, 'permissions'])->name('permissions');
    
    // User management
    Route::put('users/{user}/roles', [AdminController::class, 'updateUserRoles'])->name('users.roles');
    Route::put('users/{user}/permissions', [AdminController::class, 'updateUserPermissions'])->name('users.permissions');
    
    // Role management
    Route::post('roles', [AdminController::class, 'storeRole'])->name('roles.store');
    Route::put('roles/{role}', [AdminController::class, 'updateRole'])->name('roles.update');
    Route::delete('roles/{role}', [AdminController::class, 'deleteRole'])->name('roles.destroy');
    
    // Permission management
    Route::post('permissions', [AdminController::class, 'storePermission'])->name('permissions.store');
    Route::put('permissions/{permission}', [AdminController::class, 'updatePermission'])->name('permissions.update');
    Route::delete('permissions/{permission}', [AdminController::class, 'deletePermission'])->name('permissions.destroy');
});

// API Routes
Route::prefix('api')->group(function () {
    Route::get('companies', [CompanyController::class, 'index']);
    Route::middleware('auth')->group(function () {
        Route::post('companies', [CompanyController::class, 'store']);
        Route::get('companies/{company}', [CompanyController::class, 'show']);
        Route::put('companies/{company}', [CompanyController::class, 'update']);
        Route::delete('companies/{company}', [CompanyController::class, 'destroy']);
        
        // Research Items
        Route::apiResource('research-items', \App\Http\Controllers\Api\ResearchItemController::class);
        
        // Categories and Tags (for research item creation)
        Route::get('categories', function() {
            return response()->json(\App\Models\Category::all(['id', 'name', 'color']));
        });
        Route::get('tags', function() {
            return response()->json(\App\Models\Tag::all(['id', 'name', 'color']));
        });
    });
});

require __DIR__.'/auth.php';
