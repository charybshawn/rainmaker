<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Company::query()->with(['researchItems', 'creator']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('ticker_symbol', 'like', '%' . $searchTerm . '%')
                  ->orWhere('sector', 'like', '%' . $searchTerm . '%')
                  ->orWhere('industry', 'like', '%' . $searchTerm . '%');
            });
        }

        // Show companies with recent activity first, then by creation date
        $companies = $query->leftJoin('research_items', 'companies.id', '=', 'research_items.company_id')
            ->select('companies.*')
            ->selectRaw('MAX(research_items.updated_at) as latest_research_activity')
            ->groupBy('companies.id')
            ->orderByRaw('COALESCE(MAX(research_items.updated_at), companies.updated_at) DESC')
            ->get();

        // Transform the data for the frontend
        $companiesData = $companies->map(function ($company) {
            return [
                'id' => $company->id,
                'name' => $company->name,
                'ticker' => $company->ticker_symbol,
                'sector' => $company->sector,
                'industry' => $company->industry,
                'marketCap' => $company->market_cap,
                'marketCapFormatted' => $company->market_cap_formatted,
                'description' => $company->description,
                'headquarters' => $company->headquarters,
                'employees' => $company->employees,
                'foundedDate' => $company->founded_date?->format('Y-m-d'),
                'researchItemsCount' => $company->researchItems->count(),
                'createdAt' => $company->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($companiesData);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ticker_symbol' => 'required|string|max:10|unique:companies',
            'sector' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'market_cap' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'headquarters' => 'nullable|string|max:255',
            'employees' => 'nullable|integer|min:0',
            'founded_date' => 'nullable|date',
        ]);

        $validated['created_by'] = auth()->id() ?? 1; // Default to user 1 for now
        $validated['ticker_symbol'] = strtoupper($validated['ticker_symbol']);

        $company = Company::create($validated);

        return response()->json([
            'id' => $company->id,
            'name' => $company->name,
            'ticker' => $company->ticker_symbol,
            'sector' => $company->sector,
            'industry' => $company->industry,
            'marketCap' => $company->market_cap,
            'marketCapFormatted' => $company->market_cap_formatted,
        ], 201);
    }

    public function show(Company $company): JsonResponse
    {
        $company->load(['researchItems.tags', 'researchItems.media', 'creator']);

        return response()->json([
            'id' => $company->id,
            'name' => $company->name,
            'ticker' => $company->ticker_symbol,
            'sector' => $company->sector,
            'industry' => $company->industry,
            'marketCap' => $company->market_cap,
            'marketCapFormatted' => $company->market_cap_formatted,
            'description' => $company->description,
            'headquarters' => $company->headquarters,
            'employees' => $company->employees,
            'foundedDate' => $company->founded_date?->format('Y-m-d'),
            'researchItemsCount' => $company->researchItems->count(),
            'researchItems' => $company->researchItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'visibility' => $item->visibility,
                    'attachments' => $item->getMedia('attachments')->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'name' => $media->name,
                            'file_name' => $media->file_name,
                            'mime_type' => $media->mime_type,
                            'size' => $media->size,
                            'url' => $media->getUrl(),
                        ];
                    }),
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'createdAt' => $company->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'ticker_symbol' => 'required|string|max:10|unique:companies,ticker_symbol,' . $company->id,
            'sector' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'market_cap' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'headquarters' => 'nullable|string|max:255',
            'employees' => 'nullable|integer|min:0',
            'founded_date' => 'nullable|date',
        ]);

        $validated['ticker_symbol'] = strtoupper($validated['ticker_symbol']);
        $company->update($validated);

        return response()->json([
            'id' => $company->id,
            'name' => $company->name,
            'ticker' => $company->ticker_symbol,
            'sector' => $company->sector,
            'industry' => $company->industry,
            'marketCap' => $company->market_cap,
            'marketCapFormatted' => $company->market_cap_formatted,
        ]);
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();
        return response()->json(null, 204);
    }

    public function getBlogPosts(Company $company): JsonResponse
    {
        $posts = $company->blogPosts()
            ->where('status', 'published')
            ->with('user:id,name')
            ->latest('published_at')
            ->take(10)
            ->get(['id', 'title', 'slug', 'content', 'status', 'published_at', 'created_at', 'user_id']);

        return response()->json($posts);
    }
}
