<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;
use App\Logger;

class NewsAPI {
    private $client;
    private $baseUrl;
    private $logger;

    public function __construct() {
        $this->baseUrl = "https://newstimes.share.zrok.io";
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 30.0,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->logger = new Logger();
    }

    /**
     * Fetch news from the API
     * @return array
     * @throws Exception
     */
    public function fetchNews() {
        try {
            $response = $this->client->get('/news');
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                return $this->validateResponse($data);
            }
            throw new Exception("API returned status code: " . $response->getStatusCode());
        } catch (GuzzleException $e) {
            $this->logger->logError("API Error: " . $e->getMessage());
            echo "GuzzleException: " . $e->getMessage();
            throw new Exception("Failed to fetch news: " . $e->getMessage());
        } catch (Exception $e) {
            $this->logger->logError("General Error: " . $e->getMessage());
            echo "Exception: " . $e->getMessage();
            throw $e;
        }
    }

    /**
     * Validate API response
     * @param array $data
     * @return array
     * @throws Exception
     */
    private function validateResponse($data) {
        if (!is_array($data)) {
            throw new Exception("Invalid API response format");
        }

        // Validate required fields for each article
        foreach ($data as $article) {
            if (!isset($article['id']) || 
                !isset($article['title']) || 
                !isset($article['full_text'])) {
                throw new Exception("Missing required fields in article");
            }
        }

        return $data;
    }

    /**
     * Process news articles
     * @param array $articles
     * @return array
     */
    public function processArticles($articles) {
        $processedArticles = [];
        
        foreach ($articles as $article) {
            $processedArticles[] = [
                'id' => $article['id'],
                'title' => $article['title'],
                'link' => $article['link'] ?? '',
                'published' => $article['published'] ?? null,
                'source' => $article['source'] ?? '',
                'authors' => $article['authors'] ?? null,
                'summary' => $article['summary'] ?? '',
                'top_image' => $article['top_image'] ?? '',
                'full_text' => $article['full_text'],
                'category' => $article['category'] ?? 'News',
                'processed_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return $processedArticles;
    }

    /**
     * Get article by ID
     * @param int $id
     * @return array|null
     */
    public function getArticleById($id) {
        try {
            $response = $this->client->get('/news');
            $data = json_decode($response->getBody(), true);
            
            foreach ($data as $article) {
                if ($article['id'] === $id) {
                    return $this->processArticles([$article])[0];
                }
            }
            
            return null;
        } catch (GuzzleException $e) {
            $this->logger->logError("Error fetching article: " . $e->getMessage());
            throw new Exception("Failed to fetch article: " . $e->getMessage());
        }
    }

    /**
     * Search articles by keyword
     * @param string $keyword
     * @return array
     */
    public function searchArticles($keyword) {
        try {
            $response = $this->client->get('/news');
            $data = json_decode($response->getBody(), true);
            $results = [];
            
            foreach ($data as $article) {
                if (stripos($article['title'], $keyword) !== false || 
                    stripos($article['full_text'], $keyword) !== false) {
                    $results[] = $this->processArticles([$article])[0];
                }
            }
            
            return $results;
        } catch (GuzzleException $e) {
            $this->logger->logError("Error searching articles: " . $e->getMessage());
            throw new Exception("Failed to search articles: " . $e->getMessage());
        }
    }

    /**
     * Get articles by category
     * @param string $category
     * @return array
     */
    public function getArticlesByCategory($category) {
        try {
            $response = $this->client->get('/news');
            $data = json_decode($response->getBody(), true);
            $results = [];
            
            foreach ($data as $article) {
                if (isset($article['category']) && 
                    strtolower($article['category']) === strtolower($category)) {
                    $results[] = $this->processArticles([$article])[0];
                }
            }
            
            return $results;
        } catch (GuzzleException $e) {
            $this->logger->logError("Error fetching category articles: " . $e->getMessage());
            throw new Exception("Failed to fetch category articles: " . $e->getMessage());
        }
    }

    /**
     * Cache the API response
     * @param array $data
     * @return void
     */
    public function cacheResponse($data) {
        // Implement caching logic
        // You can use Redis, Memcached, or file-based caching
    }

    /**
     * Get cached response if available
     * @return array|null
     */
    public function getCachedResponse() {
        // Implement cache retrieval logic
        return null;
    }
}