<?php
return [
    'host' => $_ENV['REDIS_HOST'] ?? 'localhost',
    'port' => $_ENV['REDIS_PORT'] ?? 6379,
    'timeout' => 2.0,
    'read_write_timeout' => 0,
    'persistent' => false
];
