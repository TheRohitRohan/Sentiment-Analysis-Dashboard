<?php
namespace App;

use PHPInsight\Sentiment;

class SentimentAnalyzer {
    private $sentiment;

    public function __construct() {
        $this->sentiment = new Sentiment();
    }

    public function analyzeSentiment($text) {
        $class = $this->sentiment->categorise($text); // 'pos', 'neu', 'neg'
        $scores = $this->sentiment->score($text); // array: ['pos' => float, 'neu' => float, 'neg' => float]

        // Map PHPInsight class to polarity and score
        $polarity = [
            'pos' => 'positive',
            'neu' => 'neutral',
            'neg' => 'negative'
        ];
        $score = $scores[$class];

        return [
            'score' => $score,
            'polarity' => $polarity[$class],
            'confidence' => $score
        ];
    }
}
