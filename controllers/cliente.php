<?php

class Cliente extends Controller {

    public function index() {
        $this->listing();
    }

    public function listing() {
        $this->view("listing", array("dataset" => $this->get("*")));
        System::showAlerts();
    }

    public function register($id = null) {
        $this->view("register", array("dataset" => (!empty($id) ? $this->model->_row("*", array("id" => $id)) : array())));
        System::showAlerts();
    }

    public function get($fields, $conditions = array()) {
        $_SESSION['teste'] = "teste";
        System::debug(array("olá, sou uma mensagem de debug.","segunda mensagem de debug."));
        return $this->model->_get($fields, $conditions);
    }

    public function save($dataset) {
        if(!empty($_FILES)){
foreach($_FILES as $k => $f){
$dataset[$k] = file_get_contents($_FILES[$k]["tmp_name"]);
}
}
        $conditions = (empty($dataset[$this->model->primarykey]) ? array() : array($this->model->primarykey => $dataset[$this->model->primarykey]));
        
        if ($this->model->_save($dataset, $conditions,true)) {
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
        
        if ($this->model->_delete(array($this->model->primarykey => $list))) {
            System::setAlert("The registers were deleted successfully!", ALERT_SUCCESS);
        } else {
            System::setAlert("Attempt to delete registers failed!", ALERT_FAILURE);
        }

        header('Location: /cliente');
    }

}

?>