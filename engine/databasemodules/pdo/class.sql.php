<?php

/* ///////////////////////////
  PDO SQLOBJ PACKAGE CLASS////
 *////////////////////////////

class Sqlobj {

    // SQL string, itself.
    public $sqlstring;
    // The values to be inserted in sql.
    public $sqlvalues;
    // Current table name.
    public $table;
    // Map data?
    public $mapdata;

    public function __construct($str, $vals, $table, $mapdataflag) {
        $this->sqlstring = $str;
        $this->sqlvalues = $vals;
        $this->table = $table;
        $this->mapdata = $mapdataflag;
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
    // Current table name.
    private $table;
    // Map data flag.
    private $mapdata;
    // An instance of the class Mysql.
    private $dbclass;

    public function __construct() {
        $this->sqlstring = "";
        $this->sqlvalues = array();
        $this->mapdata = false;
        $this->dbclass = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/pdo/class.dbclass.php", 'dbclass');
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

        $this->write("INSERT INTO " . $this->escape($table) . " (" . $fields . $values, $arrVals, $table);
        return $this;
    }

    // Build a update type query string with argument passed in dataset.
    public function update($dataset, $table) {
        $sql = "UPDATE " . $this->escape($table) . " SET ";
        foreach ($dataset as $key => $val) {
            if (!is_null($val) && $val !== false) {
                $sql .= $this->escape($key) . "= ? ,";
            }
        }
        $sql = rtrim($sql, " ,");

        $this->write($sql, $dataset, $table);
        return $this;
    }

    // Build a select type query string with argument passed in dataset.
    public function select($fields, $table) {
        if (is_string($fields)) {
            $fields = array($fields);
        }
        $tb_key = $this->dbclass->tablekey($table);

        $sql = "SELECT " . $table . "." . $this->escape($tb_key->keyname) . " AS " . $this->escape($tb_key->keyalias) . ",";
        foreach ($fields as $f) {
            if (is_array($f)) {
                if ($f[1] === "*") {
                    foreach ($this->dbclass->describeTable($f[0]) as $c) {
                        $sql .= $f[0] . "." . $this->escape($c->Field) . " AS " . $this->escape($f[0] . "_" . $c->Field) . ",";
                    }
                    $sql = rtrim($sql, ",");
                } else {
                    $sql .= $f[0] . "." . $this->escape($f[1]) . " AS " . $this->escape($f[0] . "_" . $f[1]);
                }
            } else {
                if ($f === "*") {
                    foreach ($this->dbclass->describeTable($table) as $c) {
                        $sql .= $table . "." . $this->escape($c->Field) . " AS " . $this->escape($table . "_" . $c->Field) . ",";
                    }
                    $sql = rtrim($sql, ",");
                } else {
                    $sql .= $table . "." . $this->escape($f) . " AS " . $this->escape($table . "_" . $f);
                }
            }
            $sql .= ",";
        }
        $sql = rtrim($sql, ",");

        $this->write($sql . " FROM " . $this->escape($table), array(), $table);
        return $this;
    }

    // Build a delete type query string with argument passed in dataset.
    public function delete($table) {
        $this->write("DELETE " . $this->escape($table) . " FROM " . $this->escape($table), array(), $table);
        return $this;
    }

    /* Build a Mysql where clause string based on conditions passed on params,
     * the join OR or AND and operator as = or LIKE.
     */

    public function where($params, $join = 'AND', $operator = '=') {
        $where = '';
        if (!empty($params)) {
            if (is_array($params)) {
                $_conditions = array();
                foreach ($params as $key => $val) {
                    $key = $this->table . "." . $this->escape($key);
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
                System::log("sql_error", 'Error message: ' . 'Where clause conditions must be an array.');
            }
        }

        if (is_array($params)) {
            $arrvalues = array();
            foreach ($params as $c) {
                if (is_array($c)) {
                    $arrvalues = array_merge($arrvalues, $c);
                } else {
                    $arrvalues[] = $c;
                }
            }
        }

        $this->write($where, (isset($arrvalues) ? $arrvalues : array()), null, false);

        return $this;
    }

    public function join($table2join, $matches, $way = 'INNER', $operators = "=", $joint = 'AND') {
        $str = " " . $way . " JOIN " . $this->escape($table2join) . " ON ";
        $counter = 0;
        foreach ($matches as $m) {
            $str .= $this->condition($m[0], $m[1], (is_array($operators) ? $operators[$counter] : $operators)) . " " . (is_array($joint) ? $joint[$counter] : $joint) . " ";
            $counter++;
        }
        $str = rtrim($str, " " . $joint . " ");

        $this->write($str, array(), null, false);
        $this->mapdata = true;
        return $this;
    }

    private function condition($factor1, $factor2, $operator = "=") {
//        $factor1 = (is_array($factor1) ? $factor1[0] . '.' . $this->escape($factor1[1]) : (is_numeric($factor1) ? $factor1 : "'" . $factor1 . "'"));
        if (is_array($factor1)) {
            $factor1 = $factor1[0] . '.' . $this->escape($factor1[1]);
        } else {
            array_push($this->sqlvalues, $factor1);
            $factor1 = "?";
        }
        if (is_array($factor2)) {
            $factor2 = $factor2[0] . '.' . $this->escape($factor2[1]);
        } else {
            array_push($this->sqlvalues, $factor2);
            $factor2 = "?";
        }

        return $factor1 . $operator . $factor2;
    }

    // Register SQL query data, then return the object.
    public function write($sqlstr, $values, $table, $overwrite = true) {
        if ($overwrite) {
            $this->sqlstring = $sqlstr;
            $this->sqlvalues = $values;
            $this->table = $table;
        } else {
            $this->sqlstring .= $sqlstr;
            $this->sqlvalues = array_merge($this->sqlvalues, array_values($values));
        }
        return $this;
    }

    private function escape($val) {
        return $val == "*" ? $val : "`" . $val . "`";
    }

    public function output() {
        return new Sqlobj($this->sqlstring, $this->sqlvalues, $this->table, $this->mapdata);
    }

    // Erase SQL query data, then return the object.
    public function reset() {
        $this->sqlstring = "";
        $this->sqlvalues = array();
        return $this;
    }

}

?>