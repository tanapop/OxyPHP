<?php

class Model {

    // Main table of this module. By default, it has the same name of the module itself.
    public $table;
    // An instance of the class Mysql.
    protected $mysql;

    // It sets the main table name and isntantiate class Mysql.
    public function __construct($table) {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/engine/databaseclasses/class.mysql.php";

        $this->table = $table;

        $this->mysql = new Mysql();
    }

    protected function build($type, $data) {
        
    }

    private function insert_query() {
        
    }

    private function update_query() {
        
    }

    private function select_query() {
        
    }

    private function delete_query() {
        
    }

}

?>