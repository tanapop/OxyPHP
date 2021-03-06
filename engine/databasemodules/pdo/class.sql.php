<?php

/* ///////////////////////////
  PDO SQLOBJ PROTOTYPE CLASS//
 *////////////////////////////

class Sqlobj {
    // SQL string, itself.
    public $sqlstring;
    // The values to be inserted in sql.
    public $sqlvalues;
    
    public function __construct($str, $vals){
        $this->sqlstring = $str;
        $this->sqlvalues = $vals;
    }
}

/* ////////////////////////////
  PDO SQL QUERY BUILDER CLASS//
 */////////////////////////////
class Sql {

    // SQL string, itself.
    private $sqlstring;
    // The values to be inserted in sql.
    private $sqlvalues;

    public function __construct() {
        $this->sqlstring = "";
        $this->sqlvalues = array();
    }

    // Build a insert type query string with argument passed in dataset.
    public function insert($dataset, $table) {
        $fields = "";
        $values = " VALUES (";
        $arrVals = array();

        foreach ($dataset as $key => $val) {
            if (!empty($val)) {
                $fields .= $this->escape($key) . ",";
                $values .= "?,";
                $arrVals[] = $val;
            }
        }
        $fields = rtrim($fields, ",") . ")";
        $values = rtrim($values, ",") . ")";

        $this->write("INSERT INTO " . $this->escape($table). " (" . $fields . $values, $arrVals);
        return $this;
    }

    // Build a update type query string with argument passed in dataset.
    public function update($dataset, $table, $conditions = array()) {
        $sql = "UPDATE " . $this->escape($table) . " SET ";
        foreach ($dataset as $key => $val) {
            if (!is_null($val) && $val !== false) {
                $sql .= $this->escape($key) . "= ? ,";
            }
        }
        $sql = rtrim($sql, " ,");

        $this->write($sql, array_merge(array_values($dataset), array_values($conditions)));
        return $this;
    }

    // Build a select type query string with argument passed in dataset.
    public function select($fields, $table, $conditions = array()) {
        $sql = "SELECT ";
        foreach ($fields as $f) {
            $sql .= $this->escape($f) . ",";
        }
        $sql = rtrim($sql, ",");

        $this->write($sql . " FROM " . $this->escape($table), $conditions);
        return $this;
    }

    // Build a delete type query string with argument passed in dataset.
    public function delete($table, $conditions = array()) {
        $arrvalues = array();
        foreach($conditions as $c){
            if(is_array($c)){
                $arrvalues = array_merge($arrvalues, $c);
            }else{
                $arrvalues[] = $c;
            }
        }
        
        $this->write("DELETE ".$this->escape($table)." FROM " . $this->escape($table), $arrvalues);
        return $this;
    }

    /* Build a Mysql where clause string based on conditions passed on params,
     * the join OR or AND and operator as = or LIKE.
     */

    public function where($params, $table = null, $join = 'AND', $operator = '=') {
        $where = '';
        if (!empty($params)) {
            if (is_array($params)) {
                $_conditions = array();
                foreach ($params as $key => $val) {
                    $key = (!empty($table) ? $table.".".$this->escape($key)  : $this->escape($key));
                    if (strtoupper($operator) == "LIKE") {
                        $_conditions[] = $key . ' LIKE ? ';
                    } else if (is_array($val) && !empty($val)) {
                        $joined_values = array();

                        foreach ($val as $in_val) {
                            $joined_values[] = ' ? ';
                        }

                        $_conditions[] = $key . ' IN (' . join(',', $joined_values) . ')';
                    } else {
                        $_conditions[] = $key . $operator . ' ? ';
                    }
                }
                $join = strtoupper($join);
                $join = 'AND' == $join || 'OR' == $join ? " {$join} " : null;

                $where = $join !== null ? ' WHERE ' . join($join, $_conditions) : '';
            } else {
                System::log("sql_error",'Error message: ' . 'Where clause conditions must be an array.');
            }
        }

        $this->write($where, array(), false);

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

        $this->write($str, array(), false);
        return $this;
    }

    private function condition($factor1, $factor2, $operator = "=") {
        $factor1 = (is_array($factor1) ? $factor1[0] . '.' . $this->escape($factor1[1]) : (is_numeric($factor1) ? $factor1 : "'" . $factor1 . "'"));
        $factor2 = (is_array($factor2) ? $factor2[0] . '.' . $this->escape($factor2[1]) : (is_numeric($factor2) ? $factor2 : "'" . $factor2 . "'"));

        return $factor1 . $operator . $factor2;
    }

    // Register SQL query data, then return the object.
    public function write($sqlstr, $values, $overwrite = true) {
        if ($overwrite) {
            $this->sqlstring = $sqlstr;
            $this->sqlvalues = $values;
        } else {
            $this->sqlstring .= $sqlstr;
            $this->sqlvalues = array_merge($this->sqlvalues, $values);
        }
        return $this;
    }
    
    private function escape($val){
        return $val == "*" ? $val : "`".$val."`";
    }

    public function output() {
        return new Sqlobj($this->sqlstring, $this->sqlvalues);
    }

    // Erase SQL query data, then return the object.
    public function reset() {
        $this->sqlstring = "";
        $this->sqlvalues = array();
        return $this;
    }

}

?>