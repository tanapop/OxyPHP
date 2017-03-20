<?php

class ModelProd extends Model {

    // An alias for this::_get()
    public function get($fields, $conditions = array(), $debug = false) {
        return $this->_get($fields, $conditions, $debug);
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