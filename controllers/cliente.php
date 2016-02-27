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
        $this->_view("register", array("dataset" => (!empty($id) ? $this->model->row("*", array("id" => $id)) : array())));
        System::showAlerts();
    }

    public function get($fields, $conditions = array()) {
        return $this->model->get($fields, $conditions);
    }

    public function save($dataset) {
        foreach($_FILES as $k => $f){
if ($_FILES[$k]["size"]) {
$dataset[$k] = $_FILES[$k]["type"].";".file_get_contents($_FILES[$k]["tmp_name"]);
}
}
        $primarykey = $this->model->_get_primary_key();
        
        if ($this->model->save($dataset, (empty($dataset[$primarykey]) ? array() : array($primarykey => $dataset[$primarykey])))) {
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
            System::debug(array('Argument type error'=>'Argument "list" must be an integer or an array.'));
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