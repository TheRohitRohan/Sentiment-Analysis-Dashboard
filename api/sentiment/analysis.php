<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Services\NewsAPI;
use App\SentimentAnalyzer;
use App\Database;
use App\Logger;
use App\Cache;
use App\NewsAnalyzer;
use App\Config;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// Load configuration
Config::load();

// Initialize components
$newsAPI = new NewsAPI();
$sentimentAnalyzer = new SentimentAnalyzer();
$database = Database::getInstance();
$logger = new Logger();
$cache = new Cache();

// Create analyzer
$analyzer = new NewsAnalyzer($newsAPI, $sentimentAnalyzer, $database, $logger, $cache);

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    // Get analysis results
    $results = $analyzer->analyzeNews();
    
    // Format data for dashboard
    $dashboardData = [
        'sentimentDistribution' => [
            'positive' => 0,
            'neutral' => 0,
            'negative' => 0
        ],
        'timeSeries' => [
            'labels' => [],
            'data' => []
        ],
        'summary' => [
            'totalArticles' => count($results),
            'averageSentiment' => 0,
            'lastUpdated' => date('Y-m-d H:i:s')
        ]
    ];
    
    // Process results
    $totalSentiment = 0;
    foreach ($results as $result) {
        // Update sentiment distribution
        if ($result['sentiment_score'] > 0.1) {
            $dashboardData['sentimentDistribution']['positive']++;
        } elseif ($result['sentiment_score'] < -0.1) {
            $dashboardData['sentimentDistribution']['negative']++;
        } else {
            $dashboardData['sentimentDistribution']['neutral']++;
        }
        
        // Add to time series
        $dashboardData['timeSeries']['labels'][] = $result['analyzed_at'];
        $dashboardData['timeSeries']['data'][] = $result['sentiment_score'];
        
        // Add to total sentiment
        $totalSentiment += $result['sentiment_score'];
    }
    
    // Calculate average sentiment
    if (count($results) > 0) {
        $dashboardData['summary']['averageSentiment'] = $totalSentiment / count($results);
    }
    
    // Return JSON response
    echo json_encode($dashboardData);
} catch (Exception $e) {
    // Log error
    $logger->error('API Error: ' . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => $e->getMessage()
    ]);
} 