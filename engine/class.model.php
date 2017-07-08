<?php

class Model {

    // The name of table's primary key.
    private $tbmetadata;
    // The name of main table of this module. By default, it has the same name of the module itself.
    private $table;
    // An instance of the class Dblink.
    protected $dblink;
    // An instance of the class Sql.
    protected $sql;
    // The global object of Helpers class
    protected $helpers;

    // It sets the main table name, instantiate class Mysql and defines the table's primary key.
    public function __construct($table) {
        require_once $_SERVER['DOCUMENT_ROOT'] . "/engine/databasemodules/" . DBCLASS . "/class.tbmetadata.php";
        $this->helpers = System::loadClass($_SERVER['DOCUMENT_ROOT'] . "/engine/class.helpers.php", "helpers");

        $this->table = $table;

        $this->dblink = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.dblink.php", 'dblink');
        $this->sql = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.sql.php", 'sql');

        $this->upd_metadata();
    }

    private function upd_metadata() {
        $this->tbmetadata = Tbmetadata::info($this->table);
    }

    private function mapdata($dataset, $key) {
        if (!$key) {
            System::log("db_error", date('m/d/Y h:i:s') . " - NOTICE: Table from where you selected data has not a primary key. So, dataset could not be mapped. It is extremely recommended to define primary keys for all your database tables.");
            return $dataset;
        }

        $result = array();

        foreach ($dataset as $row) {
            if (!isset($result[$row->$key])) {
                $result[$row->$key] = $row;
            } else {
                foreach ((array) $row as $k => $v) {
                    if ($result[$row->$key]->$k != $v) {
                        if (!is_array($result[$row->$key]->$k)) {
                            $result[$row->$key]->$k = array($result[$row->$key]->$k, $v);
                        } else {
                            $arr = $result[$row->$key]->$k;
                            $arr[] = $v;
                            ;
                            $result[$row->$key]->$k = $arr;
                        }
                    }
                }
            }
        }
        return array_values($result);
    }

    protected function dbquery(Sqlobj $sqlobj) {
        $db_res = $this->dblink->runsql($sqlobj);

        if (is_array($db_res) && $sqlobj->mapdata === true) {
            $db_res = $this->mapdata($db_res, $this->tbmetadata->key->keyalias);
        }
        
        return $db_res;
    }

    public function _get_primary_key() {
        return $this->tbmetadata->key->keyname;
    }

    public function _set_table($tablename) {
        $this->table = $tablename;

        $this->upd_metadata();
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

        if ($result = $this->dbquery($sql)) {
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
            if (isset($dataset[$this->_get_primary_key()]))
                unset($dataset[$this->_get_primary_key()]);
            $sql = $this->sql->insert($dataset, $this->table);
        }

        return $this->dbquery($sql->output());
    }

    // Delete data from table under the rules specified in conditions.
    public function _delete($conditions) {
        $sql = $this->sql
                ->delete($this->table)
                ->where($conditions);

        return $this->dbquery($sql->output());
    }

}

?>