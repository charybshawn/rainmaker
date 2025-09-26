<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Models\ResearchItem;
use App\Models\Company;
use App\Models\Category;
use App\Models\Tag;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResearchItemController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    public function index(Request $request): JsonResponse
    {
        // Pagination parameters
        $perPage = $request->get('limit', 5);
        $page = $request->get('page', 1);

        $query = ResearchItem::query()
            ->with(['company', 'category', 'tags', 'user']);

        // If user is authenticated, show their own items + public items
        // If user is not authenticated, show only public items
        if (auth()->check()) {
            $query->where(function($q) {
                $q->where('user_id', auth()->id())
                  ->orWhere('visibility', 'public');
            });
        } else {
            $query->where('visibility', 'public');
        }

        // Filter by company if provided
        if ($request->has('company_id') && !empty($request->company_id)) {
            $query->where('company_id', $request->company_id);
        }

        // Search functionality (enhanced to also search company names)
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('content', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('company', function($companyQuery) use ($searchTerm) {
                      $companyQuery->where('name', 'like', '%' . $searchTerm . '%')
                                  ->orWhere('ticker_symbol', 'like', '%' . $searchTerm . '%');
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
                    'ticker' => $item->company->ticker_symbol,
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
            ]
        ]);
    }

    public function store(FileUploadRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'visibility' => 'required|in:public,team,private',
            'ai_synopsis' => 'nullable|string',
            // Support legacy existing file formats for compatibility
            'existing_file_ids' => 'nullable|array',
            'existing_file_ids.*' => 'integer|exists:media,id',
            'existing_files' => 'nullable|array',
            'existing_files.*' => 'integer|exists:media,id',
            'existing_attachment_ids' => 'nullable|array',
            'existing_attachment_ids.*' => 'integer|exists:media,id',
        ]);

        $validated['user_id'] = auth()->id();

        $researchItem = ResearchItem::create($validated);

        // Attach tags if provided
        if (isset($validated['tag_ids'])) {
            $researchItem->tags()->sync($validated['tag_ids']);
        }

        // Handle file uploads using centralized service
        $attachmentResults = $this->fileUploadService->handleUploads($researchItem, $request);

        $researchItem->load(['company', 'category', 'tags', 'user', 'media']);

        return response()->json([
            'id' => $researchItem->id,
            'title' => $researchItem->title,
            'content' => $researchItem->content,
            'visibility' => $researchItem->visibility,
            'company' => $researchItem->company ? [
                'id' => $researchItem->company->id,
                'name' => $researchItem->company->name,
                'ticker' => $researchItem->company->ticker_symbol,
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

        $researchItem->load(['company', 'category', 'tags', 'user', 'media']);

        return response()->json([
            'id' => $researchItem->id,
            'title' => $researchItem->title,
            'content' => $researchItem->content,
            'visibility' => $researchItem->visibility,
            'company' => $researchItem->company ? [
                'id' => $researchItem->company->id,
                'name' => $researchItem->company->name,
                'ticker' => $researchItem->company->ticker_symbol,
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
                'request_data' => $request->all()
            ]);

            // Only allow updating own research items
            if ($researchItem->user_id !== auth()->id()) {
                return response()->json(['message' => 'Forbidden'], 403);
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
            // Support legacy existing file formats for compatibility
            'existing_file_ids' => 'nullable|array',
            'existing_file_ids.*' => 'integer|exists:media,id',
            'existing_files' => 'nullable|array',
            'existing_files.*' => 'integer|exists:media,id',
            'existing_attachment_ids' => 'nullable|array',
            'existing_attachment_ids.*' => 'integer|exists:media,id',
        ]);

        // Filter out non-database fields before updating
        $updateData = collect($validated)->only([
            'title', 'content', 'company_id', 'category_id', 'visibility', 'ai_synopsis'
        ])->filter(function ($value, $key) {
            // Don't include null category_id since it's not nullable in the database
            if ($key === 'category_id' && $value === null) {
                return false;
            }
            return true;
        })->toArray();

        $researchItem->update($updateData);

        // Sync tags if provided
        if (isset($validated['tag_ids'])) {
            $researchItem->tags()->sync($validated['tag_ids']);
        }

        // Handle file uploads using centralized service
        $attachmentResults = $this->fileUploadService->handleUploads($researchItem, $request);

        $researchItem->load(['company', 'category', 'tags', 'user', 'media']);

        return response()->json([
            'id' => $researchItem->id,
            'title' => $researchItem->title,
            'content' => $researchItem->content,
            'visibility' => $researchItem->visibility,
            'company' => $researchItem->company ? [
                'id' => $researchItem->company->id,
                'name' => $researchItem->company->name,
                'ticker' => $researchItem->company->ticker_symbol,
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
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to update research item: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(ResearchItem $researchItem): JsonResponse
    {
        // Only allow deleting own research items
        if ($researchItem->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Mark associated assets as orphaned before deletion
        $this->fileUploadService->removeMediaFromModel($researchItem);

        $researchItem->delete();
        return response()->json(null, 204);
    }


    /**
     * Get available files that can be reused for research items
     */
    public function getAvailableFiles(Request $request): JsonResponse
    {
        \Log::info('getAvailableFiles called', [
            'user_id' => auth()->id(),
            'search' => $request->get('search'),
            'limit' => $request->get('limit')
        ]);

        // Get all media files from research items and documents that the user has access to
        $userFiles = collect();

        // Get files from user's own research items
        if (auth()->check()) {
            $userResearchItems = ResearchItem::where('user_id', auth()->id())
                ->with('media')
                ->get();

            foreach ($userResearchItems as $item) {
                foreach ($item->getMedia('attachments') as $media) {
                    $userFiles->push([
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'url' => $media->getUrl(),
                        'source_type' => 'research_item',
                        'source_id' => $item->id,
                        'source_title' => $item->title,
                        'created_at' => $media->created_at,
                    ]);
                }
            }

            // Get files from user's own documents
            $userDocuments = \App\Models\Document::where('user_id', auth()->id())
                ->with('media')
                ->get();

            foreach ($userDocuments as $document) {
                foreach ($document->getMedia('attachments') as $media) {
                    $userFiles->push([
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'url' => $media->getUrl(),
                        'source_type' => 'document',
                        'source_id' => $document->id,
                        'source_title' => $document->title,
                        'created_at' => $media->created_at,
                    ]);
                }
            }
        }

        // Sort by most recent first
        $sortedFiles = $userFiles->sortByDesc('created_at')->values();

        \Log::info('Files found', [
            'total_files' => $sortedFiles->count(),
            'user_id' => auth()->id()
        ]);

        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
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
            ]
        ]);
    }

    /**
     * Link existing files to a research item
     */
    public function linkExistingFiles(Request $request, ResearchItem $researchItem): JsonResponse
    {
        $validated = $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'integer|exists:media,id',
        ]);

        try {
            foreach ($validated['media_ids'] as $mediaId) {
                $originalMedia = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);

                // Check if user has access to this file
                $hasAccess = false;

                if ($originalMedia->model_type === 'App\Models\ResearchItem') {
                    $sourceItem = ResearchItem::find($originalMedia->model_id);
                    $hasAccess = $sourceItem && $sourceItem->user_id === auth()->id();
                } elseif ($originalMedia->model_type === 'App\Models\Document') {
                    $sourceDocument = \App\Models\Document::find($originalMedia->model_id);
                    $hasAccess = $sourceDocument && $sourceDocument->user_id === auth()->id();
                }

                if (!$hasAccess) {
                    return response()->json([
                        'message' => 'You do not have access to one or more selected files.'
                    ], 403);
                }

                // Copy the file to this research item
                $researchItem->addMediaFromUrl($originalMedia->getUrl())
                    ->usingName($originalMedia->name)
                    ->usingFileName($originalMedia->file_name)
                    ->toMediaCollection('attachments');
            }

            return response()->json([
                'message' => 'Files linked successfully',
                'linked_count' => count($validated['media_ids'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Error linking existing files to research item', [
                'research_item_id' => $researchItem->id,
                'media_ids' => $validated['media_ids'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Failed to link files: ' . $e->getMessage()
            ], 500);
        }
    }
}