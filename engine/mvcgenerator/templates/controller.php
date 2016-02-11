<?php

class _CLASS_NAME_ extends Controller {
    
    public function index(){
        $this->listing();
    }
    
    public function listing(){
        $this->view("listing", array("dataset" => $this->get("*")));
    }
    
    public function register($id = null){
        $this->view("register", array("dataset" => (!empty($id) ? $this->get("*", array("id" => $id)) : array())));
    }
    
    public function get($fields, $conditions = null) {
        return $this->model->_get($fields, $conditions);
    }

    public function save() {
        
    }

    public function delete($list) {
        if($this->model->_delete($list)){
            System::setAlert("The registers were deleted successfully!", ALERT_SUCCESS);
        } else{
            System::setAlert("Attempt to delete registers failed!", ALERT_FAILURE);
        }
    }
}

?>