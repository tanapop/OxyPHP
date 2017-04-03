<?php

class ModelBar extends Model {

    // An alias for this::_get()
    public function get($fields, $conditions = array(), $debug = false) {
        $t = $this->_get_table();
        $k = $this->_get_primary_key();
        if (is_string($fields)) {
            $fields = array($fields);
        }

//        $sql = $this->sql->select($fields, $t)->join("foo", array(
//                    array(
//                        array($t, $k),
//                        array("foo", "bar_id")
//            )))->output();
        $sqlstr = "SELECT `bar`.id AS bar_id,`bar`.name AS bar_name, `foo`.name AS foo_name"
                . " FROM `bar` INNER JOIN `foo` ON bar.`id`=foo.`bar_id`";
        $sql = $this->sql->write($sqlstr, array(), $t)->output();

//        $this->helpers->insecticide->debug(array("Query" => $sql->sqlstring));
        return $this->dbclass->query($sql);

//
//        return $this->_get($fields, $conditions, $debug);
    }

    // Return the first row from this::_get() result
    public function row($fields, $conditions = array(), $debug = false) {
        return $this->_get($fields, $conditions, $debug)[0];
    }

    // An alias for this::_save()
    public function save($dataset, $conditions = array(), $debug = false) {
        return $this->_save($dataset, $conditions, $debug);
    }

    // An alias for this::_delete()
    public function delete($conditions, $debug = false) {
        return $this->_delete($conditions, $debug);
    }

}

?>