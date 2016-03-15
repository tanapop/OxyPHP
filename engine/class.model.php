<?php

class Model {

    // The name of table's primary key.
    private $primarykey;
    // The name of main table of this module. By default, it has the same name of the module itself.
    private $table;
    // An instance of the class Mysql.
    private $dbclass;

    // It sets the main table name, instantiate class Mysql and defines the table's primary key.
    public function __construct($table) {
        $this->table = $table;

        $this->dbclass = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.dbclass.php", 'dbclass');

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

    protected function dbquery($args) {
        if (!is_array($args))
            $args = array($args);

        return call_user_func_array(array($this->dbclass, 'query'), $args);
    }

    // Select fields from the table under the rules specified in conditions. Return a list of results.
    public function _get($fields, $conditions = array(), $debug = false) {
        if (is_string($fields)) {
            $fields = array($fields);
        } elseif (!is_array($fields)) {
            return false;
        }

        if (!is_array($conditions)) {
            return false;
        }

        if ($debug) {
            System::debug(array(), array("SQL" => $this->dbclass->querybuilder->build("select", array($fields, $conditions), $this->table)));
        } else {
            if ($result = $this->dbquery($this->dbclass->querybuilder->build("select", array($fields, $conditions), $this->table))) {
                return $result;
            } else
                return false;
        }
    }

    // Save on database data passed in dataset, under the rules specified in conditions.
    public function _save($dataset, $conditions = array(), $debug = false) {
        $dataset = (array) $dataset;
        if (!empty($conditions)) {
            $sql = $this->dbclass->querybuilder->build("update", array($dataset, $conditions), $this->table);
        } else {
            if (isset($dataset[$this->primarykey]))
                unset($dataset[$this->primarykey]);
            $sql = $this->dbclass->querybuilder->build("insert", array($dataset), $this->table);
        }

        if ($debug) {
            System::debug(array(), array("SQL" => $sql));
        } else {
            return $this->dbquery($sql);
        }
    }

    // Delete data from table under the rules specified in conditions.
    public function _delete($conditions, $debug = false) {
        if ($debug) {
            System::debug(array(), array("SQL" => $this->dbclass->querybuilder->build("delete", array($conditions), $this->table)));
        } else {
            
            return $this->dbquery($this->dbclass->querybuilder->build("delete", array($conditions), $this->table));
        }
    }

}

?>