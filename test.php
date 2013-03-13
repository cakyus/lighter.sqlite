<?php

// Test Case for Database

class Test {
    
    public $database;

    public function testConnection() {
        $file = __DIR__.'/test.sqlite';
        $database = new \Lighter\Database;
        $database->open($file);
        $this->database = $database;
    }
    
    public function testVersion() {
        debug_print('Version:', $this->database->version());
    }
    
    public function testSetup() {
        $commandString = file_get_contents('test.sql');
        $commands = explode(';', $commandString);
        for ($i = 0; $i < count($commands) - 1; $i++){
            $this->database->exec($commands[$i]);
        }
    }
    
    public function testQuery() {
        $query = $this->database->query('SELECT * FROM users');
        debug_print('Class-Name:', get_class($query));
    }
    
    public function testRecord() {
        $record = new \Lighter\Database\Record('users');
        $record->get(1);
    }
    
    public function start() {

        foreach (get_class_methods($this) as $method) {
            if (substr($method,0,4) == 'test') {
                debug_print('<'.__CLASS__.'>',$method);
                call_user_func(array($this, $method));
            }
        }
        
        debug_print('<'.__CLASS__.'>', 'done');
    }
}

function debug_print() {
    echo date('H:i:s ').implode("\t",func_get_args())."\n";
}

if (!debug_backtrace()) {
    call_user_func(function(){
        
        include(__DIR__.'/library/Lighter/Loader.php');
        \Lighter\Loader::register();
        
        $test = new Test;
        $test->start();
    });
}
