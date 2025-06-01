<?php
namespace App;

use Predis\Client;
use Exception;

class Cache {
    private $redis;
    private $enabled;

    public function __construct() {
        $this->enabled = false;
        try {
            if (isset($_ENV['REDIS_HOST']) && isset($_ENV['REDIS_PORT'])) {
                $this->redis = new Client([
                    'scheme' => 'tcp',
                    'host'   => $_ENV['REDIS_HOST'],
                    'port'   => $_ENV['REDIS_PORT'],
                ]);
                $this->enabled = true;
            }
        } catch (Exception $e) {
            // Redis connection failed, caching will be disabled
            $this->enabled = false;
        }
    }

    public function set($key, $value, $ttl = 3600) {
        if ($this->enabled) {
            $this->redis->setex($key, $ttl, json_encode($value));
        }
    }

    public function get($key) {
        if ($this->enabled) {
            $value = $this->redis->get($key);
            return $value ? json_decode($value, true) : null;
        }
        return null;
    }

    public function clear() {
        if ($this->enabled) {
            $this->redis->flushAll();
        }
    }

    public function isEnabled() {
        return $this->enabled;
    }
}
