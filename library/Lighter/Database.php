<?php

namespace Lighter;

class Database {
    
    private static $connection;
    
    public function open($file) {
        self::$connection = new \SQLite3($file);
    }
    
    public function exec($sql) {
        
        if (self::$connection->exec($sql)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function query($sql) {
        
        if (!$query = self::$connection->query($sql)) {
            return false;
        }
        
        return $query;
    }
    
    public function version() {
        $version = \SQLite3::version();
        return $version['versionString'];
    }
}
