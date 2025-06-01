<?php
namespace App;

class SentimentAnalyzer {
    private $positiveWords;
    private $negativeWords;
    private $emotionWords;
    private $logger;

    public function __construct() {
        $this->loadWordLists();
    }

    private function loadWordLists() {
        $this->positiveWords = [
            'approve', 'success', 'effective', 'safe', 'promising', 'support',
            'important', 'protect', 'increase', 'improve', 'better', 'good'
        ];

        $this->negativeWords = [
            'cancel', 'skepticism', 'limit', 'restriction', 'departure',
            'risk', 'disease', 'severe', 'concern', 'bad', 'worse'
        ];

        $this->emotionWords = [
            'joy' => ['approve', 'success', 'effective', 'promising'],
            'fear' => ['risk', 'disease', 'severe', 'concern'],
            'anger' => ['cancel', 'skepticism', 'limit'],
            'surprise' => ['new', 'unexpected', 'sudden'],
            'sadness' => ['cancel', 'limit', 'restriction']
        ];
    }

    public function analyzeSentiment($text) {
        $words = explode(' ', strtolower($text));
        $score = 0;
        $wordCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords)) {
                $score += 1;
            } elseif (in_array($word, $this->negativeWords)) {
                $score -= 1;
            }
            $wordCount++;
        }

        return [
            'score' => $wordCount > 0 ? $score / $wordCount : 0,
            'polarity' => $this->getPolarity($score),
            'confidence' => $this->calculateConfidence($score, $wordCount)
        ];
    }

    public function detectEmotions($text) {
        $emotions = [
            'joy' => 0,
            'sadness' => 0,
            'anger' => 0,
            'fear' => 0,
            'surprise' => 0
        ];

        $words = explode(' ', strtolower($text));
        foreach ($words as $word) {
            foreach ($this->emotionWords as $emotion => $wordList) {
                if (in_array($word, $wordList)) {
                    $emotions[$emotion]++;
                }
            }
        }

        return $emotions;
    }

    private function getPolarity($score) {
        if ($score > 0.1) return 'positive';
        if ($score < -0.1) return 'negative';
        return 'neutral';
    }

    private function calculateConfidence($score, $wordCount) {
        return min(abs($score) * 2, 1);
    }
}
