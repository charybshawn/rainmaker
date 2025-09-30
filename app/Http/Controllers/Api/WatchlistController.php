<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Watchlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WatchlistController extends Controller
{
    /**
     * Display a listing of the user's watchlists.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Auth::user()->watchlists()
            ->withCount('companies')
            ->with(['companies' => function ($query) {
                $query->select('companies.id', 'companies.name', 'companies.ticker', 'companies.market_cap')
                      ->limit(3); // Show first 3 companies for preview
            }]);

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        switch ($sortBy) {
            case 'companies_count':
                $query->orderBy('companies_count', $sortDirection);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortDirection);
                break;
            default:
                $query->orderBy('name', $sortDirection);
                break;
        }

        $watchlists = $query->get();

        return response()->json([
            'watchlists' => $watchlists,
            'total' => $watchlists->count()
        ]);
    }

    /**
     * Store a newly created watchlist.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $watchlist = Auth::user()->watchlists()->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $watchlist->load('companies');
        $watchlist->loadCount('companies');

        return response()->json([
            'watchlist' => $watchlist,
            'message' => 'Watchlist created successfully'
        ], 201);
    }

    /**
     * Display the specified watchlist.
     */
    public function show(Watchlist $watchlist): JsonResponse
    {
        // Ensure the watchlist belongs to the authenticated user
        if ($watchlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Watchlist not found'], 404);
        }

        $watchlist->load(['companies' => function ($query) {
            $query->select('companies.*')
                  ->orderBy('watchlist_companies.added_at', 'desc');
        }]);
        $watchlist->loadCount('companies');

        return response()->json(['watchlist' => $watchlist]);
    }

    /**
     * Update the specified watchlist.
     */
    public function update(Request $request, Watchlist $watchlist): JsonResponse
    {
        // Ensure the watchlist belongs to the authenticated user
        if ($watchlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Watchlist not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $watchlist->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $watchlist->load('companies');
        $watchlist->loadCount('companies');

        return response()->json([
            'watchlist' => $watchlist,
            'message' => 'Watchlist updated successfully'
        ]);
    }

    /**
     * Remove the specified watchlist.
     */
    public function destroy(Watchlist $watchlist): JsonResponse
    {
        // Ensure the watchlist belongs to the authenticated user
        if ($watchlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Watchlist not found'], 404);
        }

        $watchlist->delete();

        return response()->json(['message' => 'Watchlist deleted successfully']);
    }

    /**
     * Add a company to a watchlist.
     */
    public function addCompany(Request $request, Watchlist $watchlist): JsonResponse
    {
        // Ensure the watchlist belongs to the authenticated user
        if ($watchlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Watchlist not found'], 404);
        }

        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::find($request->company_id);

        if ($watchlist->hasCompany($company)) {
            return response()->json(['message' => 'Company already in watchlist'], 422);
        }

        $watchlist->addCompany($company);

        return response()->json(['message' => 'Company added to watchlist successfully']);
    }

    /**
     * Remove a company from a watchlist.
     */
    public function removeCompany(Watchlist $watchlist, Company $company): JsonResponse
    {
        // Ensure the watchlist belongs to the authenticated user
        if ($watchlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Watchlist not found'], 404);
        }

        $watchlist->removeCompany($company);

        return response()->json(['message' => 'Company removed from watchlist successfully']);
    }

    /**
     * Add a company to multiple watchlists.
     */
    public function addCompanyToWatchlists(Request $request): JsonResponse
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'watchlist_ids' => 'required|array|min:1',
            'watchlist_ids.*' => 'exists:watchlists,id',
            'new_watchlist' => 'nullable|array',
            'new_watchlist.name' => 'required_with:new_watchlist|string|max:255',
            'new_watchlist.description' => 'nullable|string|max:1000',
        ]);

        $company = Company::find($request->company_id);
        $addedTo = [];
        $alreadyIn = [];

        // Handle existing watchlists
        $watchlists = Auth::user()->watchlists()->whereIn('id', $request->watchlist_ids)->get();
        foreach ($watchlists as $watchlist) {
            if ($watchlist->hasCompany($company)) {
                $alreadyIn[] = $watchlist->name;
            } else {
                $watchlist->addCompany($company);
                $addedTo[] = $watchlist->name;
            }
        }

        // Handle new watchlist creation
        if ($request->has('new_watchlist') && $request->new_watchlist) {
            $newWatchlist = Auth::user()->watchlists()->create([
                'name' => $request->new_watchlist['name'],
                'description' => $request->new_watchlist['description'] ?? null,
            ]);
            $newWatchlist->addCompany($company);
            $addedTo[] = $newWatchlist->name;
        }

        $message = [];
        if (!empty($addedTo)) {
            $message[] = 'Added to: ' . implode(', ', $addedTo);
        }
        if (!empty($alreadyIn)) {
            $message[] = 'Already in: ' . implode(', ', $alreadyIn);
        }

        return response()->json([
            'message' => implode('. ', $message),
            'added_to' => $addedTo,
            'already_in' => $alreadyIn
        ]);
    }
}
