<?php

/* //////////////////////////
  MYSQLI QUERYBUILDER CLASS//
 *///////////////////////////

class Querybuilder {

    // The name of main table of this module. By default, it has the same name of the module itself.
    private $table;
    // An instance of the class Mysql.
    private $dbclass;

    public function __construct($dbclass) {
        $this->dbclass = $dbclass;
    }

    // This function is called from any model to build the query based on argument passed in type.
    public function build($type, $data, $table) {
        try {
            $this->table = $table;
            return call_user_func_array(array($this, $type . "_query"), $data);
        } catch (Exception $ex) {
            System::log("sql_error","Error message" . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }
    }

    // Build a insert type query string with argument passed in dataset and return it.
    private function insert_query($dataset) {
        $dataset = $this->dbclass->escapevar($dataset);

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

        return "INSERT INTO " . $this->table . " (" . $fields . $values;
    }

    // Build a update type query string with argument passed in dataset and return it.
    private function update_query($dataset, $conditions, $join = "AND", $operator = "=") {
        $dataset = $this->dbclass->escapevar($dataset);
        $conditions = $this->escapeParams($conditions);

        $sql = "UPDATE " . $this->table . " SET ";
        foreach ($dataset as $key => $val) {
            if (!is_null($val) && $val !== false) {
                $sql .= $key . "=" . (is_numeric($val) ? $val : "'" . $val . "'") . ",";
            }
        }
        $sql = rtrim($sql, ",");

        return $sql . $this->_whereClause($conditions, $join, $operator);
    }

    // Build a select type query string with argument passed in dataset and return it.
    private function select_query($fields, $conditions, $join = "AND", $operator = "=") {
        $fields = $this->dbclass->escapevar($fields);
        $conditions = $this->escapeParams($conditions);

        $sql = "SELECT ";
        foreach ($fields as $f) {
            $sql .= $f . ",";
        }
        $sql = rtrim($sql, ",");

        return $sql . " FROM " . $this->table . $this->_whereClause($conditions, $join, $operator);
    }

    // Build a delete type query string with argument passed in dataset and return it.
    private function delete_query($conditions, $join = "AND", $operator = "=") {
        $conditions = $this->escapeParams($conditions);

        return "DELETE FROM " . $this->table . $this->_whereClause($conditions, $join, $operator);
    }

    /* Build a Mysql where clause string based on conditions passed on params,
     * the join OR or AND and operator as = or LIKE, then return the string.
     */

    public function _whereClause($params = array(), $join = 'AND', $operator = '=') {
        $where = '';
        if (!empty($params)) {
            if (is_array($params)) {
                $_conditions = array();
                foreach ($params as $key => $val) {
                    if (strtoupper($operator) == "LIKE") {
                        $_conditions[] = $key . ' LIKE "%' . $val . '%"';
                    } else if (is_array($val) && !empty($val)) {
                        $joined_values = array();

                        foreach ($val as $in_val) {
                            $joined_values[] = is_numeric($in_val) ? $in_val : '"' . $in_val . '"';
                        }

                        $_conditions[] = $key . ' IN (' . join(',', $joined_values) . ')';
                    } else {
                        $_conditions[] = $key . $operator . (is_numeric($val) ? $val : '"' . $val . '"');
                    }
                }
                $join = strtoupper($join);
                $join = 'AND' == $join || 'OR' == $join ? " {$join} " : null;

                $where = $join !== null ? ' WHERE ' . join($join, $_conditions) : '';
            } else {
                $where = (string) $params;
            }
        }

        return $where;
    }

    public function escapeParams($params) {
        foreach ($params as $k => $p) {
            $params[$k] = $this->dbclass->escapevar($p);
        }
        return $params;
    }

}

?>