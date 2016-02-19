<?php

class Pedido extends Controller {

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
        return $this->model->_get($fields, $conditions);
    }

    public function save($dataset) {
        if ($this->model->_save($dataset)) {
            System::setAlert("The data was successfully saved!", ALERT_SUCCESS);
        } else {
            System::setAlert("Attempt to save data failed!", ALERT_FAILURE);
        }
    }

    public function delete($list) {
        if ($this->model->_delete($list)) {
            System::setAlert("The registers were deleted successfully!", ALERT_SUCCESS);
        } else {
            System::setAlert("Attempt to delete registers failed!", ALERT_FAILURE);
        }
    }

}

?>