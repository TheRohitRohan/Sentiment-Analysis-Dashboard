<?php
require 'vendor/autoload.php';

use App\Services\NewsAPI;
use App\SentimentAnalyzer;
use App\Database;
use App\Logger;
use App\Cache;
use App\NewsAnalyzer;
use App\Config;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
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

// Run analysis
try {
    $results = $analyzer->analyzeNews();
    echo "Analysis completed successfully!\n";
    echo "Results:\n";
    print_r($results);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sentiment Analysis Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Sentiment Analysis Dashboard</h1>
        
        <div class="dashboard">
            <!-- Summary Statistics -->
            <div class="card">
                <h2>Summary</h2>
                <div class="summary-stats">
                    <p>Total Articles: <span id="totalArticles">0</span></p>
                    <p>Average Sentiment: <span id="averageSentiment">0.00</span></p>
                    <p>Last Updated: <span id="lastUpdated">-</span></p>
                </div>
            </div>
            
            <!-- Sentiment Distribution -->
            <div class="card">
                <h2>Sentiment Distribution</h2>
                <div class="chart-container">
                    <canvas id="sentimentChart"></canvas>
                </div>
            </div>
            
            <!-- Time Series -->
            <div class="card">
                <h2>Sentiment Over Time</h2>
                <div class="chart-container">
                    <canvas id="timeSeriesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/dashboard.js"></script>
</body>
</html>

