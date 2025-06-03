<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\NewsAPI;
use App\SentimentAnalyzer;
use App\NewsAnalyzer;
use App\Config;

// Load configuration
Config::load();

// Initialize components
$newsAPI = new NewsAPI();
$sentimentAnalyzer = new SentimentAnalyzer();

// Create analyzer
$analyzer = new NewsAnalyzer($newsAPI, $sentimentAnalyzer);

// Set headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    // Get analysis results
    $results = $analyzer->analyzeNews();
    
    // Return JSON response with full analysis results
    echo json_encode([
        'status' => 'success',
        'data' => [
            'full_analysis' => $results
        ]
    ]);
} catch (Exception $e) {
    // Return error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => 'Internal Server Error',
        'message' => $e->getMessage()
    ]);
} 