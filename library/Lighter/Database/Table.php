<?php

namespace Lighter\Database;

class Table {
    
    public $name;
    public $columns;
    public $primaryKeys;
    
    private $database;
    
    public function __construct($database=null) {
        
        if (is_null($database)) {
            $this->database = new \Lighter\Database;
        } else {
            $this->database = $database;
        }
        
        $this->columns = array();
    }
    
    public function get($name) {
        
        try {
            
            $query = $this->database->query("PRAGMA TABLE_INFO($name)");
            // @todo interface for SQlite3Result
            while ($result = $query->fetchArray(SQLITE3_ASSOC)){
                
                $column = new Column;
                $column->load($result);
                
                if ($column->primaryKey) {
                    $this->primaryKeys[$column->name] = $column->name;
                }
                
                $this->columns[$column->name] = $column;
            }
            
            $this->name = $name;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
