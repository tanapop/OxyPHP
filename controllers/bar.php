<?php

class Bar extends Controller {

    public function index() {
        $this->listing();
    }

    public function listing() {
        $this->_view("listing", array("dataset" => $this->get("*")));
        $this->helpers->phpalert->show();
    }

    public function register($id = null) {
        $this->_view("register", array("dataset" => (!empty($id) ? $this->model->row("*", array("id" => $id)) : array())));
        $this->helpers->phpalert->show();
    }

    public function get($fields, $conditions = array()) {
        return $this->model->get($fields, $conditions);
    }

    public function save($dataset) {
        
        $primarykey = $this->model->_get_primary_key();
        
        if ($this->model->save($dataset, (empty($dataset[$primarykey]) ? array() : array($primarykey => $dataset[$primarykey])))) {
            $this->helpers->phpalert->add("The data was successfully saved!", "success");
        } else {
            $this->helpers->phpalert->add("Attempt to save data failed!", "failure");
        }

        header('Location: /bar');
    }

    public function delete($list) {
        if (is_numeric($list)) {
            $list = array($list);
        } elseif (!is_array($list)) {
            $this->helpers->insecticide->debug(array('Argument type error'=>'Argument "list" must be an integer or an array.'));
        }
        
        if ($this->model->delete(array($this->model->_get_primary_key() => $list))) {
            $this->helpers->phpalert->add("The registers were deleted successfully!", "success");
        } else {
            $this->helpers->phpalert->add("Attempt to delete registers failed!", "failure");
        }

        header('Location: /bar');
    }
    
    public function download($args){
        $this->_downloadfile($args, uniqid());
    }

}

?>