<?php
return [
    'name' => 'Sentiment Analysis Dashboard',
    'debug' => true,
    'url' => 'http://localhost',
    'timezone' => 'UTC',
    'locale' => 'en',
    
    // API Configuration
    'api' => [
        'base_url' => 'https://newstimes.share.zrok.io',
        'timeout' => 30,
        'retry_attempts' => 3
    ],
    
    // Sentiment Analysis Configuration
    'sentiment' => [
        'confidence_threshold' => 0.1
    ]
];
