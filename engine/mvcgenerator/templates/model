<?php

class _CLASS_NAME_ extends Model {

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
            System::debug(array(), array("Mysql Query" => $this->buildquery("select", array($fields, $conditions))));
        } else {
            if ($result = $this->mysql->query($this->buildquery("select", array($fields, $conditions)))) {
                return $result;
            } else
                return false;
        }
    }
    
    // Return the first row from this->_get result.
    public function _row($fields, $conditions = array(), $debug = false){
        $return = $this->_get($fields, $conditions, $debug);
        return $return[0];
    }

    // Save on database data passed in dataset, under the rules specified in conditions.
    public function _save($dataset, $conditions = array(), $debug = false) {
        $dataset = (array) $dataset;
        if (!empty($conditions)) {
            $sql = $this->buildquery("update", array($dataset, $conditions));
        } else {
            if(isset($dataset[$this->primarykey]))
                unset($dataset[$this->primarykey]);
            $sql = $this->buildquery("insert", array($dataset));
        }

        if ($debug) {
            System::debug(array(), array("Mysql Query" => $sql));
        } else {
            return $this->mysql->query($sql);
        }
    }

    // Delete data from table under the rules specified in conditions.
    public function _delete($conditions, $debug = false) {
        if ($debug) {
            System::debug(array(), array("Mysql Query" => $this->buildquery("delete", array($conditions))));
        } else {
            return $this->mysql->query($this->buildquery("delete", array($conditions)));
        }
    }

}

?>