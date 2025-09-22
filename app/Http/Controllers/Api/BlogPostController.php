<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Company;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    /**
     * Search blog posts with relationship support
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $includeCompanies = $request->boolean('include_companies', false);
        $perPage = $request->get('limit', 5);
        $page = $request->get('page', 1);

        if (strlen($query) < 2) {
            return response()->json([
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 0,
                    'has_more_pages' => false,
                    'total_items' => 0,
                    'per_page' => $perPage,
                    'from' => null,
                    'to' => null,
                ]
            ]);
        }

        // Start with published blog posts
        $blogPostsQuery = BlogPost::where('status', 'published')
            ->with(['user:id,name', 'companies:id,name,ticker_symbol']);

        // Search by title or content (case-insensitive, database agnostic)
        $blogPostsQuery->where(function($q) use ($query) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($query) . '%'])
              ->orWhereRaw('LOWER(content) LIKE ?', ['%' . strtolower($query) . '%']);
        });

        // If include_companies is true, also include blog posts linked to companies that match the query
        if ($includeCompanies) {
            $blogPostsQuery->orWhereHas('companies', function($q) use ($query) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                  ->orWhereRaw('LOWER(ticker_symbol) LIKE ?', ['%' . strtolower($query) . '%']);
            });
        }

        // Order by published date (newest first) and paginate
        $blogPosts = $blogPostsQuery->orderBy('published_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Return paginated response with metadata
        return response()->json([
            'data' => $blogPosts->items(),
            'pagination' => [
                'current_page' => $blogPosts->currentPage(),
                'total_pages' => $blogPosts->lastPage(),
                'has_more_pages' => $blogPosts->hasMorePages(),
                'total_items' => $blogPosts->total(),
                'per_page' => $blogPosts->perPage(),
                'from' => $blogPosts->firstItem(),
                'to' => $blogPosts->lastItem(),
            ]
        ]);
    }

    /**
     * Get published quotes for display
     */
    public function quotes()
    {
        $quotes = BlogPost::where('status', 'published')
            ->where('category', 'quotes')
            ->select(['id', 'content', 'author_name', 'published_at'])
            ->orderBy('published_at', 'desc')
            ->get();

        return response()->json($quotes);
    }
}