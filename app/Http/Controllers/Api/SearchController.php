<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Company;
use App\Models\Document;
use App\Models\ResearchItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Universal search across companies, blog posts, research items, and documents
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (empty(trim($query))) {
            return response()->json([
                'companies' => [],
                'blogPosts' => [],
                'researchItems' => [],
                'documents' => [],
            ]);
        }

        // Search companies (using database-agnostic LIKE)
        $companies = Company::where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('ticker', 'LIKE', "%{$query}%")
                ->orWhere('sector', 'LIKE', "%{$query}%")
                ->orWhere('industry', 'LIKE', "%{$query}%");
        })
            ->select('id', 'name', 'ticker', 'sector', 'industry', 'market_cap')
            ->limit(10)
            ->get();

        // Search published blog posts
        $blogPosts = BlogPost::where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%");
            })
            ->with('user:id,name')
            ->select('id', 'title', 'slug', 'content', 'published_at', 'user_id')
            ->latest('published_at')
            ->limit(5)
            ->get();

        // Search research items (only public ones for unauthenticated users)
        $researchQuery = ResearchItem::where('visibility', 'public')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%");
            });

        // If user is authenticated, include their own research items
        if (auth()->check()) {
            $researchQuery->orWhere(function ($q) use ($query) {
                $q->where('user_id', auth()->id())
                    ->where(function ($subQ) use ($query) {
                        $subQ->where('title', 'LIKE', "%{$query}%")
                            ->orWhere('content', 'LIKE', "%{$query}%");
                    });
            });
        }

        $researchItems = $researchQuery
            ->with(['company:id,name,ticker', 'category:id,name', 'tags:id,name,color'])
            ->select('id', 'title', 'content', 'visibility', 'company_id', 'category_id', 'created_at')
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Search documents (only public ones for unauthenticated users)
        $documentsQuery = Document::where('visibility', 'public')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->orWhere('ai_synopsis', 'LIKE', "%{$query}%");
            });

        // If user is authenticated, include their own documents
        if (auth()->check()) {
            $documentsQuery->orWhere(function ($q) use ($query) {
                $q->where('user_id', auth()->id())
                    ->where(function ($subQ) use ($query) {
                        $subQ->where('title', 'LIKE', "%{$query}%")
                            ->orWhere('description', 'LIKE', "%{$query}%")
                            ->orWhere('ai_synopsis', 'LIKE', "%{$query}%");
                    });
            });
        }

        $documents = $documentsQuery
            ->with(['company:id,name,ticker', 'category:id,name', 'tags:id,name,color'])
            ->select('id', 'title', 'description', 'ai_synopsis', 'visibility', 'company_id', 'category_id', 'created_at')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return response()->json([
            'companies' => $companies,
            'blogPosts' => $blogPosts,
            'researchItems' => $researchItems,
            'documents' => $documents,
        ]);
    }
}
