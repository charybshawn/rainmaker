<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResearchItem;
use App\Models\Company;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResearchItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
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

        $researchItems = $query->latest()->get();

        return response()->json($researchItems->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'content' => $item->content,
                'visibility' => $item->visibility,
                'company' => [
                    'id' => $item->company->id,
                    'name' => $item->company->name,
                    'ticker' => $item->company->ticker_symbol,
                ],
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
        }));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'nullable|exists:categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            'visibility' => 'required|in:public,team,private',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,jpg,jpeg,png,gif,webp,svg',
        ]);

        $validated['user_id'] = auth()->id();

        $researchItem = ResearchItem::create($validated);

        // Attach tags if provided
        if (isset($validated['tag_ids'])) {
            $researchItem->tags()->sync($validated['tag_ids']);
        }

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $researchItem->addMedia($file)
                    ->toMediaCollection('attachments');
            }
        }

        $researchItem->load(['company', 'category', 'tags', 'user', 'media']);

        return response()->json([
            'id' => $researchItem->id,
            'title' => $researchItem->title,
            'content' => $researchItem->content,
            'visibility' => $researchItem->visibility,
            'company' => [
                'id' => $researchItem->company->id,
                'name' => $researchItem->company->name,
                'ticker' => $researchItem->company->ticker_symbol,
            ],
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
            'attachments' => $researchItem->getMedia('attachments')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'url' => $media->getUrl(),
                ];
            }),
            'created_at' => $researchItem->created_at->format('Y-m-d H:i:s'),
        ], 201);
    }

    public function show(ResearchItem $researchItem): JsonResponse
    {
        // Only allow viewing own research items
        if ($researchItem->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $researchItem->load(['company', 'category', 'tags', 'user']);

        return response()->json([
            'id' => $researchItem->id,
            'title' => $researchItem->title,
            'content' => $researchItem->content,
            'visibility' => $researchItem->visibility,
            'company' => [
                'id' => $researchItem->company->id,
                'name' => $researchItem->company->name,
                'ticker' => $researchItem->company->ticker_symbol,
            ],
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
            'created_at' => $researchItem->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function update(Request $request, ResearchItem $researchItem): JsonResponse
    {
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
        ]);

        $researchItem->update($validated);

        // Sync tags if provided
        if (isset($validated['tag_ids'])) {
            $researchItem->tags()->sync($validated['tag_ids']);
        }

        $researchItem->load(['company', 'category', 'tags', 'user']);

        return response()->json([
            'id' => $researchItem->id,
            'title' => $researchItem->title,
            'content' => $researchItem->content,
            'visibility' => $researchItem->visibility,
            'company' => [
                'id' => $researchItem->company->id,
                'name' => $researchItem->company->name,
                'ticker' => $researchItem->company->ticker_symbol,
            ],
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
            'created_at' => $researchItem->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function destroy(ResearchItem $researchItem): JsonResponse
    {
        // Only allow deleting own research items
        if ($researchItem->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $researchItem->delete();
        return response()->json(null, 204);
    }
}