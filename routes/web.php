<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\BlogPostController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Main Investment Research Dashboard - accessible to all
Route::get('/', function () {
    $auth = auth()->check() ? [
        'user' => auth()->user()->load('roles', 'permissions')
    ] : ['user' => null];

    // Get recent published blog posts for community feed
    $recentPosts = \App\Models\BlogPost::where('status', 'published')
        ->with('user:id,name')
        ->latest('published_at')
        ->take(5)
        ->get(['id', 'title', 'slug', 'content', 'published_at', 'user_id']);

    return Inertia::render('InvestmentDashboard', [
        'auth' => $auth,
        'recentBlogPosts' => $recentPosts
    ]);
})->name('dashboard');

// Authenticated Dashboard - same as main but shows user context
Route::get('/dashboard', function () {
    // Get recent published blog posts for community feed
    $recentPosts = \App\Models\BlogPost::where('status', 'published')
        ->with('user:id,name')
        ->latest('published_at')
        ->take(5)
        ->get(['id', 'title', 'slug', 'content', 'published_at', 'user_id']);

    return Inertia::render('InvestmentDashboard', [
        'auth' => [
            'user' => auth()->user()->load('roles', 'permissions')
        ],
        'recentBlogPosts' => $recentPosts
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
    // Public endpoints (accessible to everyone)
    Route::get('companies', [CompanyController::class, 'index']);
    Route::get('quotes', [\App\Http\Controllers\Api\BlogPostController::class, 'quotes']);
    Route::get('git-info', [\App\Http\Controllers\GitInfoController::class, 'index']);

    // Public Categories and Tags (accessible to everyone for dashboard filtering)
    Route::get('categories', function() {
        return response()->json(\App\Models\Category::all(['id', 'name', 'color']));
    });
    Route::get('tags', function() {
        return response()->json(\App\Models\Tag::all(['id', 'name', 'color']));
    });

    // Public search endpoints (accessible to everyone, with different results based on auth status)
    Route::get('search', [\App\Http\Controllers\Api\SearchController::class, 'search']);
    Route::get('blog-posts/search', [\App\Http\Controllers\Api\BlogPostController::class, 'search']);

    // Public research item endpoints (accessible to everyone, with different results based on auth status)
    Route::get('research-items', [\App\Http\Controllers\Api\ResearchItemController::class, 'index']);
    Route::get('research-items/{research_item}', [\App\Http\Controllers\Api\ResearchItemController::class, 'show']);

    Route::middleware('auth')->group(function () {
        // Protected endpoints (require authentication)
        Route::post('companies', [CompanyController::class, 'store']);
        Route::get('companies/{company}', [CompanyController::class, 'show']);
        Route::put('companies/{company}', [CompanyController::class, 'update']);
        Route::delete('companies/{company}', [CompanyController::class, 'destroy']);
        Route::get('companies/{company}/blog-posts', [CompanyController::class, 'getBlogPosts']);

        // Bulk operations for companies
        Route::post('companies/deletion-impact', [CompanyController::class, 'deletionImpact']);
        Route::delete('companies/bulk', [CompanyController::class, 'bulkDestroy']);

        // Research Items (protected operations)
        Route::post('research-items', [\App\Http\Controllers\Api\ResearchItemController::class, 'store']);
        Route::put('research-items/{research_item}', [\App\Http\Controllers\Api\ResearchItemController::class, 'update']);
        Route::delete('research-items/{research_item}', [\App\Http\Controllers\Api\ResearchItemController::class, 'destroy']);

        // File management for research items
        Route::get('research-items/files/available', [\App\Http\Controllers\Api\ResearchItemController::class, 'getAvailableFiles']);
        Route::post('research-items/{research_item}/link-files', [\App\Http\Controllers\Api\ResearchItemController::class, 'linkExistingFiles']);

        // Documents (protected operations)
        Route::get('documents', [\App\Http\Controllers\Api\DocumentController::class, 'index']);
        Route::post('documents', [\App\Http\Controllers\Api\DocumentController::class, 'store']);
        Route::get('documents/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'show']);
        Route::put('documents/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'update']);
        Route::delete('documents/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'destroy']);

        // Assets (for direct creation and deletion)
        Route::post('assets', [\App\Http\Controllers\Api\AssetController::class, 'store']);
        Route::delete('assets/{asset}', [\App\Http\Controllers\Api\AssetController::class, 'destroy']);

        // Background Upload System
        Route::post('upload/file/{token}', [\App\Http\Controllers\Api\UploadController::class, 'uploadFile']);
        Route::post('upload/url/{token}', [\App\Http\Controllers\Api\UploadController::class, 'uploadUrl']);
        Route::get('upload/progress/{token}', [\App\Http\Controllers\Api\UploadController::class, 'getProgress']);
        Route::delete('upload/cancel/{token}', [\App\Http\Controllers\Api\UploadController::class, 'cancelUpload']);

        // Activity Tracking (protected operations)
        Route::get('activities', [\App\Http\Controllers\Api\ActivityController::class, 'index']);
        Route::get('activities/stats', [\App\Http\Controllers\Api\ActivityController::class, 'stats']);
        Route::get('activities/latest/companies', [\App\Http\Controllers\Api\ActivityController::class, 'latestCompanies']);
        Route::get('activities/latest/research', [\App\Http\Controllers\Api\ActivityController::class, 'latestResearch']);
        Route::get('activities/latest/insights', [\App\Http\Controllers\Api\ActivityController::class, 'latestInsights']);
        Route::get('activities/users/{user}', [\App\Http\Controllers\Api\ActivityController::class, 'forUser']);
        Route::get('activities/{modelType}/{modelId}', [\App\Http\Controllers\Api\ActivityController::class, 'forModel']);

        // URL Download Testing (protected)
        Route::get('test-download', function () {
            $downloadService = new \App\Services\UrlDownloadService();
            return response()->json($downloadService->testDownload());
        });

        Route::post('test-download-url', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'url' => 'required|url',
                'name' => 'nullable|string|max:255'
            ]);

            $downloadService = new \App\Services\UrlDownloadService();
            $result = $downloadService->downloadFile(
                $request->url,
                $request->name ?? 'Test Download'
            );

            if ($result) {
                $fileSize = filesize($result);
                $fileInfo = pathinfo($result);

                // Clean up test file
                unlink($result);

                return response()->json([
                    'success' => true,
                    'message' => 'Download successful',
                    'file_size' => $fileSize,
                    'extension' => $fileInfo['extension'] ?? null,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Download failed - check logs for details'
                ], 500);
            }
        });
    });
});

// Blog Routes - SPA-friendly (no separate create/edit pages)
Route::middleware('auth')->prefix('my-blog')->name('blog.')->group(function () {
    Route::get('/', [BlogPostController::class, 'index'])->name('index');
    Route::post('/', [BlogPostController::class, 'store'])->name('store');
    Route::put('/{blogPost}', [BlogPostController::class, 'update'])->name('update');
    Route::delete('/{blogPost}', [BlogPostController::class, 'destroy'])->name('destroy');
});

// Public Company Routes
Route::get('/companies', function () {
    return Inertia::render('CompanyListing', [
        'auth' => auth()->check() ? [
            'user' => auth()->user()->load('roles', 'permissions')
        ] : ['user' => null]
    ]);
})->name('companies.index');

Route::get('/companies/{ticker}', function ($ticker) {
    return Inertia::render('CompanyProfile', [
        'ticker' => $ticker,
        'tab' => request('tab', 'overview'),
        'auth' => auth()->check() ? [
            'user' => auth()->user()->load('roles', 'permissions')
        ] : ['user' => null]
    ]);
})->name('company.profile');

// Public Blog Routes
Route::get('/users/{username}/blog', [BlogPostController::class, 'userBlog'])->name('user.blog');
Route::get('/blog/{blogPost:slug}', [BlogPostController::class, 'show'])->name('blog.show');

require __DIR__.'/auth.php';
