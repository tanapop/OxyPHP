<?php

class Cliente extends Controller {

    public function index() {
        $this->listing();
    }

    public function listing() {
        $this->_view("listing", array("dataset" => $this->get("*")));
        System::showAlerts();
    }

    public function register($id = null) {
//        $this->_view("register", "string de teste");
        $this->_view("registere", array("dataset" => (!empty($id) ? $this->model->row("*", array("id" => $id)) : array())));
        System::showAlerts();
    }

    public function get($fields, $conditions = array()) {
        return $this->model->get($fields, $conditions);
    }

    public function save($dataset) {
        if(!empty($_FILES)){
foreach($_FILES as $k => $f){
$dataset[$k] = $_FILES[$k]["type"].";".file_get_contents($_FILES[$k]["tmp_name"]);
}
}
        $primarykey = $this->model->_get_primary_key();
        $conditions = (empty($dataset[$primarykey]) ? array() : array($primarykey => $dataset[$primarykey]));
        
        if ($this->model->save($dataset, $conditions)) {
            System::setAlert("The data was successfully saved!", ALERT_SUCCESS);
        } else {
            System::setAlert("Attempt to save data failed!", ALERT_FAILURE);
        }

        header('Location: /cliente');
    }

    public function delete($list) {
        if (is_numeric($list)) {
            $list = array($list);
        } elseif (!is_array($list)) {
            return false;
        }
        
        if ($this->model->delete(array($this->model->_get_primary_key() => $list))) {
            System::setAlert("The registers were deleted successfully!", ALERT_SUCCESS);
        } else {
            System::setAlert("Attempt to delete registers failed!", ALERT_FAILURE);
        }

        header('Location: /cliente');
    }
    
    public function download($args){
        $this->_downloadfile($args, uniqid());
    }

}

?>