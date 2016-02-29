<?php

class Model extends Querybuilder{

    // The name of table's primary key.
    private $primarykey;

    // It sets the main table name, instantiate class Mysql and defines the table's primary key.
    public function __construct($table) {
        $this->table = $table;

        $this->dbclass = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databaseclasses/class." . DBCLASS . ".php", 'oxy'.DBCLASS);

        $this->set_primary_key();
    }

    private function set_primary_key() {
        foreach ($this->dbclass->query("DESCRIBE " . $this->table) as $row) {
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
            System::debug(array("Mysql Query" => $this->_buildquery("select", array($fields, $conditions))), array());
        } else {
            if ($result = $this->dbclass->query($this->_buildquery("select", array($fields, $conditions)))) {
                return $result;
            } else
                return false;
        }
    }

    // Save on database data passed in dataset, under the rules specified in conditions.
    public function _save($dataset, $conditions = array(), $debug = false) {
        $dataset = (array) $dataset;
        if (!empty($conditions)) {
            $sql = $this->_buildquery("update", array($dataset, $conditions));
        } else {
            if (isset($dataset[$this->primarykey]))
                unset($dataset[$this->primarykey]);
            $sql = $this->_buildquery("insert", array($dataset));
        }

        if ($debug) {
            System::debug(array("Mysql Query" => $sql), array());
        } else {
            return $this->dbclass->query($sql);
        }
    }

    // Delete data from table under the rules specified in conditions.
    public function _delete($conditions, $debug = false) {
        if ($debug) {
            System::debug(array("Mysql Query" => $this->_buildquery("delete", array($fields, $conditions))), array());
        } else {
            return $this->dbclass->query($this->_buildquery("delete", array($conditions)));
        }
    }

}

?>