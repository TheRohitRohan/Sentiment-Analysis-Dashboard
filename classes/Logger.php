<?php
namespace App;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger {
    private $logger;

    public function __construct() {
        $this->logger = new MonologLogger('sentiment_analysis');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', MonologLogger::DEBUG));
    }

    public function logError($message) {
        $this->logger->error($message);
    }

    public function logInfo($message) {
        $this->logger->info($message);
    }

    public function logAnalysis($data) {
        $this->logger->info('Analysis completed', $data);
    }
}
