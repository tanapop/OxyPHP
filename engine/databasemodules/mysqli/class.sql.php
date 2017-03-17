<?php

/* ///////////////////////////////
  MYSQLI SQL QUERY BUILDER CLASS//
 *////////////////////////////////

class Sql {

    // SQL string, itself.
    private $sqlstring;
    // An instance of the class Mysql.
    private $dbclass;

    public function __construct() {
        $this->dbclass = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/mysqli/class.dbclass.php", 'dbclass');
        $this->sqlstring = "";
    }

    // Build a insert type query string with argument passed in dataset and return it.
    public function insert($dataset, $table) {
        $dataset = $this->dbclass->escapevar($dataset);

        $fields = "";
        $values = " VALUES (";

        foreach ($dataset as $key => $val) {
            if (!empty($val)) {
                $fields .= $this->escape($key) . ",";
                $values .= (is_numeric($val) ? $val : "'" . $val . "'") . ",";
            }
        }
        $fields = rtrim($fields, ",") . ")";
        $values = rtrim($values, ",") . ")";

        $this->write("INSERT INTO " . $this->escape($table) . " (" . $fields . $values);
        return $this;
    }

    // Build a update type query string with argument passed in dataset and return it.
    public function update($dataset, $table, $conditions = null) {
        unset($conditions);
        $dataset = $this->dbclass->escapevar($dataset);

        $sql = "UPDATE " . $this->escape($table) . " SET ";
        foreach ($dataset as $key => $val) {
            if (!is_null($val) && $val !== false) {
                $sql .= $this->escape($key) . "=" . (is_numeric($val) ? $val : "'" . $val . "'") . ",";
            }
        }
        $sql = rtrim($sql, ",");

        $this->write($sql);
        return $this;
    }

    // Build a select type query string with argument passed in dataset and return it.
    public function select($fields, $table, $conditions = null) {
        unset($conditions);
        $fields = $this->dbclass->escapevar($fields);

        $sql = "SELECT ";
        foreach ($fields as $f) {
            $sql .= $this->escape($f) . ",";
        }
        $sql = rtrim($sql, ",");
        
        $this->write($sql . " FROM " . $this->escape($table));
        return $this;
    }

    // Build a delete type query string with argument passed in dataset and return it.
    public function delete($table, $conditions = null) {
        unset($conditions);

        $this->write("DELETE ".$this->escape($table)." FROM " . $this->escape($table));
        return $this;
    }

    /* Build a Mysql where clause string based on conditions passed on params,
     * the join OR or AND and operator as = or LIKE, then return the string.
     */

    public function where($params, $table = null, $join = 'AND', $operator = '=') {
        $where = '';
        if (!empty($params)) {
            if (is_array($params)) {
                $_conditions = array();
                foreach ($params as $key => $val) {
                    $key = (!empty($table) ? $table.".".$this->escape($key)  : $this->escape($key));
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
                System::log("sql_error",'Error message: ' . 'Where clause conditions must be an array.');
            }
        }
        
        $this->write($where, false);

        return $this;
    }
    
    public function join($table2join, $matches, $way = 'INNER', $operators = "=", $joint = 'AND') {
        $str = " " . $way . " JOIN " . $this->escape($table2join) . " ON ";
        $counter = 0;
        foreach ($matches as $m) {
            $str .= $this->condition($m[0], $m[1], (is_array($operators) ? $operators[$counter] : $operators)) . " " . $joint . " ";
            $counter++;
        }
        $str = rtrim($str, " " . $joint . " ");

        $this->write($str, false);
        return $this;
    }
    
    private function condition($factor1, $factor2, $operator = "=") {
        $factor1 = (is_array($factor1) ? $factor1[0] . '.' . $this->escape($factor1[1]) : (is_numeric($factor1) ? $factor1 : "'" . $factor1 . "'"));
        $factor2 = (is_array($factor2) ? $factor2[0] . '.' . $this->escape($factor2[1]) : (is_numeric($factor2) ? $factor2 : "'" . $factor2 . "'"));

        return $factor1 . $operator . $factor2;
    }
    
    // Register SQL query data, then return the object.
    public function write($sqlstr, $overwrite = true) {
        if ($overwrite) {
            $this->sqlstring = $sqlstr;
        } else {
            $this->sqlstring .= $sqlstr;
        }
        return $this;
    }
    
    private function escape($val){
        return $val == "*" ? $val : "`".$val."`";
    }
    
    public function output(){
        return $this->sqlstring;
    }
    
    public function reset(){
        $this->sqlstring = "";
        return $this;
    }

    public function escapeArgs($params) {
        foreach ($params as $k => $p) {
            $params[$k] = $this->dbclass->escapevar($p);
        }
        return $params;
    }
    

}

?>