<?php

class _CLASS_NAME_ extends Model {

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
    
    public function _row($fields, $conditions = array(), $debug = false){
        $return = $this->_get($fields, $conditions, $debug);
        return $return[0];
    }

    public function _save($dataset, $debug = false) {
        $dataset = (array) $dataset;
        if (!empty($dataset["id"])) {
            $sql = $this->buildquery("update", array($dataset));
        } else {
            if(isset($dataset["id"]))
                unset($dataset["id"]);
            $sql = $this->buildquery("insert", array($dataset));
        }

        if ($debug) {
            System::debug(array(), array("Mysql Query" => $sql));
        } else {
            return $this->mysql->query($sql);
        }
    }

    public function _delete($list, $debug = false) {
        if (is_numeric($list)) {
            $list = array($list);
        } elseif (!is_array($list)) {
            return false;
        }

        if ($debug) {
            System::debug(array(), array("Mysql Query" => $this->buildquery("delete", array($list))));
        } else {
            return $this->mysql->query($this->buildquery("delete", array($list)));
        }
    }

}

?>