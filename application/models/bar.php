<?php

class ModelBar extends Model {

    // An alias for this::_get()
    public function get($fields, $conditions = array()) {
        $sql = $this->sql
                ->select($fields, $this->_get_table())
                ->join("foo", array(
                    array(
                        array("foo", "bar_id"),
                        array("bar", "id")
                    )
                        ), "LEFT")
                ->where($conditions);
        return $this->dbclass->query($sql->output());
    }

    // Return the first row from this::_get() result
    public function row($fields, $conditions = array()) {
        return $this->get($fields, $conditions)[0];
    }

    // An alias for this::_save()
    public function save($dataset, $conditions = array()) {
        return $this->_save($dataset, $conditions);
    }

    // An alias for this::_delete()
    public function delete($conditions) {
        return $this->_delete($conditions);
    }

}

?>