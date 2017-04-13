<?php

class ModelExample extends Model {

    // An alias for this::_get()
    public function get($fields, $conditions = array()) {
        return $this->_get($fields, $conditions);
    }

    // Return the first row from this::_get() result
    public function row($fields, $conditions = array()) {
        return $this->_get($fields, $conditions)[0];
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