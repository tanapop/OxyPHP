<?php

class MvcGenerator {

    private $mysql;
    private $tables;

    public function __construct() {
        require_once $_SERVER['DOCUMENT_ROOT'] . "engine/databaseclasses/class.mysql.php";

        $this->mysql = new Mysql();

        $this->tables = $this->mysql->query("SHOW TABLES");

        $this->index();
    }

    private function index() {
        $path = $_SERVER['DOCUMENT_ROOT'] . "engine/mvcgenerator/index.php";
        
        extract(array("tables" => $this->tables));

        ob_start();
        include $path;

        $contents = ob_get_clean();

        echo $contents;
    }
    
    

}
