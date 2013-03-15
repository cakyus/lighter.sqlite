<?php

namespace Lighter\Database;

class Column {
    
    public $name;
    public $type;
    public $length;
    public $precission;
	public $defaultValue;
	public $allowNull;
	public $primaryKey;
    
    public function __construct() {
        
    }
    
    public function load($data) {
        
		$this->name = $data['name'];
		$this->allowNull = ($data['notnull'] ? false : true);
		$this->primaryKey = ($data['pk'] ? true : false);
		$this->defaultValue = $data['dflt_value'];
        
        if (preg_match_all(
              "/([A-Z]+)(\(([0-9]+)(,([0-9]+))?\))?/"
            , $data['type']
            , $match)) {
            
            $this->type = $match[1][0];
            $this->length = 0;
            $this->precission = 0;
            
            if (!empty($match[3][0])) {
                $this->length = $match[3][0];
            }
            
            if (!empty($match[5][0])) {
                $this->precission = $match[5][0];
            }
        }
    }    
}
