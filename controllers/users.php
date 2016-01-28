<?php

class Users extends Controller {

    private $model;
    private $module;

    public function __construct() {
        $this->module = "usuarios";

        require_once $_SERVER['DOCUMENT_ROOT'] . "/models/" . $this->module . ".php";

        $classname = "Model" . ucfirst($this->module);
        $this->model = new $classname();
    }
    
    public function getFromDB($data){
        return $this->model->_get($data);
    }

    public function listFromDB() {
        global $system;
        $system->auth();
        
        $list = $this->model->_list();

        self::view("common", "top");
        self::view($this->module, "list", array("list" => $list));
        self::view("common", "footer");
    }
    
    public function saveToDB() {
        global $system;
        $system->auth();

        if (empty($_POST['id']))
            unset($_POST['id']);

        $_POST['senha'] = (!empty($_POST['senha']) ? md5($_POST['senha']) : "");

        if ($this->model->_save((object) $_POST)) {
            $system->alert("As informações foram salvas com sucesso.", ALERT_SUCCESS);
        } else {
            $system->alert("Ocorreu uma falha. As infomações não foram salvas.", ALERT_ERROR);
        }

        header("Location: /?c=" . $this->module . "&a=lista");
    }

    public function deleteFromDB() {
        global $system;
        $system->auth();

        if (!empty($_REQUEST['id'])) {
            $id = $_REQUEST['id'];
            if ($this->model->_delete($id)) {
                $system->alert("O registro foi excluído.", ALERT_SUCCESS);
            } else {
                $system->alert("Ocorreu uma falha inesperada. O registro não pôde ser excluído.", ALERT_ERROR);
            }
        } else {
            $system->alert("O sistema não pôde identificar o registro a ser excluído.", ALERT_ERROR);
        }

        header("Location: /?c=" . $this->module . "&a=lista");
    }

    public function login() {
        global $system;

        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $db_user = $this->model->_get(array("email" => $_POST['email']));

            if (!empty($db_user)) {
                if ($db_user->senha === md5($_POST['senha'])) {
                    $_SESSION['user'] = $db_user;
                } else {
                    $system->alert("O usuário e senha informados não conferem.");
                }
            } else {
                $system->alert("Não há nenhum usuário registrado com o email informado.", ALERT_ERROR);
            }
        } else {
            $system->alert("O email informado não é um endereço válido.", ALERT_ERROR);
        }

        header("Location: /");
    }

    public function logout($msg = null) {
        global $system;

        session_destroy();
        session_start();
        
        if (!empty($msg)) {
            $system->alert($msg);
        }
        
        header("Location: /");
        die();
    }

}

?>