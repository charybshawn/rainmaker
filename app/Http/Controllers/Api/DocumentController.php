<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Models\Document;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('limit', 15);
        $page = $request->get('page', 1);

        // Query all assets from the dedicated assets table
        $query = \App\Models\Asset::query()
            ->with(['company', 'user']);

        // Filter by company if provided
        if ($request->has('company_id') && !empty($request->company_id)) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by source type if provided
        if ($request->has('source_type') && !empty($request->source_type)) {
            $query->where('source_type', $request->source_type);
        }

        // Filter by orphaned status if provided
        if ($request->has('show_orphaned')) {
            $showOrphaned = filter_var($request->show_orphaned, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_orphaned', $showOrphaned);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('file_name', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('company', function($companyQuery) use ($searchTerm) {
                      $companyQuery->where('name', 'like', '%' . $searchTerm . '%')
                                  ->orWhere('ticker_symbol', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $assets = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        // Transform assets to document format
        $documentsData = $assets->map(function ($asset) {
            return [
                'id' => $asset->id,
                'title' => $asset->title,
                'description' => $asset->description,
                'visibility' => $asset->visibility,
                'is_orphaned' => $asset->is_orphaned,
                'company' => $asset->company ? [
                    'id' => $asset->company->id,
                    'name' => $asset->company->name,
                    'ticker' => $asset->company->ticker_symbol,
                ] : null,
                'category' => null, // We can add category relationship to Asset later if needed
                'tags' => collect(), // We can add tags relationship to Asset later if needed
                'attachments' => [[
                    'id' => $asset->id,
                    'name' => $asset->title,
                    'file_name' => $asset->file_name,
                    'mime_type' => $asset->mime_type,
                    'size' => $asset->size,
                    'url' => $asset->url,
                ]],
                'user' => $asset->user ? [
                    'id' => $asset->user->id,
                    'name' => $asset->user->name,
                ] : null,
                'created_at' => $asset->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $asset->updated_at->format('Y-m-d H:i:s'),
                'source_type' => $asset->source_type,
                'source_id' => $asset->source_id,
            ];
        });

        return response()->json([
            'data' => $documentsData,
            'pagination' => [
                'current_page' => $assets->currentPage(),
                'total_pages' => $assets->lastPage(),
                'has_more_pages' => $assets->hasMorePages(),
                'total_items' => $assets->total(),
                'per_page' => $assets->perPage(),
                'from' => $assets->firstItem(),
                'to' => $assets->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FileUploadRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'visibility' => 'required|in:public,team,private',
        ]);

        $validated['user_id'] = auth()->id();

        $document = Document::create($validated);

        // Attach tags if provided
        if (isset($validated['tag_ids'])) {
            $document->tags()->sync($validated['tag_ids']);
        }

        // Handle file uploads using centralized service
        $uploadResults = $this->fileUploadService->handleUploads($document, $request);

        $document->load(['company', 'category', 'tags', 'user', 'media']);

        return response()->json($this->transformDocument($document), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document): JsonResponse
    {
        $document->load(['company', 'category', 'tags', 'user', 'media']);
        return response()->json($this->transformDocument($document));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document): JsonResponse
    {
        // Only allow updating own documents
        if ($document->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'visibility' => 'required|in:public,team,private',
        ]);

        $document->update($validated);

        // Sync tags if provided
        if (isset($validated['tag_ids'])) {
            $document->tags()->sync($validated['tag_ids']);
        }

        $document->load(['company', 'category', 'tags', 'user', 'media']);

        return response()->json($this->transformDocument($document));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document): JsonResponse
    {
        // Only allow deleting own documents
        if ($document->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Remove media files and sync assets
        $this->fileUploadService->removeMediaFromModel($document);

        $document->delete();
        return response()->json(null, 204);
    }

    /**
     * Transform document data for API response.
     */
    private function transformDocument(Document $document): array
    {
        return [
            'id' => $document->id,
            'title' => $document->title,
            'description' => $document->description,
            'visibility' => $document->visibility,
            'company' => [
                'id' => $document->company->id,
                'name' => $document->company->name,
                'ticker' => $document->company->ticker_symbol,
            ],
            'category' => $document->category ? [
                'id' => $document->category->id,
                'name' => $document->category->name,
                'color' => $document->category->color,
            ] : null,
            'tags' => $document->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ];
            }),
            'attachments' => $document->getFormattedAttachments(),
            'user' => $document->user ? [
                'id' => $document->user->id,
                'name' => $document->user->name,
            ] : null,
            'created_at' => $document->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $document->updated_at->format('Y-m-d H:i:s'),
        ];
    }

}
