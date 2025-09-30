<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Models\Company;
use App\Models\ResearchItem;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResearchItemController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index(Request $request): JsonResponse
    {
        // Check if user has permission to view research items
        if (!auth()->user()->can('view research items')) {
            return response()->json(['message' => 'You do not have permission to view research items.'], 403);
        }

        // Pagination parameters
        $perPage = $request->get('limit', 5);
        $page = $request->get('page', 1);

        $query = ResearchItem::query()
            ->with(['company', 'category', 'tags', 'user']);

        // If user is authenticated, show their own items + public items
        // If user is not authenticated, show only public items
        if (auth()->check()) {
            $query->where(function ($q) {
                $q->where('user_id', auth()->id())
                    ->orWhere('visibility', 'public');
            });
        } else {
            $query->where('visibility', 'public');
        }

        // Filter by company if provided
        if ($request->has('company_id') && ! empty($request->company_id)) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by tags if provided
        if ($request->has('tag_ids') && ! empty($request->tag_ids)) {
            $tagIds = is_array($request->tag_ids) ? $request->tag_ids : [$request->tag_ids];
            $query->whereHas('tags', function ($tagQuery) use ($tagIds) {
                $tagQuery->whereIn('tags.id', $tagIds);
            });
        }

        // Filter by category if provided
        if ($request->has('category_id') && ! empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        // Enhanced search functionality (includes company names and tags)
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%'.$searchTerm.'%')
                    ->orWhere('content', 'like', '%'.$searchTerm.'%')
                    ->orWhereHas('company', function ($companyQuery) use ($searchTerm) {
                        $companyQuery->where('name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('ticker', 'like', '%'.$searchTerm.'%');
                    })
                    ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                        $tagQuery->where('name', 'like', '%'.$searchTerm.'%');
                    });
            });
        }

        $researchItems = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        // Transform the data for the frontend
        $researchData = $researchItems->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'content' => $item->content,
                'visibility' => $item->visibility,
                'company' => $item->company ? [
                    'id' => $item->company->id,
                    'name' => $item->company->name,
                    'ticker' => $item->company->ticker,
                ] : null,
                'category' => $item->category ? [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                    'color' => $item->category->color,
                ] : null,
                'tags' => $item->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'color' => $tag->color,
                    ];
                }),
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // Return paginated response with metadata
        return response()->json([
            'data' => $researchData,
            'pagination' => [
                'current_page' => $researchItems->currentPage(),
                'total_pages' => $researchItems->lastPage(),
                'has_more_pages' => $researchItems->hasMorePages(),
                'total_items' => $researchItems->total(),
                'per_page' => $researchItems->perPage(),
                'from' => $researchItems->firstItem(),
                'to' => $researchItems->lastItem(),
            ],
        ]);
    }

    public function store(FileUploadRequest $request): JsonResponse
    {
        // Check if user has permission to create research items
        if (!auth()->user()->can('create research items')) {
            return response()->json(['message' => 'You do not have permission to create research items.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'visibility' => 'required|in:public,team,private',
            'ai_synopsis' => 'nullable|string',
            'source_date' => 'nullable|date',
            // Support legacy existing file formats for compatibility
            'existing_file_ids' => 'nullable|array',
            'existing_file_ids.*' => 'integer|exists:assets,id',
            'existing_files' => 'nullable|array',
            'existing_files.*' => 'integer|exists:assets,id',
            'existing_attachment_ids' => 'nullable|array',
            'existing_attachment_ids.*' => 'integer|exists:assets,id',
        ]);

        $validated['user_id'] = auth()->id();

        // Convert empty string category_id to null
        if (isset($validated['category_id']) && $validated['category_id'] === '') {
            $validated['category_id'] = null;
        }

        $researchItem = ResearchItem::create($validated);

        // Attach tags if provided
        if (isset($validated['tag_ids'])) {
            $researchItem->tags()->sync($validated['tag_ids']);
        }

        // Handle file uploads using centralized service
        $attachmentResults = $this->fileUploadService->handleUploads($researchItem, $request);

        $researchItem->load(['company', 'category', 'tags', 'user', 'assets', 'directAssets']);

        return response()->json([
            'id' => $researchItem->id,
            'title' => $researchItem->title,
            'content' => $researchItem->content,
            'visibility' => $researchItem->visibility,
            'company' => $researchItem->company ? [
                'id' => $researchItem->company->id,
                'name' => $researchItem->company->name,
                'ticker' => $researchItem->company->ticker,
            ] : null,
            'category' => $researchItem->category ? [
                'id' => $researchItem->category->id,
                'name' => $researchItem->category->name,
                'color' => $researchItem->category->color,
            ] : null,
            'tags' => $researchItem->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ];
            }),
            'attachments' => $researchItem->getFormattedAttachments(),
            'created_at' => $researchItem->created_at->format('Y-m-d H:i:s'),
            'attachment_results' => $attachmentResults,
        ], 201);
    }

    public function show(ResearchItem $researchItem): JsonResponse
    {
        // Check if user has permission to view research items
        if (!auth()->user()->can('view research items')) {
            return response()->json(['message' => 'You do not have permission to view research items.'], 403);
        }

        // Check visibility permissions
        if (auth()->check()) {
            // If authenticated, allow viewing own items + public items
            if ($researchItem->user_id !== auth()->id() && $researchItem->visibility !== 'public') {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        } else {
            // If not authenticated, only allow public items
            if ($researchItem->visibility !== 'public') {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $researchItem->load(['company', 'category', 'tags', 'user', 'assets', 'directAssets']);

        return response()->json([
            'id' => $researchItem->id,
            'title' => $researchItem->title,
            'content' => $researchItem->content,
            'visibility' => $researchItem->visibility,
            'company' => $researchItem->company ? [
                'id' => $researchItem->company->id,
                'name' => $researchItem->company->name,
                'ticker' => $researchItem->company->ticker,
            ] : null,
            'category' => $researchItem->category ? [
                'id' => $researchItem->category->id,
                'name' => $researchItem->category->name,
                'color' => $researchItem->category->color,
            ] : null,
            'tags' => $researchItem->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ];
            }),
            'attachments' => $researchItem->getFormattedAttachments(),
            'user' => $researchItem->user ? [
                'id' => $researchItem->user->id,
                'name' => $researchItem->user->name,
            ] : null,
            'created_at' => $researchItem->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $researchItem->updated_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function update(FileUploadRequest $request, ResearchItem $researchItem): JsonResponse
    {
        try {
            \Log::info('Update research item started', [
                'research_item_id' => $researchItem->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);

            // Check permissions: admin can edit all, users can only edit their own research items
            $user = auth()->user();
            if (!$user->hasRole('admin') && !$user->can('edit research items')) {
                return response()->json(['message' => 'You do not have permission to edit research items.'], 403);
            }

            // Non-admin users can only edit research items they created
            if (!$user->hasRole('admin') && $researchItem->user_id !== $user->id) {
                return response()->json(['message' => 'You can only edit research items that you created.'], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'company_id' => 'required|exists:companies,id',
                'category_id' => 'nullable|exists:categories,id',
                'tag_ids' => 'nullable|array',
                'tag_ids.*' => 'exists:tags,id',
                'visibility' => 'required|in:public,team,private',
                'ai_synopsis' => 'nullable|string',
                'source_date' => 'nullable|date',
                // Support legacy existing file formats for compatibility
                'existing_file_ids' => 'nullable|array',
                'existing_file_ids.*' => 'integer|exists:assets,id',
                'existing_files' => 'nullable|array',
                'existing_files.*' => 'integer|exists:assets,id',
                'existing_attachment_ids' => 'nullable|array',
                'existing_attachment_ids.*' => 'integer|exists:assets,id',
            ]);

            // Convert empty string category_id to null
            if (isset($validated['category_id']) && $validated['category_id'] === '') {
                $validated['category_id'] = null;
            }

            // Filter out non-database fields before updating
            $updateData = collect($validated)->only([
                'title', 'content', 'company_id', 'category_id', 'visibility', 'ai_synopsis', 'source_date',
            ])->toArray();

            $researchItem->update($updateData);

            // Sync tags if provided
            if (isset($validated['tag_ids'])) {
                $researchItem->tags()->sync($validated['tag_ids']);
            }

            // Handle file uploads using centralized service
            $attachmentResults = $this->fileUploadService->handleUploads($researchItem, $request);

            $researchItem->load(['company', 'category', 'tags', 'user', 'assets', 'directAssets']);

            return response()->json([
                'id' => $researchItem->id,
                'title' => $researchItem->title,
                'content' => $researchItem->content,
                'visibility' => $researchItem->visibility,
                'company' => $researchItem->company ? [
                    'id' => $researchItem->company->id,
                    'name' => $researchItem->company->name,
                    'ticker' => $researchItem->company->ticker,
                ] : null,
                'category' => $researchItem->category ? [
                    'id' => $researchItem->category->id,
                    'name' => $researchItem->category->name,
                    'color' => $researchItem->category->color,
                ] : null,
                'tags' => $researchItem->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'color' => $tag->color,
                    ];
                }),
                'attachments' => $researchItem->getFormattedAttachments(),
                'created_at' => $researchItem->created_at->format('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Update research item failed', [
                'research_item_id' => $researchItem->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to update research item: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(ResearchItem $researchItem): JsonResponse
    {
        try {
            \Log::info('Starting research item deletion', [
                'research_item_id' => $researchItem->id,
                'user_id' => auth()->id(),
                'research_item_title' => $researchItem->title,
            ]);

            // Check permissions: admin can delete all, users can only delete their own research items
            $user = auth()->user();
            if (!$user->hasRole('admin') && !$user->can('delete research items')) {
                return response()->json(['message' => 'You do not have permission to delete research items.'], 403);
            }

            // Non-admin users can only delete research items they created
            if (!$user->hasRole('admin') && $researchItem->user_id !== $user->id) {
                return response()->json(['message' => 'You can only delete research items that you created.'], 403);
            }

            \Log::info('Permissions validated, starting media cleanup', [
                'research_item_id' => $researchItem->id,
            ]);

            // Mark associated assets as orphaned before deletion
            try {
                $this->fileUploadService->removeMediaFromModel($researchItem);
                \Log::info('Media cleanup completed successfully', [
                    'research_item_id' => $researchItem->id,
                ]);
            } catch (\Exception $mediaException) {
                \Log::error('Media cleanup failed', [
                    'research_item_id' => $researchItem->id,
                    'error' => $mediaException->getMessage(),
                    'trace' => $mediaException->getTraceAsString(),
                ]);
                // Continue with deletion even if media cleanup fails
            }

            \Log::info('Starting research item deletion from database', [
                'research_item_id' => $researchItem->id,
            ]);

            $researchItem->delete();

            \Log::info('Research item deleted successfully', [
                'research_item_id' => $researchItem->id,
            ]);

            return response()->json(null, 204);

        } catch (\Exception $e) {
            \Log::error('Research item deletion failed', [
                'research_item_id' => $researchItem->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to delete research item',
                'error' => $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ], 500);
        }
    }

    /**
     * Get available files that can be reused for research items
     */
    public function getAvailableFiles(Request $request): JsonResponse
    {
        \Log::info('getAvailableFiles called', [
            'user_id' => auth()->id(),
            'search' => $request->get('search'),
            'limit' => $request->get('limit'),
        ]);

        // Get all media files from research items and documents that the user has access to
        $userFiles = collect();

        // Get files from user's own research items
        if (auth()->check()) {
            $userResearchItems = ResearchItem::where('user_id', auth()->id())
                ->with(['assets', 'directAssets'])
                ->get();

            foreach ($userResearchItems as $item) {
                // Add direct assets (created specifically for this research item)
                foreach ($item->directAssets as $asset) {
                    $userFiles->push([
                        'id' => $asset->id,
                        'name' => $asset->title,
                        'file_name' => $asset->file_name,
                        'mime_type' => $asset->mime_type,
                        'size' => $asset->size,
                        'url' => $asset->url,
                        'source_type' => 'research_item',
                        'source_id' => $item->id,
                        'source_title' => $item->title,
                        'created_at' => $asset->created_at,
                        'attachment_type' => 'direct'
                    ]);
                }

                // Add symbolic link assets (referenced assets)
                foreach ($item->assets as $asset) {
                    $userFiles->push([
                        'id' => $asset->id,
                        'name' => $asset->title,
                        'file_name' => $asset->file_name,
                        'mime_type' => $asset->mime_type,
                        'size' => $asset->size,
                        'url' => $asset->url,
                        'source_type' => 'research_item',
                        'source_id' => $item->id,
                        'source_title' => $item->title,
                        'created_at' => $asset->created_at,
                        'attachment_type' => 'reference'
                    ]);
                }
            }

            // Get files from user's own documents
            $userDocuments = \App\Models\Document::where('user_id', auth()->id())
                ->with('assets')
                ->get();

            foreach ($userDocuments as $document) {
                foreach ($document->assets as $asset) {
                    $userFiles->push([
                        'id' => $asset->id,
                        'name' => $asset->title,
                        'file_name' => $asset->file_name,
                        'mime_type' => $asset->mime_type,
                        'size' => $asset->size,
                        'url' => $asset->url,
                        'source_type' => 'document',
                        'source_id' => $document->id,
                        'source_title' => $document->title,
                        'created_at' => $asset->created_at,
                    ]);
                }
            }
        }

        // Sort by most recent first
        $sortedFiles = $userFiles->sortByDesc('created_at')->values();

        \Log::info('Files found', [
            'total_files' => $sortedFiles->count(),
            'user_id' => auth()->id(),
        ]);

        // Apply search filter if provided
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = strtolower($request->search);
            $sortedFiles = $sortedFiles->filter(function ($file) use ($searchTerm) {
                return str_contains(strtolower($file['name']), $searchTerm) ||
                       str_contains(strtolower($file['file_name']), $searchTerm) ||
                       str_contains(strtolower($file['source_title']), $searchTerm);
            })->values();
        }

        // Apply pagination
        $perPage = $request->get('limit', 20);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;

        $paginatedFiles = $sortedFiles->slice($offset, $perPage)->values();

        return response()->json([
            'data' => $paginatedFiles,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_items' => $sortedFiles->count(),
                'total_pages' => ceil($sortedFiles->count() / $perPage),
                'has_more_pages' => $offset + $perPage < $sortedFiles->count(),
            ],
        ]);
    }

    /**
     * Link existing assets to a research item (symbolic links)
     */
    public function linkExistingFiles(Request $request, ResearchItem $researchItem): JsonResponse
    {
        $validated = $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'integer|exists:assets,id',
        ]);

        try {
            $linkedCount = 0;
            $alreadyLinkedCount = 0;

            foreach ($validated['asset_ids'] as $assetId) {
                $asset = \App\Models\Asset::findOrFail($assetId);

                // Check if user has access to this asset
                $hasAccess = false;

                // Check if user owns the asset directly
                if ($asset->user_id === auth()->id()) {
                    $hasAccess = true;
                }

                // Check if user owns the source model that created this asset
                if (!$hasAccess && $asset->source_type && $asset->source_id) {
                    if ($asset->source_type === 'research_item') {
                        $sourceItem = ResearchItem::find($asset->source_id);
                        $hasAccess = $sourceItem && $sourceItem->user_id === auth()->id();
                    } elseif ($asset->source_type === 'document') {
                        $sourceDocument = \App\Models\Document::find($asset->source_id);
                        $hasAccess = $sourceDocument && $sourceDocument->user_id === auth()->id();
                    }
                }

                if (!$hasAccess) {
                    return response()->json([
                        'message' => 'You do not have access to one or more selected assets.',
                    ], 403);
                }

                // Check if already linked to avoid duplicates
                $exists = $researchItem->assets()->where('asset_id', $assetId)->exists();

                if (!$exists) {
                    // Create symbolic link via pivot table
                    $researchItem->assets()->attach($assetId);
                    $linkedCount++;
                } else {
                    $alreadyLinkedCount++;
                }
            }

            return response()->json([
                'message' => 'Assets linked successfully',
                'linked_count' => $linkedCount,
                'already_linked_count' => $alreadyLinkedCount,
                'total_requested' => count($validated['asset_ids']),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error linking existing assets to research item', [
                'research_item_id' => $researchItem->id,
                'asset_ids' => $validated['asset_ids'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to link assets: '.$e->getMessage(),
            ], 500);
        }
    }
}
