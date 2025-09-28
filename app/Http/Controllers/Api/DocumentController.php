<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // Check if user has permission to download documents
        if (!auth()->user()->can('download documents')) {
            return response()->json(['message' => 'You do not have permission to view documents.'], 403);
        }

        $perPage = $request->get('limit', 15);
        $page = $request->get('page', 1);

        // Determine if we should return actual Documents or Assets
        $useDocuments = $request->get('use_documents', false);

        if ($useDocuments) {
            // Query actual Documents with their relationships
            $query = Document::query()
                ->with(['company', 'category', 'tags', 'user', 'assets']);

            // Filter by company if provided
            if ($request->has('company_id') && ! empty($request->company_id)) {
                $query->where('company_id', $request->company_id);
            }

            // Filter by category if provided
            if ($request->has('category_id') && ! empty($request->category_id)) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by visibility if provided
            if ($request->has('visibility') && ! empty($request->visibility)) {
                $query->where('visibility', $request->visibility);
            }

            // Filter by tags if provided
            if ($request->has('tag_ids') && ! empty($request->tag_ids)) {
                $tagIds = is_array($request->tag_ids) ? $request->tag_ids : [$request->tag_ids];
                $query->whereHas('tags', function ($tagQuery) use ($tagIds) {
                    $tagQuery->whereIn('tags.id', $tagIds);
                });
            }

            // Search functionality (includes title, description, company names, and tags)
            if ($request->has('search') && ! empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', '%'.$searchTerm.'%')
                        ->orWhere('description', 'like', '%'.$searchTerm.'%')
                        ->orWhereHas('company', function ($companyQuery) use ($searchTerm) {
                            $companyQuery->where('name', 'like', '%'.$searchTerm.'%')
                                ->orWhere('ticker', 'like', '%'.$searchTerm.'%');
                        })
                        ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                            $tagQuery->where('name', 'like', '%'.$searchTerm.'%');
                        });
                });
            }

            $documents = $query->latest()->paginate($perPage, ['*'], 'page', $page);

            $documentsData = $documents->map(function ($document) {
                return $this->transformDocument($document);
            });

            return response()->json([
                'data' => $documentsData,
                'pagination' => [
                    'current_page' => $documents->currentPage(),
                    'total_pages' => $documents->lastPage(),
                    'has_more_pages' => $documents->hasMorePages(),
                    'total_items' => $documents->total(),
                    'per_page' => $documents->perPage(),
                    'from' => $documents->firstItem(),
                    'to' => $documents->lastItem(),
                ],
            ]);
        }

        // Default behavior: Query all assets from the dedicated assets table
        $query = \App\Models\Asset::query()
            ->with(['company', 'user']);

        // Filter by company if provided
        if ($request->has('company_id') && ! empty($request->company_id)) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by source type if provided
        if ($request->has('source_type') && ! empty($request->source_type)) {
            $query->where('source_type', $request->source_type);
        }

        // Filter by orphaned status if provided
        if ($request->has('show_orphaned')) {
            $showOrphaned = filter_var($request->show_orphaned, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_orphaned', $showOrphaned);
        }

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%'.$searchTerm.'%')
                    ->orWhere('description', 'like', '%'.$searchTerm.'%')
                    ->orWhere('file_name', 'like', '%'.$searchTerm.'%')
                    ->orWhereHas('company', function ($companyQuery) use ($searchTerm) {
                        $companyQuery->where('name', 'like', '%'.$searchTerm.'%')
                            ->orWhere('ticker', 'like', '%'.$searchTerm.'%');
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
                'size' => $asset->size, // Add size field for widget display
                'file_name' => $asset->file_name,
                'mime_type' => $asset->mime_type,
                'company' => $asset->company ? [
                    'id' => $asset->company->id,
                    'name' => $asset->company->name,
                    'ticker' => $asset->company->ticker,
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
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        \Log::info('Document creation started', [
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        // Check if user has permission to upload documents
        if (!auth()->user()->can('upload documents')) {
            return response()->json(['message' => 'You do not have permission to upload documents.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'visibility' => 'required|in:public,team,private',
            'asset_ids' => 'nullable|array',
            'asset_ids.*' => 'exists:assets,id',
        ]);

        $validated['user_id'] = auth()->id();

        $document = Document::create($validated);

        // Attach tags if provided
        if (isset($validated['tag_ids'])) {
            $document->tags()->sync($validated['tag_ids']);
        }

        // Attach assets if provided
        if (isset($validated['asset_ids'])) {
            \Log::info('Attaching assets to document', [
                'document_id' => $document->id,
                'asset_ids' => $validated['asset_ids'],
            ]);
            $document->assets()->sync($validated['asset_ids']);
        }

        $document->load(['company', 'category', 'tags', 'user', 'assets']);

        \Log::info('Document created successfully', [
            'document_id' => $document->id,
            'asset_count' => $document->assets->count(),
            'tag_count' => $document->tags->count(),
        ]);

        return response()->json($this->transformDocument($document), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document): JsonResponse
    {
        // Check if user has permission to download documents
        if (!auth()->user()->can('download documents')) {
            return response()->json(['message' => 'You do not have permission to view documents.'], 403);
        }

        $document->load(['company', 'category', 'tags', 'user', 'assets']);

        return response()->json($this->transformDocument($document));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document): JsonResponse
    {
        // Check permissions: admin can edit all, users can only edit their own documents
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->can('upload documents')) {
            return response()->json(['message' => 'You do not have permission to edit documents.'], 403);
        }

        // Non-admin users can only edit documents they created
        if (!$user->hasRole('admin') && $document->user_id !== $user->id) {
            return response()->json(['message' => 'You can only edit documents that you created.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'visibility' => 'required|in:public,team,private',
            'asset_ids' => 'nullable|array',
            'asset_ids.*' => 'exists:assets,id',
        ]);

        $document->update($validated);

        // Sync tags if provided
        if (isset($validated['tag_ids'])) {
            $document->tags()->sync($validated['tag_ids']);
        }

        // Sync assets if provided
        if (isset($validated['asset_ids'])) {
            $document->assets()->sync($validated['asset_ids']);
        }

        $document->load(['company', 'category', 'tags', 'user', 'assets']);

        return response()->json($this->transformDocument($document));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document): JsonResponse
    {
        // Check permissions: admin can delete all, users can only delete their own documents
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->can('upload documents')) {
            return response()->json(['message' => 'You do not have permission to delete documents.'], 403);
        }

        // Non-admin users can only delete documents they created
        if (!$user->hasRole('admin') && $document->user_id !== $user->id) {
            return response()->json(['message' => 'You can only delete documents that you created.'], 403);
        }

        // Remove asset associations (but keep the assets themselves)
        $document->assets()->detach();

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
            'company' => $document->company ? [
                'id' => $document->company->id,
                'name' => $document->company->name,
                'ticker' => $document->company->ticker,
            ] : null,
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
