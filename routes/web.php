<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\BlogPostController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Main Dashboard - redirects to default tab
Route::get('/', function () {
    return redirect()->route('dashboard.overview');
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard Tab Routes
Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Overview Tab (default)
    Route::get('/overview', function () {
        $recentPosts = \App\Models\BlogPost::where('status', 'published')
            ->with('user:id,name')
            ->latest('published_at')
            ->take(5)
            ->get(['id', 'title', 'slug', 'content', 'published_at', 'user_id']);

        return Inertia::render('Dashboard/Overview', [
            'auth' => [
                'user' => auth()->user()->load('roles', 'permissions')
            ],
            'recentBlogPosts' => $recentPosts
        ]);
    })->name('overview');

    // Companies Tab
    Route::get('/companies', function () {
        return Inertia::render('Dashboard/Companies', [
            'auth' => [
                'user' => auth()->user()->load('roles', 'permissions')
            ]
        ]);
    })->name('companies');

    // Research Tab
    Route::get('/research', function () {
        return Inertia::render('Dashboard/Research', [
            'auth' => [
                'user' => auth()->user()->load('roles', 'permissions')
            ]
        ]);
    })->name('research');

    // Analytics Tab
    Route::get('/analytics', function () {
        return Inertia::render('Dashboard/Analytics', [
            'auth' => [
                'user' => auth()->user()->load('roles', 'permissions')
            ]
        ]);
    })->name('analytics');

    // Watchlists Tab with optional slug parameter
    Route::get('/watchlists/{slug?}', function ($slug = null) {
        $data = [
            'auth' => [
                'user' => auth()->user()->load('roles', 'permissions')
            ]
        ];

        // If a slug is provided, load the specific watchlist
        if ($slug) {
            $watchlist = \App\Models\Watchlist::findBySlugForUser($slug, auth()->id());

            if ($watchlist) {
                $data['selectedWatchlistSlug'] = $slug;
            }
        }

        return Inertia::render('Dashboard/Watchlists', $data);
    })->name('watchlists');
});

// Authenticated Dashboard - redirects to main dashboard
Route::get('/dashboard', function () {
    return redirect()->route('dashboard');
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

// API Routes moved to routes/api.php to avoid Inertia middleware conflicts

// Blog Routes - SPA-friendly (no separate create/edit pages)
Route::middleware(['auth', 'verified'])->prefix('my-blog')->name('blog.')->group(function () {
    Route::get('/', [BlogPostController::class, 'index'])->name('index');
    Route::post('/', [BlogPostController::class, 'store'])->name('store');
    Route::put('/{blogPost}', [BlogPostController::class, 'update'])->name('update');
    Route::delete('/{blogPost}', [BlogPostController::class, 'destroy'])->name('destroy');
});

// Company Routes - require authentication
Route::get('/companies', function () {
    return Inertia::render('CompanyListing', [
        'auth' => [
            'user' => auth()->user()->load('roles', 'permissions')
        ]
    ]);
})->middleware(['auth', 'verified'])->name('companies.index');

Route::get('/companies/{ticker}', function ($ticker) {
    return Inertia::render('CompanyProfile', [
        'ticker' => $ticker,
        'tab' => request('tab', 'overview'),
        'auth' => [
            'user' => auth()->user()->load('roles', 'permissions')
        ]
    ]);
})->middleware(['auth', 'verified'])->name('company.profile');

// Blog Routes (require authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/users/{username}/blog', [BlogPostController::class, 'userBlog'])->name('user.blog');
    Route::get('/blog/{blogPost:slug}', [BlogPostController::class, 'show'])->name('blog.show');
});

// API Routes that need session authentication (moved from api.php)
Route::prefix('api')->middleware(['auth', 'verified'])->group(function () {
    // Application data endpoints
    Route::get('git-info', [\App\Http\Controllers\GitInfoController::class, 'index']);

    // Categories and Tags
    Route::get('categories', function() {
        return response()->json(\App\Models\Category::all(['id', 'name', 'color']));
    });

    // Tag management
    Route::get('tags', [\App\Http\Controllers\Api\TagController::class, 'index']);
    Route::post('tags', [\App\Http\Controllers\Api\TagController::class, 'store']);
    Route::get('tags/{tag}', [\App\Http\Controllers\Api\TagController::class, 'show']);
    Route::put('tags/{tag}', [\App\Http\Controllers\Api\TagController::class, 'update']);
    Route::delete('tags/{tag}', [\App\Http\Controllers\Api\TagController::class, 'destroy']);

    // Watchlist management
    Route::get('watchlists', [\App\Http\Controllers\Api\WatchlistController::class, 'index']);
    Route::post('watchlists', [\App\Http\Controllers\Api\WatchlistController::class, 'store']);
    Route::get('watchlists/{watchlist}', [\App\Http\Controllers\Api\WatchlistController::class, 'show']);
    Route::put('watchlists/{watchlist}', [\App\Http\Controllers\Api\WatchlistController::class, 'update']);
    Route::delete('watchlists/{watchlist}', [\App\Http\Controllers\Api\WatchlistController::class, 'destroy']);
    Route::post('watchlists/{watchlist}/companies', [\App\Http\Controllers\Api\WatchlistController::class, 'addCompany']);
    Route::delete('watchlists/{watchlist}/companies/{company}', [\App\Http\Controllers\Api\WatchlistController::class, 'removeCompany']);
    Route::post('watchlists/add-company', [\App\Http\Controllers\Api\WatchlistController::class, 'addCompanyToWatchlists']);

    // Search endpoints
    Route::get('search', [\App\Http\Controllers\Api\SearchController::class, 'search']);
    Route::get('blog-posts/search', [\App\Http\Controllers\Api\BlogPostController::class, 'search']);

    // Research item endpoints
    Route::get('research-items', [\App\Http\Controllers\Api\ResearchItemController::class, 'index']);
    Route::get('research-items/{research_item}', [\App\Http\Controllers\Api\ResearchItemController::class, 'show']);
    Route::post('research-items', [\App\Http\Controllers\Api\ResearchItemController::class, 'store']);
    Route::put('research-items/{research_item}', [\App\Http\Controllers\Api\ResearchItemController::class, 'update']);
    Route::delete('research-items/{research_item}', [\App\Http\Controllers\Api\ResearchItemController::class, 'destroy']);

    // File management for research items
    Route::get('research-items/files/available', [\App\Http\Controllers\Api\ResearchItemController::class, 'getAvailableFiles']);
    Route::post('research-items/{research_item}/link-files', [\App\Http\Controllers\Api\ResearchItemController::class, 'linkExistingFiles']);

    // Company endpoints
    Route::get('companies', [\App\Http\Controllers\Api\CompanyController::class, 'index']);
    Route::post('companies', [\App\Http\Controllers\Api\CompanyController::class, 'store']);

    // Bulk operations for companies (must be before parameterized routes)
    Route::post('companies/deletion-impact', [\App\Http\Controllers\Api\CompanyController::class, 'deletionImpact']);
    Route::delete('companies/bulk', [\App\Http\Controllers\Api\CompanyController::class, 'bulkDestroy']);

    Route::get('companies/{company}', [\App\Http\Controllers\Api\CompanyController::class, 'show']);
    Route::put('companies/{company}', [\App\Http\Controllers\Api\CompanyController::class, 'update']);
    Route::delete('companies/{company}', [\App\Http\Controllers\Api\CompanyController::class, 'destroy']);
    Route::get('companies/{company}/blog-posts', [\App\Http\Controllers\Api\CompanyController::class, 'getBlogPosts']);

    // Documents (protected operations)
    Route::get('documents', [\App\Http\Controllers\Api\DocumentController::class, 'index']);
    Route::post('documents', [\App\Http\Controllers\Api\DocumentController::class, 'store']);
    Route::get('documents/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'show']);
    Route::put('documents/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'update']);
    Route::delete('documents/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'destroy']);

    // Assets (for direct creation and deletion)
    Route::get('media/available', [\App\Http\Controllers\Api\AssetController::class, 'getAvailable']);
    Route::get('assets', [\App\Http\Controllers\Api\AssetController::class, 'index']);
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

    // Article Extraction (protected)
    Route::post('extract-article', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'url' => 'required|url|max:2048'
        ]);

        $extractionService = new \App\Services\ArticleExtractionService();
        $result = $extractionService->extractFromUrl($request->url);

        return response()->json($result, $result['success'] ? 200 : 400);
    });

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

require __DIR__.'/auth.php';
