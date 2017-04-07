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
        foreach ($this->dbclass->describeTable($this->table) as $row) {
            if ($row->Key == "PRI") {
                $this->primarykey = $row->Field;
                break;
            }
        }
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
    public function _get($fields, $conditions = array()) {
        if (is_string($fields)) {
            $fields = array($fields);
        } elseif (!is_array($fields)) {
            return false;
        }

        if (!is_array($conditions)) {
            return false;
        }

        $sql = $this->sql
                ->select($fields, $this->table, $conditions)
                ->where($conditions, $this->table);

        if ($result = $this->dbclass->query($sql->output())) {
            return $result;
        } else
            return false;
    }

    // Save on database data passed in dataset, under the rules specified in conditions.
    public function _save($dataset, $conditions = array()) {
        $dataset = (array) $dataset;
        if (!empty($conditions)) {
            $sql = $this->sql
                    ->update($dataset, $this->table, $conditions)
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
                ->delete($this->table, $conditions)
                ->where($conditions);

        return $this->dbclass->query($sql->output());
    }

}

?>