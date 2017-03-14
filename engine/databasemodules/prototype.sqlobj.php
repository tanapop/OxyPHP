<?php
class Sqlobj {
    // SQL string, itself.
    public $sqlstring;
    // The values to be inserted in sql.
    public $sqlvalues;
    
    public function __construct($str, $vals){
        $this->sqlstring = $str;
        $this->sqlvalues = $vals;
    }
}