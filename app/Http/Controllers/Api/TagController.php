<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Tag::query()->where('is_active', true);

        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        $tags = $query->orderBy('name')->get(['id', 'name', 'color', 'description']);

        return response()->json($tags);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;

        // Set default color if not provided
        if (!isset($validated['color'])) {
            $colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
            $validated['color'] = $colors[array_rand($colors)];
        }

        $tag = Tag::create($validated);

        return response()->json([
            'id' => $tag->id,
            'name' => $tag->name,
            'color' => $tag->color,
            'description' => $tag->description,
        ], 201);
    }

    public function update(Request $request, Tag $tag): JsonResponse
    {
        $user = auth()->user();

        // Check if user can edit tags (admin or creator)
        if (!$user->hasRole('admin') && $tag->created_by !== $user->id) {
            return response()->json(['message' => 'You can only edit tags you created.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $tag->update($validated);

        return response()->json([
            'id' => $tag->id,
            'name' => $tag->name,
            'color' => $tag->color,
            'description' => $tag->description,
        ]);
    }

    public function destroy(Tag $tag): JsonResponse
    {
        $user = auth()->user();

        // Check if user can delete tags (admin or creator)
        if (!$user->hasRole('admin') && $tag->created_by !== $user->id) {
            return response()->json(['message' => 'You can only delete tags you created.'], 403);
        }

        // Check if tag is being used
        $usageCount = $tag->researchItems()->count();
        if ($usageCount > 0) {
            return response()->json([
                'message' => "Cannot delete tag. It is currently used by {$usageCount} research item(s)."
            ], 422);
        }

        $tag->delete();

        return response()->json(null, 204);
    }

    public function show(Tag $tag): JsonResponse
    {
        return response()->json([
            'id' => $tag->id,
            'name' => $tag->name,
            'color' => $tag->color,
            'description' => $tag->description,
            'usage_count' => $tag->researchItems()->count(),
            'created_at' => $tag->created_at->format('Y-m-d H:i:s'),
        ]);
    }
}