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
    
    public function testRecordRead() {
        $user = new \Lighter\Database\Record('users');
        // get user with id = 1
        $user->get(1);
        debug_print('User-Name:', $user->name);
    }
    
    public function testRecordUpdate() {
        
        $user = new \Lighter\Database\Record('users');
        $name = "administrator";
        $time = time();
        // get user with id = 1, ie. "admin"
        $user->get(1);
        $user->name = $name;
        $user->timeCreate = $time;
        $user->set();
        // load once again
        $user->get(1);
        
        if ($time != $user->timeCreate) {
            throw new \Exception("Update field timeCreate fail");
        }
        
        if ($name != $user->name) {
            throw new \Exception("Update field name fail");
        }
    }
    
    public function testRecordDelete() {
        
        $user = new \Lighter\Database\Record('users');
        // get user with id = 1, ie. "admin"
        $user->get(1);
        $user->del();
        
        try {
            $user->get(1);
        } catch (\Exception $e) {
            echo $e->getMessage()."\n";
        }
    }
    
    public function testRecordInsert() {
        
        $user = new \Lighter\Database\Record('users');
        
        $name = "admin123";
        $time = time();
        
        $user->name = $name;
        $user->timeCreate = $time;
        $user->put();
        
        $id = $user->id;
        
        $user->get($id);
        
        if ($time != $user->timeCreate) {
            throw new \Exception("Insert field timeCreate fail");
        }
        
        if ($name != $user->name) {
            throw new \Exception("Insert field name fail");
        }
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
    // @todo display class name and function name who call this function
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
