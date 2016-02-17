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

    protected function buildquery($type, $data) {
        if (!is_array($data)) {
            System::debug(array("class.model: Argument Error: data must be an array. In method build."), array($data));
        }
        return call_user_func_array(array($this, $type . "_query"), $data);
    }

    private function insert_query($dataset) {
        $sql = "INSERT INTO " . $this->table . " (";
        $fields = "";
        $values = " VALUES (";

        foreach ($dataset as $key => $val) {
            if (!empty($val)) {
                $fields .= $key . ",";
                $values .= (is_numeric($val) ? $val : "'" . $val . "',");
            }
        }
        $fields = rtrim($fields, ",") . ")";
        $values = rtrim($values, ",") . ")";
        
        $sql .= $fields.$values;
        return $sql;
    }

    private function update_query($dataset) {
        $sql = "UPDATE " . $this->table . " SET ";
        foreach ($dataset as $key => $val) {
            if (!empty($val))
                $sql .= $key . "=" . (is_numeric($val) ? $val : "'" . $val . "',");
        }
        $sql = rtrim($sql, ",");

        $sql .= " WHERE id=" . $dataset['id'];
        
        return $sql;
    }

    private function select_query($fields, $conditions) {
        $sql = "SELECT ";
        foreach ($fields as $f) {
            $sql .= $f . ",";
        }
        $sql = rtrim($sql, ",");

        $sql .= " FROM " . $this->table;

        if (!empty($conditions)) {
            $sql .= " WHERE ";
            foreach ($conditions as $key => $val) {
                $sql .= $key . "=" . (is_numeric($val) ? $val : "'" . $val . "'");
                $sql .= " AND ";
            }
            $sql = rtrim($sql, " AND ");
        }

        return $sql;
    }

    private function delete_query($list) {
        $sql = "DELETE FROM " . $this->table . " WHERE id IN (";
        foreach ($list as $id) {
            $sql .= $id . ",";
        }
        $sql = rtrim($sql, ",");
        $sql .= ")";

        return $sql;
    }

}

?>