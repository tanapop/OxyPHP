<?php

/* ///////////////////////
  PDO QUERYBUILDER CLASS//
 *////////////////////////

class Querybuilder {

    // The name of main table of this module. By default, it has the same name of the module itself.
    private $table;

    public function __construct() {
        
    }

    // This function is called from any model to build the query based on argument passed in type.
    public function build($type, $data, $table) {
        try {
            $this->table = $table;
            return call_user_func_array(array($this, $type . "_query"), $data);
        } catch (Exception $ex) {
            System::debug(array("Error message" => $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.'), array('Parameter type' => $type, 'Parameter data' => $data));
        }
    }

    // Build a insert type query string with argument passed in dataset and return it.
    private function insert_query($dataset) {
        $fields = "";
        $values = " VALUES (";
        $arrVals = array();

        foreach ($dataset as $key => $val) {
            if (!empty($val)) {
                $fields .= $key . ",";
                $values .= "?,";
                $arrVals[] = $val;
            }
        }
        $fields = rtrim($fields, ",") . ")";
        $values = rtrim($values, ",") . ")";

        return array("INSERT INTO " . $this->table . " (" . $fields . $values, $arrVals);
    }

    // Build a update type query string with argument passed in dataset and return it.
    private function update_query($dataset, $conditions, $join = "AND", $operator = "=") {
        $sql = "UPDATE " . $this->table . " SET ";
        foreach ($dataset as $key => $val) {
            if (!is_null($val) && $val !== false) {
                $sql .= $key . "= ? ,";
            }
        }
        $sql = rtrim($sql, ",");

        return array($sql . $this->_whereClause($conditions, $join, $operator), array_merge(array_values($dataset),array_values($conditions)));
    }

    // Build a select type query string with argument passed in dataset and return it.
    private function select_query($fields, $conditions, $join = "AND", $operator = "=") {
        $sql = "SELECT ";
        foreach ($fields as $f) {
            $sql .= $f . ",";
        }
        $sql = rtrim($sql, ",");

        return array($sql . " FROM " . $this->table . $this->_whereClause($conditions, $join, $operator), $conditions);
    }

    // Build a delete type query string with argument passed in dataset and return it.
    private function delete_query($conditions, $join = "AND", $operator = "=") {
        
        return array("DELETE FROM " . $this->table . $this->_whereClause($conditions, $join, $operator), array_values($conditions)[0]);
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
                System::debug(array('Error message'=>'Where clause conditions must be an array.'),array('Arg passed on $conditions'=>$params));
            }
        }

        return $where;
    }

}

?>