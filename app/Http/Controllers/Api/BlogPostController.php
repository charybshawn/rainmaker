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

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Start with published blog posts
        $blogPostsQuery = BlogPost::where('status', 'published')
            ->with(['user:id,name', 'companies:id,name,ticker_symbol']);

        // Search by title or content (case-insensitive, database agnostic)
        $blogPostsQuery->where(function($q) use ($query) {
            $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($query) . '%'])
              ->orWhereRaw('LOWER(content) LIKE ?', ['%' . strtolower($query) . '%']);
        });

        $blogPosts = $blogPostsQuery->get();

        // If include_companies is true, also search for blog posts linked to companies that match the query
        if ($includeCompanies) {
            $matchingCompanies = Company::where(function($q) use ($query) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                  ->orWhereRaw('LOWER(ticker_symbol) LIKE ?', ['%' . strtolower($query) . '%']);
            })->get();

            if ($matchingCompanies->isNotEmpty()) {
                $companyIds = $matchingCompanies->pluck('id');

                $relatedBlogPosts = BlogPost::where('status', 'published')
                    ->with(['user:id,name', 'companies:id,name,ticker_symbol'])
                    ->whereHas('companies', function($q) use ($companyIds) {
                        $q->whereIn('companies.id', $companyIds);
                    })
                    ->get();

                // Merge and remove duplicates
                $blogPosts = $blogPosts->merge($relatedBlogPosts)->unique('id');
            }
        }

        return response()->json($blogPosts->values());
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