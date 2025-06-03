<?php
namespace App;

use App\Services\NewsAPI;

class NewsAnalyzer {
    private $newsAPI;
    private $sentimentAnalyzer;

    public function __construct(
        NewsAPI $newsAPI,
        SentimentAnalyzer $sentimentAnalyzer
    ) {
        $this->newsAPI = $newsAPI;
        $this->sentimentAnalyzer = $sentimentAnalyzer;
    }

    public function analyzeNews() {
        try {
            $articles = $this->newsAPI->fetchNews();
            $results = [];

            foreach ($articles as $article) {
                $analysis = [
                    'article_id' => $article['id'],
                    'title' => $article['title'],
                    'link' => $article['link'] ?? null,
                    'sentiment' => $this->sentimentAnalyzer->analyzeSentiment($article['full_text']),
                    'analyzed_at' => date('Y-m-d H:i:s')
                ];

                $results[] = $analysis;
            }

            return $results;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
