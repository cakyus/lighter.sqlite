<?php

namespace Lighter\Database;

class Record {
    
    private $database;
    private $table;
    private $properties;
    
    public function __construct($tableName, $database=null) {
        
        $this->properties = array();
        
        if (is_null($this->database)) {
            $this->database = new \Lighter\Database;
        } else {
            $this->database = $database;
        }
        
        try {
            $this->table = new Table;
            $this->table->get($tableName);
            foreach ($this->table->columns as $column) {
                $this->properties[$column->name] = $column->defaultValue;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function __get($name) {
        
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }
        
        throw new \Exception("Field not found. \"$name\"");
    }
    
    public function __set($name, $value) {
        
        if (array_key_exists($name, $this->properties)) {
            $this->properties[$name] = $value;
            return true;
        }
        
        throw new \Exception("Field not found. \"$name\"");
    }
    
    public function put() {
        
        $sql = 'INSERT INTO '.$this->table->name
            .' '.$this->getSqlInsertFields()
            ;
        
        try {
            $this->database->exec($sql);
        } catch (\Exception $e) {
            throw $e;
        }
        
        // update insert id
        foreach ($this->table->primaryKeys as $columnName) {
            
            $column = $this->table->columns[$columnName];
            if ($column->type == 'INTEGER') {
                $this->properties[$columnName] =
                    $this->database->lastInsertRowId()
                    ;
            }
        }
        
        return true;        
    }
    
    public function get() {
        
        $sql = 'SELECT * FROM '.$this->table->name
            .' WHERE '.$this->getSqlWherePrimaryKeys(func_get_args())
            ;
            
        $query = $this->database->query($sql);
        if ($properties = $query->fetchArray(SQLITE3_ASSOC)) {
            $this->properties = $properties;
            return true;
        }
        
        return false;
    }
    
    public function set() {
        
        $sql = 'UPDATE '.$this->table->name
            .' SET '.$this->getSqlUpdateFields()
            .' WHERE '.$this->getSqlWherePrimaryKeys()
            ;
        
        try {
            $this->database->exec($sql);
        } catch (\Exception $e) {
            throw $e;
        }
        
        return true;
    }
    
    public function del() {
        
        $sql = 'DELETE FROM '.$this->table->name
            .' WHERE '.$this->getSqlWherePrimaryKeys()
            ;
        
        try {
            $this->database->exec($sql);
        } catch (\Exception $e) {
            throw $e;
        }
        
        return true;
    }
    
    private function getSqlInsertFields() {
        
        $properties = $this->properties;
        $primaryKeys = $this->table->primaryKeys;
        
        // build sql statement
        $sqlInsertFieldNames = array();
        $sqlInsertFieldValues = array();
        foreach ($properties as $columnName => $columnValue) {
            
            // primaryKeys is not included
            if (in_array($columnName, $primaryKeys)) {
                continue;
            }
            
            $sqlInsertFieldNames[] = $columnName;
            if (is_string($properties[$columnName])) {
                $properties[$columnName] = '"'
                    .$this->database->escape($properties[$columnName])
                    .'"'
                    ;
            }
            $sqlInsertFieldValues[] = $properties[$columnName];
        }

        return ' ('.implode(', ', $sqlInsertFieldNames).') '
            .' VALUES ('.implode(', ', $sqlInsertFieldValues).')'
            ;
    }
    
    private function getSqlUpdateFields() {
        
        $properties = $this->properties;
        $primaryKeys = $this->table->primaryKeys;
        
        // build sql statement
        $sqlUpdateFields = array();
        foreach ($properties as $columnName => $columnValue) {
            
            // primaryKeys is not included
            if (in_array($columnName, $primaryKeys)) {
                continue;
            }
            
            if (is_string($properties[$columnName])) {
                $properties[$columnName] = '"'
                    .$this->database->escape($properties[$columnName])
                    .'"'
                    ;
            }
            
            $sqlUpdateFields[] = $columnName.' = '.$properties[$columnName];
        }

        return implode(', ', $sqlUpdateFields);
    }
    
    private function getSqlWherePrimaryKeys($data=null) {
        
        $properties = $this->properties;
        $primaryKeys = $this->table->primaryKeys;
        
        if (!is_null($data)) {
            $properties = array_combine($primaryKeys, $data);
        }
        
        // build sql statement
        $sqlWherePrimaryKeys = array();
        foreach ($primaryKeys as $columnName) {
            if (is_string($properties[$columnName])) {
                $properties[$columnName] = '"'
                    .$this->database->escape($properties[$columnName])
                    .'"'
                    ;
            }
            $sqlWherePrimaryKeys[] = $columnName.' = '.$properties[$columnName];
        }

        return implode(' AND ', $sqlWherePrimaryKeys);
    }
}
