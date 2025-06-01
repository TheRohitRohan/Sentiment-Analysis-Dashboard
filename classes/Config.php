<?php
namespace App;

class Config {
    private static $config = [];
    
    public static function load() {
        $configFiles = [
            'app' => __DIR__ . '/app.php',
            'database' => __DIR__ . '/database.php',
            'redis' => __DIR__ . '/redis.php',
            'sentiment' => __DIR__ . '/sentiment.php'
        ];
        
        foreach ($configFiles as $key => $file) {
            if (file_exists($file)) {
                self::$config[$key] = require $file;
            }
        }
    }
    
    public static function get($key, $default = null) {
        $keys = explode('.', $key);
        $config = self::$config;
        
        foreach ($keys as $segment) {
            if (!isset($config[$segment])) {
                return $default;
            }
            $config = $config[$segment];
        }
        
        return $config;
    }
    
    public static function set($key, $value) {
        $keys = explode('.', $key);
        $config = &self::$config;
        
        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $config[$segment] = $value;
            } else {
                if (!isset($config[$segment])) {
                    $config[$segment] = [];
                }
                $config = &$config[$segment];
            }
        }
    }
}
