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
        // Pagination parameters
        $perPage = $request->get('limit', 5);
        $page = $request->get('page', 1);

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

        $researchItems = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        // Transform the data for the frontend
        $researchData = $researchItems->map(function ($item) {
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
        });

        // Return paginated response with metadata
        return response()->json([
            'data' => $researchData,
            'pagination' => [
                'current_page' => $researchItems->currentPage(),
                'total_pages' => $researchItems->lastPage(),
                'has_more_pages' => $researchItems->hasMorePages(),
                'total_items' => $researchItems->total(),
                'per_page' => $researchItems->perPage(),
                'from' => $researchItems->firstItem(),
                'to' => $researchItems->lastItem(),
            ]
        ]);
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
            'ai_synopsis' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv,jpg,jpeg,png,gif,webp,svg',
            'document_urls' => 'nullable|array',
            'document_urls.*' => 'url',
            'document_names' => 'nullable|array',
            'document_names.*' => 'nullable|string|max:255',
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

        // Handle URL downloads
        if ($request->has('document_urls') && is_array($request->document_urls)) {
            foreach ($request->document_urls as $index => $url) {
                if (!empty($url)) {
                    try {
                        // Get the document name from the parallel array or generate one
                        $documentName = $request->document_names[$index] ?? null;
                        if (empty($documentName)) {
                            $documentName = 'Document ' . ($index + 1);
                        }

                        // Download the file from URL
                        $tempFile = $this->downloadFileFromUrl($url, $documentName);

                        if ($tempFile) {
                            $researchItem->addMedia($tempFile)
                                ->usingName($documentName)
                                ->toMediaCollection('attachments');

                            // Clean up temp file
                            unlink($tempFile);
                        }
                    } catch (\Exception $e) {
                        // Log the error but continue with other files
                        \Log::error('Failed to download document from URL: ' . $url, ['error' => $e->getMessage()]);
                    }
                }
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
        // Check visibility permissions
        if (auth()->check()) {
            // If authenticated, allow viewing own items + public items
            if ($researchItem->user_id !== auth()->id() && $researchItem->visibility !== 'public') {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        } else {
            // If not authenticated, only allow public items
            if ($researchItem->visibility !== 'public') {
                return response()->json(['message' => 'Forbidden'], 403);
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
            'user' => $researchItem->user ? [
                'id' => $researchItem->user->id,
                'name' => $researchItem->user->name,
            ] : null,
            'created_at' => $researchItem->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $researchItem->updated_at->format('Y-m-d H:i:s'),
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

    /**
     * Download a file from URL and return temporary file path.
     */
    private function downloadFileFromUrl(string $url, string $documentName): ?string
    {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \Exception('Invalid URL format');
            }

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Rainmaker/1.0)');

            // Execute request
            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            if (curl_error($ch)) {
                throw new \Exception('cURL error: ' . curl_error($ch));
            }

            curl_close($ch);

            if ($httpCode !== 200) {
                throw new \Exception('HTTP error: ' . $httpCode);
            }

            if (empty($data)) {
                throw new \Exception('Empty response from URL');
            }

            // Determine file extension from content type or URL
            $extension = $this->getFileExtensionFromUrl($url, $contentType);

            // Create temporary file
            $tempFilePath = tempnam(sys_get_temp_dir(), 'download_') . '.' . $extension;

            if (file_put_contents($tempFilePath, $data) === false) {
                throw new \Exception('Failed to write downloaded content to temporary file');
            }

            return $tempFilePath;

        } catch (\Exception $e) {
            \Log::error('URL download failed', [
                'url' => $url,
                'document_name' => $documentName,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get file extension from URL or content type.
     */
    private function getFileExtensionFromUrl(string $url, ?string $contentType): string
    {
        // First try to get extension from content type
        if ($contentType) {
            $mimeToExtension = [
                'application/pdf' => 'pdf',
                'application/msword' => 'doc',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                'application/vnd.ms-excel' => 'xls',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                'application/vnd.ms-powerpoint' => 'ppt',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
                'text/plain' => 'txt',
                'text/csv' => 'csv',
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/svg+xml' => 'svg',
            ];

            $contentType = strtolower(trim(explode(';', $contentType)[0]));
            if (isset($mimeToExtension[$contentType])) {
                return $mimeToExtension[$contentType];
            }
        }

        // Fallback to URL extension
        $urlPath = parse_url($url, PHP_URL_PATH);
        if ($urlPath) {
            $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
            if (!empty($extension)) {
                return $extension;
            }
        }

        // Default fallback
        return 'bin';
    }
}