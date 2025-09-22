<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssetActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    /**
     * Get global activity feed.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('limit', 20);
        $page = $request->get('page', 1);

        $query = AssetActivity::with(['user', 'trackable'])
            ->latest();

        // Filter by activity type
        if ($request->has('type') && !empty($request->type)) {
            $query->where('activity_type', $request->type);
        }

        // Filter by user
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by model type
        if ($request->has('model_type') && !empty($request->model_type)) {
            $query->where('trackable_type', $request->model_type);
        }

        // Date range filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->where('created_at', '<=', $request->to_date);
        }

        // Search in descriptions
        if ($request->has('search') && !empty($request->search)) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $activities = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the data
        $activitiesData = $activities->map(function ($activity) {
            return $this->transformActivity($activity);
        });

        return response()->json([
            'data' => $activitiesData,
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'total_pages' => $activities->lastPage(),
                'has_more_pages' => $activities->hasMorePages(),
                'total_items' => $activities->total(),
                'per_page' => $activities->perPage(),
                'from' => $activities->firstItem(),
                'to' => $activities->lastItem(),
            ]
        ]);
    }

    /**
     * Get activities for a specific model.
     */
    public function forModel(Request $request, string $modelType, int $modelId): JsonResponse
    {
        $perPage = $request->get('limit', 20);
        $page = $request->get('page', 1);

        // Validate model type
        $allowedModels = [
            'Company' => \App\Models\Company::class,
            'ResearchItem' => \App\Models\ResearchItem::class,
            'BlogPost' => \App\Models\BlogPost::class,
            'Category' => \App\Models\Category::class,
            'Tag' => \App\Models\Tag::class,
        ];

        if (!isset($allowedModels[$modelType])) {
            return response()->json(['error' => 'Invalid model type'], 400);
        }

        $fullModelType = $allowedModels[$modelType];

        $query = AssetActivity::with(['user', 'trackable'])
            ->where('trackable_type', $fullModelType)
            ->where('trackable_id', $modelId)
            ->latest();

        // Filter by activity type
        if ($request->has('type') && !empty($request->type)) {
            $query->where('activity_type', $request->type);
        }

        $activities = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the data
        $activitiesData = $activities->map(function ($activity) {
            return $this->transformActivity($activity);
        });

        return response()->json([
            'data' => $activitiesData,
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'total_pages' => $activities->lastPage(),
                'has_more_pages' => $activities->hasMorePages(),
                'total_items' => $activities->total(),
                'per_page' => $activities->perPage(),
                'from' => $activities->firstItem(),
                'to' => $activities->lastItem(),
            ]
        ]);
    }

    /**
     * Get activities for a specific user.
     */
    public function forUser(Request $request, int $userId): JsonResponse
    {
        $perPage = $request->get('limit', 20);
        $page = $request->get('page', 1);

        $query = AssetActivity::with(['user', 'trackable'])
            ->where('user_id', $userId)
            ->latest();

        // Filter by activity type
        if ($request->has('type') && !empty($request->type)) {
            $query->where('activity_type', $request->type);
        }

        // Filter by model type
        if ($request->has('model_type') && !empty($request->model_type)) {
            $query->where('trackable_type', $request->model_type);
        }

        $activities = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the data
        $activitiesData = $activities->map(function ($activity) {
            return $this->transformActivity($activity);
        });

        return response()->json([
            'data' => $activitiesData,
            'pagination' => [
                'current_page' => $activities->currentPage(),
                'total_pages' => $activities->lastPage(),
                'has_more_pages' => $activities->hasMorePages(),
                'total_items' => $activities->total(),
                'per_page' => $activities->perPage(),
                'from' => $activities->firstItem(),
                'to' => $activities->lastItem(),
            ]
        ]);
    }

    /**
     * Get activity statistics.
     */
    public function stats(Request $request): JsonResponse
    {
        $days = $request->get('days', 7);
        $fromDate = now()->subDays($days);

        $stats = [
            'total_activities' => AssetActivity::where('created_at', '>=', $fromDate)->count(),
            'activities_by_type' => AssetActivity::where('created_at', '>=', $fromDate)
                ->selectRaw('activity_type, count(*) as count')
                ->groupBy('activity_type')
                ->pluck('count', 'activity_type'),
            'activities_by_model' => AssetActivity::where('created_at', '>=', $fromDate)
                ->selectRaw('trackable_type, count(*) as count')
                ->groupBy('trackable_type')
                ->pluck('count', 'trackable_type'),
            'most_active_users' => AssetActivity::where('created_at', '>=', $fromDate)
                ->with('user:id,name')
                ->selectRaw('user_id, count(*) as count')
                ->groupBy('user_id')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'user' => $item->user ? [
                            'id' => $item->user->id,
                            'name' => $item->user->name,
                        ] : null,
                        'count' => $item->count,
                    ];
                }),
        ];

        return response()->json($stats);
    }

    /**
     * Get latest companies based on their most recent activity.
     */
    public function latestCompanies(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);

        // Get the trackable_ids with their latest activity timestamps
        $latestActivityIds = AssetActivity::where('trackable_type', \App\Models\Company::class)
            ->select('trackable_id', DB::raw('MAX(created_at) as latest_activity'))
            ->groupBy('trackable_id')
            ->orderByDesc('latest_activity')
            ->limit($limit)
            ->pluck('trackable_id');

        // Now get the actual companies with proper relationships
        if ($latestActivityIds->isEmpty()) {
            return response()->json([]);
        }

        $companies = \App\Models\Company::with(['creator'])
            ->whereIn('id', $latestActivityIds)
            ->get()
            ->sortBy(function ($company) use ($latestActivityIds) {
                return $latestActivityIds->search($company->id);
            })
            ->values();

        return response()->json($companies);
    }

    /**
     * Get latest research items based on their most recent activity.
     */
    public function latestResearch(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);

        // Get the trackable_ids with their latest activity timestamps
        $latestActivityIds = AssetActivity::where('trackable_type', \App\Models\ResearchItem::class)
            ->select('trackable_id', DB::raw('MAX(created_at) as latest_activity'))
            ->groupBy('trackable_id')
            ->orderByDesc('latest_activity')
            ->limit($limit)
            ->pluck('trackable_id');

        // Now get the actual research items with proper relationships
        if ($latestActivityIds->isEmpty()) {
            return response()->json([]);
        }

        $research = \App\Models\ResearchItem::with(['user', 'company'])
            ->whereIn('id', $latestActivityIds)
            ->get()
            ->sortBy(function ($item) use ($latestActivityIds) {
                return $latestActivityIds->search($item->id);
            })
            ->values();

        return response()->json($research);
    }

    /**
     * Get latest blog posts (insights) based on their most recent activity.
     */
    public function latestInsights(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);

        // Get the trackable_ids with their latest activity timestamps
        $latestActivityIds = AssetActivity::where('trackable_type', \App\Models\BlogPost::class)
            ->select('trackable_id', DB::raw('MAX(created_at) as latest_activity'))
            ->groupBy('trackable_id')
            ->orderByDesc('latest_activity')
            ->limit($limit)
            ->pluck('trackable_id');

        // Now get the actual blog posts with proper relationships
        if ($latestActivityIds->isEmpty()) {
            return response()->json([]);
        }

        $insights = \App\Models\BlogPost::with(['user'])
            ->whereIn('id', $latestActivityIds)
            ->get()
            ->sortBy(function ($post) use ($latestActivityIds) {
                return $latestActivityIds->search($post->id);
            })
            ->values();

        return response()->json($insights);
    }

    /**
     * Transform activity data for API response.
     */
    protected function transformActivity(AssetActivity $activity): array
    {
        return [
            'id' => $activity->id,
            'activity_type' => $activity->activity_type,
            'field_name' => $activity->field_name,
            'description' => $activity->description,
            'old_value' => $activity->old_value,
            'new_value' => $activity->new_value,
            'metadata' => $activity->metadata,
            'icon' => $activity->icon,
            'color' => $activity->color,
            'user' => $activity->user ? [
                'id' => $activity->user->id,
                'name' => $activity->user->name,
            ] : null,
            'trackable' => $activity->trackable ? [
                'type' => class_basename($activity->trackable),
                'id' => $activity->trackable->id,
                'title' => $activity->trackable->getActivityTitle(),
            ] : null,
            'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $activity->created_at->diffForHumans(),
        ];
    }
}