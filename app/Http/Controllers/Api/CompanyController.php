<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Check if user has permission to view companies or create research items
        // Users who can create research items should be able to see companies for dropdown selection
        if (!auth()->user()->can('view companies') && !auth()->user()->can('create research items')) {
            return response()->json(['message' => 'You do not have permission to view companies.'], 403);
        }

        // Pagination parameters
        $perPage = $request->get('limit', 10);
        $page = $request->get('page', 1);

        $query = Company::query()->with(['researchItems', 'documents', 'creator']);

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('ticker', 'like', '%'.$searchTerm.'%')
                    ->orWhere('sector', 'like', '%'.$searchTerm.'%')
                    ->orWhere('industry', 'like', '%'.$searchTerm.'%');
            });
        }

        // Show companies with recent activity first, then by creation date
        $companies = $query->leftJoin('research_items', 'companies.id', '=', 'research_items.company_id')
            ->select('companies.*')
            ->selectRaw('MAX(research_items.updated_at) as latest_research_activity')
            ->groupBy('companies.id')
            ->orderByRaw('COALESCE(MAX(research_items.updated_at), companies.updated_at) DESC')
            ->paginate($perPage, ['*'], 'page', $page);

        // Transform the data for the frontend
        $companiesData = $companies->map(function ($company) use ($request) {
            $data = [
                'id' => $company->id,
                'name' => $company->name,
                'ticker' => $company->ticker,
                'sector' => $company->sector,
                'industry' => $company->industry,
                'marketCap' => $company->market_cap,
                'marketCapFormatted' => $company->market_cap_formatted,
                'description' => $company->description,
                'reports_financial_data_in' => $company->reports_financial_data_in,
                'headquarters' => $company->headquarters,
                'employees' => $company->employees,
                'foundedDate' => $company->founded_date?->format('Y-m-d'),
                'createdAt' => $company->created_at->format('Y-m-d H:i:s'),
            ];

            // Include counts if requested
            if ($request->has('include_counts') || $request->boolean('include_counts')) {
                $data['research_items_count'] = $company->researchItems->count();
                // Count documents that have assets (since documents now reference assets)
                $data['documents_count'] = $company->documents()->whereHas('assets')->count();
            }

            return $data;
        });

        // Return paginated response with metadata
        return response()->json([
            'data' => $companiesData,
            'pagination' => [
                'current_page' => $companies->currentPage(),
                'total_pages' => $companies->lastPage(),
                'has_more_pages' => $companies->hasMorePages(),
                'total_items' => $companies->total(),
                'per_page' => $companies->perPage(),
                'from' => $companies->firstItem(),
                'to' => $companies->lastItem(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // Check if user has permission to create companies
        if (!auth()->user()->can('create companies')) {
            return response()->json(['message' => 'You do not have permission to create companies.'], 403);
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'ticker' => [
                    'required',
                    'string',
                    'max:10',
                    Rule::unique('companies')->whereNull('deleted_at'),
                ],
                'sector' => 'nullable|string|max:255',
                'industry' => 'nullable|string|max:255',
                'market_cap' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'reports_financial_data_in' => 'nullable|string|max:3',
                'website_url' => 'nullable|url',
                'headquarters' => 'nullable|string|max:255',
                'employees' => 'nullable|integer|min:0',
                'founded_date' => 'nullable|date',
            ]);

            $validated['created_by'] = auth()->id() ?? 1; // Default to user 1 for now
            $validated['ticker'] = strtoupper($validated['ticker']);

            $company = Company::create($validated);

            return response()->json([
                'id' => $company->id,
                'name' => $company->name,
                'ticker' => $company->ticker,
                'sector' => $company->sector,
                'industry' => $company->industry,
                'marketCap' => $company->market_cap,
                'marketCapFormatted' => $company->market_cap_formatted,
                'reports_financial_data_in' => $company->reports_financial_data_in,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Company creation validation failed', [
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);
            throw $e; // Re-throw to let Laravel handle it properly
        } catch (\Exception $e) {
            \Log::error('Company creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Failed to create company: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Company $company): JsonResponse
    {
        // Check if user has permission to view companies
        if (!auth()->user()->can('view companies')) {
            return response()->json(['message' => 'You do not have permission to view companies.'], 403);
        }

        $company->load(['researchItems.tags', 'researchItems.category', 'researchItems.assets', 'researchItems.directAssets', 'researchItems.user', 'documents.tags', 'documents.user', 'documents.assets', 'creator']);

        return response()->json([
            'id' => $company->id,
            'name' => $company->name,
            'ticker' => $company->ticker,
            'sector' => $company->sector,
            'industry' => $company->industry,
            'marketCap' => $company->market_cap,
            'marketCapFormatted' => $company->market_cap_formatted,
            'description' => $company->description,
            'reports_financial_data_in' => $company->reports_financial_data_in,
            'headquarters' => $company->headquarters,
            'employees' => $company->employees,
            'foundedDate' => $company->founded_date?->format('Y-m-d'),
            'researchItemsCount' => $company->researchItems->count(),
            'documentsCount' => $company->documents->count(),
            'research_items' => $company->researchItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'visibility' => $item->visibility,
                    'user' => $item->user ? [
                        'id' => $item->user->id,
                        'name' => $item->user->name,
                    ] : null,
                    'attachments' => $item->getFormattedAttachments(),
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
                    'source_date' => $item->source_date ? $item->source_date->format('Y-m-d') : null,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'documents' => $company->documents->map(function ($document) {
                return [
                    'id' => $document->id,
                    'title' => $document->title,
                    'description' => $document->description,
                    'ai_synopsis' => $document->ai_synopsis,
                    'visibility' => $document->visibility,
                    'user' => $document->user ? [
                        'id' => $document->user->id,
                        'name' => $document->user->name,
                    ] : null,
                    'attachments' => $document->assets->map(function ($asset) {
                        return [
                            'id' => $asset->id,
                            'name' => $asset->title,
                            'file_name' => $asset->file_name,
                            'mime_type' => $asset->mime_type,
                            'size' => $asset->size,
                            'url' => $asset->url,
                        ];
                    }),
                    'created_at' => $document->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'createdAt' => $company->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        // Check permissions: admin can edit all, users can only edit their own companies
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->can('edit companies')) {
            return response()->json(['message' => 'You do not have permission to edit companies.'], 403);
        }

        // Non-admin users can only edit companies they created
        if (!$user->hasRole('admin') && $company->created_by !== $user->id) {
            return response()->json(['message' => 'You can only edit companies that you created.'], 403);
        }

        try {
            \Log::info('Company update started', [
                'company_id' => $company->id,
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'ticker' => [
                    'required',
                    'string',
                    'max:10',
                    Rule::unique('companies')->ignore($company->id)->whereNull('deleted_at'),
                ],
                'sector' => 'nullable|string|max:255',
                'industry' => 'nullable|string|max:255',
                'market_cap' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'reports_financial_data_in' => 'nullable|string|max:3',
                'website_url' => 'nullable|url',
                'headquarters' => 'nullable|string|max:255',
                'employees' => 'nullable|integer|min:0',
                'founded_date' => 'nullable|date',
            ]);

            \Log::info('Company update validation passed', [
                'company_id' => $company->id,
                'validated_data' => $validated,
            ]);

            $validated['ticker'] = strtoupper($validated['ticker']);

            // Update the company
            $company->update($validated);

            // Refresh the model to get updated computed attributes
            $company->refresh();

            \Log::info('Company update completed successfully', [
                'company_id' => $company->id,
                'updated_data' => $company->toArray(),
            ]);

            // Return data in the format expected by frontend
            return response()->json([
                'message' => 'Company updated successfully',
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'ticker' => $company->ticker,
                    'ticker' => $company->ticker,
                    'sector' => $company->sector,
                    'industry' => $company->industry,
                    'market_cap' => $company->market_cap,
                    'marketCap' => $company->market_cap,
                    'marketCapFormatted' => $company->market_cap_formatted,
                    'description' => $company->description,
                    'reports_financial_data_in' => $company->reports_financial_data_in,
                    'headquarters' => $company->headquarters,
                    'employees' => $company->employees,
                    'founded_date' => $company->founded_date,
                    'foundedDate' => $company->founded_date?->format('Y-m-d'),
                    'website' => $company->website_url,
                    'website_url' => $company->website_url,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Company update validation failed', [
                'company_id' => $company->id,
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);
            throw $e; // Re-throw to let Laravel handle it properly
        } catch (\Exception $e) {
            \Log::error('Company update failed', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Failed to update company: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Company $company): JsonResponse
    {
        // Check permissions: admin can delete all, users can only delete their own companies
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->can('delete companies')) {
            return response()->json(['message' => 'You do not have permission to delete companies.'], 403);
        }

        // Non-admin users can only delete companies they created
        if (!$user->hasRole('admin') && $company->created_by !== $user->id) {
            return response()->json(['message' => 'You can only delete companies that you created.'], 403);
        }

        try {
            \Log::info('Company deletion started', [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'user_id' => auth()->id(),
            ]);

            $company->delete(); // This will be a soft delete

            \Log::info('Company deletion completed successfully', [
                'company_id' => $company->id,
                'company_name' => $company->name,
            ]);

            return response()->json(null, 204);

        } catch (\Exception $e) {
            \Log::error('Company deletion failed', [
                'company_id' => $company->id,
                'company_name' => $company->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Failed to delete company: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get deletion impact summary for companies
     */
    public function deletionImpact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_ids' => 'required|array',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $companyIds = $validated['company_ids'];
        $companies = Company::whereIn('id', $companyIds)->get();

        $impact = [
            'companies' => [],
            'totals' => [
                'companies' => $companies->count(),
                'research_items' => 0,
                'documents' => 0,
                'blog_post_associations' => 0,
            ],
        ];

        foreach ($companies as $company) {
            $researchCount = $company->researchItems()->count();
            $documentsCount = $company->documents()->count();
            $blogAssociationsCount = $company->blogPosts()->count();

            $impact['companies'][] = [
                'id' => $company->id,
                'name' => $company->name,
                'ticker' => $company->ticker,
                'research_items' => $researchCount,
                'documents' => $documentsCount,
                'blog_associations' => $blogAssociationsCount,
            ];

            $impact['totals']['research_items'] += $researchCount;
            $impact['totals']['documents'] += $documentsCount;
            $impact['totals']['blog_post_associations'] += $blogAssociationsCount;
        }

        return response()->json($impact);
    }

    /**
     * Bulk delete companies with soft delete
     */
    public function bulkDestroy(Request $request): JsonResponse
    {
        \Log::info('Bulk delete request started', [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
        ]);

        // Check permissions: admin can delete all, users can only delete their own companies
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->can('delete companies')) {
            \Log::warning('Bulk delete permission denied', [
                'user_id' => $user->id,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->permissions->pluck('name'),
            ]);
            return response()->json(['message' => 'You do not have permission to delete companies.'], 403);
        }

        $validated = $request->validate([
            'company_ids' => 'required|array',
            'company_ids.*' => 'exists:companies,id',
        ]);

        $companyIds = $validated['company_ids'];
        $companies = Company::whereIn('id', $companyIds)->get();

        \Log::info('Companies found for bulk delete', [
            'requested_ids' => $companyIds,
            'found_companies' => $companies->pluck('id')->toArray(),
        ]);

        // For non-admin users, check if they can delete each company
        if (!$user->hasRole('admin')) {
            foreach ($companies as $company) {
                if ($company->created_by !== $user->id) {
                    \Log::warning('User attempted to delete company they did not create', [
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                        'company_creator' => $company->created_by,
                    ]);
                    return response()->json(['message' => 'You can only delete companies that you created.'], 403);
                }
            }
        }

        $deletedCount = 0;
        $errors = [];

        foreach ($companies as $company) {
            try {
                $company->delete(); // Soft delete
                $deletedCount++;
            } catch (\Exception $e) {
                $errors[] = [
                    'company_id' => $company->id,
                    'name' => $company->name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'deleted_count' => $deletedCount,
            'errors' => $errors,
            'message' => $deletedCount === count($companyIds)
                ? "Successfully soft-deleted {$deletedCount} companies"
                : "Soft-deleted {$deletedCount} of ".count($companyIds).' companies with '.count($errors).' errors',
        ]);
    }

    public function getBlogPosts(Company $company): JsonResponse
    {
        $posts = $company->blogPosts()
            ->where('status', 'published')
            ->with('user:id,name')
            ->latest('published_at')
            ->take(10)
            ->get(['blog_posts.id', 'title', 'slug', 'content', 'status', 'published_at', 'blog_posts.created_at', 'user_id']);

        return response()->json($posts);
    }
}
