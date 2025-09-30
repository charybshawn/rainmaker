<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogPostController extends Controller
{
    /**
     * Display user's own blog posts
     */
    public function index()
    {
        $posts = auth()->user()->blogPosts()
            ->latest()
            ->get(['id', 'title', 'slug', 'status', 'published_at', 'created_at']);

        return Inertia::render('Blog/Dashboard', [
            'posts' => $posts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|in:analysis,strategy,insights,news',
            'author_name' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'company_ids' => 'array',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $companyIds = $validated['company_ids'] ?? [];
        unset($validated['company_ids']);

        $post = BlogPost::create($validated);

        if (! empty($companyIds)) {
            $post->companies()->attach($companyIds);
        }

        return response()->json([
            'message' => 'Blog post created successfully.',
            'post' => $post->load('user:id,name', 'companies:id,name,ticker'),
        ], 201);
    }

    /**
     * Display a specific blog post (public)
     */
    public function show(BlogPost $post)
    {
        if ($post->isDraft() && $post->user_id !== auth()->id()) {
            abort(404);
        }

        $post->load('user:id,name', 'companies:id,name,ticker,sector');

        return Inertia::render('Blog/Post', [
            'post' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|in:analysis,strategy,insights,news',
            'author_name' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
            'company_ids' => 'array',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if ($validated['status'] === 'published' && $post->isDraft()) {
            $validated['published_at'] = now();
        } elseif ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        $companyIds = $validated['company_ids'] ?? [];
        unset($validated['company_ids']);

        $post->update($validated);

        // Sync company relationships
        $post->companies()->sync($companyIds);

        return response()->json([
            'message' => 'Blog post updated successfully.',
            'post' => $post->load('user:id,name', 'companies:id,name,ticker'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Blog post deleted successfully.',
        ]);
    }

    /**
     * Display user's public blog
     */
    public function userBlog($username)
    {
        $user = \App\Models\User::where('name', $username)->firstOrFail();

        $posts = $user->blogPosts()
            ->where('status', 'published')
            ->latest('published_at')
            ->get(['id', 'title', 'slug', 'content', 'published_at']);

        return Inertia::render('Blog/UserBlog', [
            'user' => $user->only(['name']),
            'posts' => $posts,
        ]);
    }
}
