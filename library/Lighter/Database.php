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
        
        try {
            $query = self::$connection->query($sql);
        } catch (\Exception $e) {
            throw $e;
        }
        
        return $query;
    }
    
    public function escape($string) {
        return self::$connection->escapeString($string);
    }
    
    public function lastInsertRowId() {
        return self::$connection->lastInsertRowID();
    }
    
    public function version() {
        $version = \SQLite3::version();
        return $version['versionString'];
    }
}
