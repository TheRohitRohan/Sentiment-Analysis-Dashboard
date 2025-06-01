<?php
namespace App;

use App\Services\NewsAPI;

class NewsAnalyzer {
    private $newsAPI;
    private $sentimentAnalyzer;
    private $database;
    private $logger;
    private $cache;

    public function __construct(
        NewsAPI $newsAPI,
        SentimentAnalyzer $sentimentAnalyzer,
        Database $database,
        Logger $logger,
        Cache $cache
    ) {
        $this->newsAPI = $newsAPI;
        $this->sentimentAnalyzer = $sentimentAnalyzer;
        $this->database = $database;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    public function analyzeNews() {
        try {
            $articles = $this->newsAPI->fetchNews();
            $results = [];

            foreach ($articles as $article) {
                $analysis = [
                    'article_id' => $article['id'],
                    'title' => $article['title'],
                    'sentiment' => $this->sentimentAnalyzer->analyzeSentiment($article['full_text']),
                    'emotions' => $this->sentimentAnalyzer->detectEmotions($article['full_text']),
                    'analyzed_at' => date('Y-m-d H:i:s')
                ];

                $this->storeAnalysis($analysis);
                $results[] = $analysis;
            }

            return $results;
        } catch (\Exception $e) {
            $this->logger->logError("Analysis failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function storeAnalysis($analysis) {
        $data = [
            'article_id' => $analysis['article_id'],
            'title' => $analysis['title'],
            'sentiment_score' => $analysis['sentiment']['score'],
            'sentiment_polarity' => $analysis['sentiment']['polarity'],
            'emotions' => json_encode($analysis['emotions']),
            'analyzed_at' => $analysis['analyzed_at']
        ];

        $this->database->insert('sentiment_analysis', $data);
    }
}
