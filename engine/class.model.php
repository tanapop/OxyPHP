<?php

class Model {

    // The name of table's primary key.
    private $primarykey;
    // The name of main table of this module. By default, it has the same name of the module itself.
    private $table;
    // An instance of the class Dbclass.
    protected $dbclass;
    // An instance of the class Sql.
    protected $sql;
    // The global object of Helpers class
    protected $helpers;

    // It sets the main table name, instantiate class Mysql and defines the table's primary key.
    public function __construct($table) {
        $this->helpers = System::loadClass($_SERVER['DOCUMENT_ROOT'] . "/engine/class.helpers.php", "helpers");

        $this->table = $table;

        $this->dbclass = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.dbclass.php", 'dbclass');
        $this->sql = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.sql.php", 'sql');

        $this->set_primary_key();
    }

    private function set_primary_key() {
        $this->primarykey = $this->dbclass->tablekey($this->table)->keyname;
    }

    public function _get_primary_key() {
        return $this->primarykey;
    }

    public function _set_table($tablename) {
        $this->table = $tablename;

        $this->set_primary_key();
    }

    public function _get_table() {
        return $this->table;
    }

    // Select fields from the table under the rules specified in conditions. Return a list of results. 
    // For more complex data selects, like joined tables results, build your own sql using methods from Sql class.
    public function _get($fields, $conditions = array()) {
        if (!is_array($fields) && !is_string($fields)) {
            return false;
        }

        if (!is_array($conditions)) {
            return false;
        }

        $sql = $this->sql
                ->select($fields, $this->table)
                ->where($conditions)
                ->output();
        
        if ($result = $this->dbclass->query($sql)) {
            return $result;
        } else
            return false;
    }

    // Save on database data passed in dataset, under the rules specified in conditions.
    public function _save($dataset, $conditions = array()) {
        $dataset = (array) $dataset;
        if (!empty($conditions)) {
            $sql = $this->sql
                    ->update($dataset, $this->table)
                    ->where($conditions);
        } else {
            if (isset($dataset[$this->primarykey]))
                unset($dataset[$this->primarykey]);
            $sql = $this->sql->insert($dataset, $this->table);
        }

        return $this->dbclass->query($sql->output());
    }

    // Delete data from table under the rules specified in conditions.
    public function _delete($conditions) {
        $sql = $this->sql
                ->delete($this->table)
                ->where($conditions);

        return $this->dbclass->query($sql->output());
    }

}

?>