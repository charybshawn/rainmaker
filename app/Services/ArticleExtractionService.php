<?php

namespace App\Services;

use The3LabsTeam\Readability\Readability;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ArticleExtractionService
{
    /**
     * Extract article content from a URL
     *
     * @param string $url
     * @return array
     */
    public function extractFromUrl(string $url): array
    {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception('Invalid URL provided');
            }

            // Fetch the HTML content
            $html = $this->fetchHtmlContent($url);

            if (!$html) {
                throw new Exception('Failed to fetch content from URL');
            }

            // Extract article content using readability
            $extracted = $this->extractContent($html);

            // Get additional metadata
            $metadata = $this->extractMetadata($html, $url);

            return [
                'success' => true,
                'data' => [
                    'title' => $extracted['title'] ?? $metadata['title'] ?? '',
                    'content' => $extracted['content'] ?? '',
                    'excerpt' => $extracted['excerpt'] ?? $metadata['description'] ?? '',
                    'author' => $metadata['author'] ?? '',
                    'published_date' => $metadata['published_date'] ?? null,
                    'url' => $url,
                    'main_image' => $this->processImageUrl($extracted['main_image'] ?? '', $url),
                    'images' => $this->processImageUrls($extracted['images'] ?? [], $url),
                    'word_count' => str_word_count(strip_tags($extracted['content'] ?? '')),
                    'read_time' => $this->calculateReadTime($extracted['content'] ?? ''),
                ],
                'message' => 'Article extracted successfully'
            ];

        } catch (Exception $e) {
            Log::error('Article extraction failed', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'data' => null,
                'message' => 'Failed to extract article: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fetch HTML content from URL
     *
     * @param string $url
     * @return string|null
     */
    private function fetchHtmlContent(string $url): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; RainmakerBot/1.0; +https://rainmaker.app)',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Accept-Encoding' => 'gzip, deflate',
                    'Connection' => 'keep-alive',
                ])
                ->get($url);

            if ($response->successful()) {
                return $response->body();
            }

            throw new Exception('HTTP request failed with status: ' . $response->status());

        } catch (Exception $e) {
            Log::warning('Failed to fetch HTML content', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Extract content using readability
     *
     * @param string $html
     * @return array
     */
    private function extractContent(string $html): array
    {
        try {
            $readability = new Readability($html);
            $parsed = $readability->parse();

            return [
                'title' => $parsed->getTitle() ?? '',
                'content' => $parsed->getContent() ?? '',
                'excerpt' => $parsed->getExcerpt() ?? '',
                'main_image' => $parsed->getImage() ?? '',
                'images' => $parsed->getImages() ?? [],
            ];

        } catch (Exception $e) {
            Log::warning('Readability parsing failed', [
                'error' => $e->getMessage()
            ]);

            // Fallback: try to extract basic content
            return $this->basicContentExtraction($html);
        }
    }

    /**
     * Basic content extraction fallback
     *
     * @param string $html
     * @return array
     */
    private function basicContentExtraction(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);

        $title = '';
        $content = '';

        // Try to get title
        $titleTags = $dom->getElementsByTagName('title');
        if ($titleTags->length > 0) {
            $title = trim($titleTags->item(0)->textContent);
        }

        // Try to get main content from common article selectors
        $selectors = ['article', 'main', '.content', '.post-content', '.entry-content'];

        foreach ($selectors as $selector) {
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query("//*[contains(@class, 'content') or contains(@class, 'article') or contains(@class, 'post')]");

            if ($nodes->length > 0) {
                $content = $nodes->item(0)->textContent;
                break;
            }
        }

        // If no content found, get body text (last resort)
        if (empty($content)) {
            $body = $dom->getElementsByTagName('body');
            if ($body->length > 0) {
                $content = strip_tags($body->item(0)->textContent);
            }
        }

        return [
            'title' => $title,
            'content' => $content ? "<p>" . nl2br(htmlspecialchars(trim($content))) . "</p>" : '',
            'excerpt' => $content ? substr(strip_tags($content), 0, 200) . '...' : '',
        ];
    }

    /**
     * Extract metadata from HTML
     *
     * @param string $html
     * @param string $url
     * @return array
     */
    private function extractMetadata(string $html, string $url): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $metadata = [];

        // Title
        $titleTags = $dom->getElementsByTagName('title');
        if ($titleTags->length > 0) {
            $metadata['title'] = trim($titleTags->item(0)->textContent);
        }

        // Meta description
        $description = $xpath->query('//meta[@name="description"]/@content');
        if ($description->length > 0) {
            $metadata['description'] = trim($description->item(0)->value);
        }

        // Author
        $author = $xpath->query('//meta[@name="author"]/@content');
        if ($author->length > 0) {
            $metadata['author'] = trim($author->item(0)->value);
        }

        // Try other author selectors
        if (empty($metadata['author'])) {
            $authorSelectors = [
                '//meta[@property="article:author"]/@content',
                '//meta[@name="twitter:creator"]/@content',
                '//*[contains(@class, "author")]/text()',
                '//*[contains(@class, "byline")]/text()'
            ];

            foreach ($authorSelectors as $selector) {
                $result = $xpath->query($selector);
                if ($result->length > 0) {
                    $metadata['author'] = trim($result->item(0)->value ?? $result->item(0)->textContent);
                    break;
                }
            }
        }

        // Published date
        $dateSelectors = [
            '//meta[@property="article:published_time"]/@content',
            '//meta[@name="pubdate"]/@content',
            '//time/@datetime',
            '//*[contains(@class, "date")]/text()'
        ];

        foreach ($dateSelectors as $selector) {
            $result = $xpath->query($selector);
            if ($result->length > 0) {
                $dateValue = trim($result->item(0)->value ?? $result->item(0)->textContent);
                if ($dateValue && strtotime($dateValue)) {
                    $metadata['published_date'] = date('Y-m-d H:i:s', strtotime($dateValue));
                    break;
                }
            }
        }

        return $metadata;
    }

    /**
     * Calculate estimated read time
     *
     * @param string $content
     * @return int minutes
     */
    private function calculateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $wordsPerMinute = 200; // Average reading speed

        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Process a single image URL to make it absolute
     *
     * @param string $imageUrl
     * @param string $baseUrl
     * @return string
     */
    private function processImageUrl(string $imageUrl, string $baseUrl): string
    {
        if (empty($imageUrl)) {
            return '';
        }

        // If it's already a full URL, return as is
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        // Handle protocol-relative URLs (//example.com/image.jpg)
        if (str_starts_with($imageUrl, '//')) {
            $baseScheme = parse_url($baseUrl, PHP_URL_SCHEME) ?? 'https';
            return $baseScheme . ':' . $imageUrl;
        }

        // Handle absolute paths (/path/image.jpg)
        if (str_starts_with($imageUrl, '/')) {
            $baseParts = parse_url($baseUrl);
            $baseScheme = $baseParts['scheme'] ?? 'https';
            $baseHost = $baseParts['host'] ?? '';
            $basePort = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';

            return $baseScheme . '://' . $baseHost . $basePort . $imageUrl;
        }

        // Handle relative paths (relative/image.jpg)
        $baseParts = parse_url($baseUrl);
        $baseScheme = $baseParts['scheme'] ?? 'https';
        $baseHost = $baseParts['host'] ?? '';
        $basePort = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';
        $basePath = rtrim(dirname($baseParts['path'] ?? '/'), '/');

        return $baseScheme . '://' . $baseHost . $basePort . $basePath . '/' . $imageUrl;
    }

    /**
     * Process multiple image URLs to make them absolute
     *
     * @param array $images
     * @param string $baseUrl
     * @return array
     */
    private function processImageUrls(array $images, string $baseUrl): array
    {
        $processedImages = [];

        foreach ($images as $image) {
            $processedUrl = $this->processImageUrl($image, $baseUrl);

            if (!empty($processedUrl)) {
                $processedImages[] = [
                    'url' => $processedUrl,
                    'alt' => '', // Could be enhanced to extract alt text
                ];
            }
        }

        // Remove duplicates and limit to reasonable number
        $processedImages = array_unique($processedImages, SORT_REGULAR);
        return array_slice($processedImages, 0, 10); // Limit to 10 images
    }
}