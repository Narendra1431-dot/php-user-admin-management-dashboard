<?php
/**
 * Task 3: Performance Optimization & Caching
 * Cache layer and optimization utilities
 */

class Cache {
    private static $cache = [];
    private static $ttl = 3600; // 1 hour
    
    public static function get($key) {
        if (isset(self::$cache[$key])) {
            $data = self::$cache[$key];
            if (time() < $data['expires']) {
                return $data['value'];
            } else {
                unset(self::$cache[$key]);
            }
        }
        return null;
    }
    
    public static function set($key, $value, $ttl = null) {
        $ttl = $ttl ?? self::$ttl;
        self::$cache[$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
    }
    
    public static function clear() {
        self::$cache = [];
    }
}

class DatabaseOptimizer {
    private $mysqli;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }
    
    // Enable query caching
    public function enableQueryCache() {
        $this->mysqli->query("SET SESSION query_cache_type = ON");
    }
    
    // Get query statistics
    public function getQueryStats() {
        $result = $this->mysqli->query("SHOW SESSION STATUS LIKE 'Threads%'");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

class PerformanceMonitor {
    private static $start_time;
    private static $start_memory;
    
    public static function start() {
        self::$start_time = microtime(true);
        self::$start_memory = memory_get_usage();
    }
    
    public static function end() {
        $end_time = microtime(true);
        $end_memory = memory_get_usage();
        
        $execution_time = $end_time - self::$start_time;
        $memory_used = $end_memory - self::$start_memory;
        
        return [
            'execution_time' => round($execution_time * 1000, 2) . 'ms',
            'memory_used' => round($memory_used / 1024, 2) . 'KB',
            'peak_memory' => round(memory_get_peak_usage() / 1024 / 1024, 2) . 'MB'
        ];
    }
}

/**
 * Optimize images on upload
 */
class ImageOptimizer {
    public static function optimizeImage($source, $destination, $max_width = 800, $max_height = 800) {
        $image_info = getimagesize($source);
        $image_type = $image_info[2];
        
        // Load image
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                $quality = 80;
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                $quality = 8;
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($source);
                break;
            default:
                return false;
        }
        
        // Get current dimensions
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Calculate new dimensions
        $ratio = min($max_width / $width, $max_height / $height);
        if ($ratio < 1) {
            $new_width = $width * $ratio;
            $new_height = $height * $ratio;
            
            $new_image = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            $image = $new_image;
        }
        
        // Save optimized image
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                return imagejpeg($image, $destination, $quality);
            case IMAGETYPE_PNG:
                return imagepng($image, $destination, $quality);
            case IMAGETYPE_GIF:
                return imagegif($image, $destination);
        }
        
        imagedestroy($image);
        return true;
    }
}

?>
