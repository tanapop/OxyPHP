<?php

class Model {

    // The name of main table of this module. By default, it has the same name of the module itself.
    public $table;
    // An instance of the class Mysql.
    protected $mysql;
    // The name of table's primary key.
    public $primarykey;

    // It sets the main table name, instantiate class Mysql and defines the table's primary key.
    public function __construct($table) {
        $this->table = $table;

        if (MYSQL_DATABASE_ON)
            $this->mysql = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databaseclasses/class.mysql.php", "Mysql");

        foreach ($this->mysql->query("DESCRIBE " . $this->table) as $row) {
            if ($row->Key == "PRI") {
                $this->primarykey = $row->Field;
                break;
            }
        }
    }

    // This function is called from any model to build the query based on argument passed in type.
    protected function _buildquery($type, $data) {
        if (!is_array($data)) {
            System::debug(array("class.model: Argument Error: data must be an array. In method build."), array($data));
        }
        return call_user_func_array(array($this, $type . "_query"), $data);
    }

    // Build a insert type query string with argument passed in dataset and return it.
    private function insert_query($dataset) {
        $dataset = $this->mysql->escapevar($dataset);

        $sql = "INSERT INTO " . $this->table . " (";
        $fields = "";
        $values = " VALUES (";

        foreach ($dataset as $key => $val) {
            if (!empty($val)) {
                $fields .= $key . ",";
                $values .= (is_numeric($val) ? $val : "'" . $val . "'") . ",";
            }
        }
        $fields = rtrim($fields, ",") . ")";
        $values = rtrim($values, ",") . ")";

        $sql .= $fields . $values;
        return $sql;
    }

    // Build a update type query string with argument passed in dataset and return it.
    private function update_query($dataset, $conditions, $join = "AND", $operator = "=") {
        $dataset = $this->mysql->escapevar($dataset);
        $conditions = $this->escapeParams($conditions);

        $sql = "UPDATE " . $this->table . " SET ";
        foreach ($dataset as $key => $val) {
            if (!is_null($val) && $val !== false) {
                $sql .= $key . "=" . (is_numeric($val) ? $val : "'" . $val . "'") . ",";
            }
        }
        $sql = rtrim($sql, ",");

        $sql .= $this->_whereClause($conditions, $join, $operator);

        return $sql;
    }

    // Build a select type query string with argument passed in dataset and return it.
    private function select_query($fields, $conditions, $join = "AND", $operator = "=") {
        $fields = $this->mysql->escapevar($fields);
        $conditions = $this->escapeParams($conditions);

        $sql = "SELECT ";
        foreach ($fields as $f) {
            $sql .= $f . ",";
        }
        $sql = rtrim($sql, ",");

        $sql .= " FROM " . $this->table . $this->_whereClause($conditions, $join, $operator);

        return $sql;
    }

    // Build a delete type query string with argument passed in dataset and return it.
    private function delete_query($conditions, $join = "AND", $operator = "=") {
        $conditions = $this->escapeParams($conditions);

        $sql = "DELETE FROM " . $this->table . $this->_whereClause($conditions, $join, $operator);

        return $sql;
    }

    private function escapeParams($params) {
        foreach ($params as $k => $p) {
            $params[$k] = $this->mysql->escapevar($p);
        }
        return $params;
    }

    /* Build a where clause string based on conditions passed on params,
     * the join OR or AND and operator as = or LIKE, then return the string.
     */

    protected function _whereClause($params = array(), $join = 'AND', $operator = '=') {
        $where = '';
        if (!empty($params)) {
            if (is_array($params)) {
                $_conditions = array();
                foreach ($params as $key => $val) {
                    if (strtoupper($operator) == "LIKE") {
                        $_conditions[] = "{$key} LIKE '%{$val}%'";
                    } else if (is_array($val) && !empty($val)) {
                        $joined_values = array();

                        foreach ($val as $in_val) {
                            $joined_values[] = is_numeric($in_val) ? $in_val : "'{$in_val}'";
                        }

                        $joined_values = join(',', $joined_values);

                        $_conditions[] = "{$key} IN ({$joined_values})";
                    } else {
                        $_conditions[] = "{$key} {$operator} {$val}";
                    }
                }
                $join = strtoupper($join);
                $join = 'AND' == $join || 'OR' == $join ? " {$join} " : null;

                $prefix = ' WHERE ';

                $where = $join !== null ? $prefix . join($join, $_conditions) : '';
            } else {
                $where = (string) $params;
            }
        }

        return $where;
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
            if ($result = $this->mysql->query($this->_buildquery("select", array($fields, $conditions)))) {
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
            return $this->mysql->query($sql);
        }
    }

    // Delete data from table under the rules specified in conditions.
    public function _delete($conditions, $debug = false) {
        if ($debug) {
            System::debug(array("Mysql Query" => $this->_buildquery("delete", array($fields, $conditions))), array());
        } else {
            return $this->mysql->query($this->_buildquery("delete", array($conditions)));
        }
    }

}

?>