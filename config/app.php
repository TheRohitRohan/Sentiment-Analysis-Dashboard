<?php
return [
    'name' => 'Sentiment Analysis Dashboard',
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => $_ENV['APP_DEBUG'] ?? true,
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'timezone' => 'UTC',
    'locale' => 'en',
    'key' => $_ENV['APP_KEY'] ?? 'base64:'.base64_encode(random_bytes(32)),
    'cipher' => 'AES-256-CBC',
    
    // API Configuration
    'api' => [
        'base_url' => 'https://newstimes.share.zrok.io',
        'timeout' => 30,
        'retry_attempts' => 3,
        'cache_ttl' => 3600 // 1 hour
    ],
    
    // Logging Configuration
    'logging' => [
        'channel' => 'sentiment_analysis',
        'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
        'path' => __DIR__ . '/../logs/app.log'
    ],
    
    // Sentiment Analysis Configuration
    'sentiment' => [
        'confidence_threshold' => 0.1,
        'cache_results' => true,
        'cache_ttl' => 3600
    ]
];
