<?php

class Users extends Controller {

    private $model;
    private $module;
    private $system;

    public function __construct() {
        global $system;
        $this->system = $system;
        $this->module = "users";

        require_once $_SERVER['DOCUMENT_ROOT'] . "/models/" . $this->module . ".php";

        $classname = "Model" . ucfirst($this->module);
        $this->model = new $classname();
    }
    
    public function getFromDB($data){
        return $this->model->_get($data);
    }

    public function listFromDB() {
        $this->auth();
        
        $list = $this->model->_list();

        self::view("common", "top");
        self::view($this->module, "list", array("list" => $list));
        self::view("common", "footer");
    }
    
    public function saveToDB() {
        $this->auth();

        if (empty($_POST['id']))
            unset($_POST['id']);

        $_POST['senha'] = (!empty($_POST['senha']) ? md5($_POST['senha']) : "");

        if ($this->model->_save((object) $_POST)) {
            $this->system->alert("As informações foram salvas com sucesso.", ALERT_SUCCESS);
        } else {
            $this->system->alert("Ocorreu uma falha. As infomações não foram salvas.", ALERT_ERROR);
        }

        header("Location: /?c=" . $this->module . "&a=lista");
    }

    public function deleteFromDB() {
        $this->auth();

        if (!empty($_REQUEST['id'])) {
            $id = $_REQUEST['id'];
            if ($this->model->_delete($id)) {
                $this->system->alert("O registro foi excluído.", ALERT_SUCCESS);
            } else {
                $this->system->alert("Ocorreu uma falha inesperada. O registro não pôde ser excluído.", ALERT_ERROR);
            }
        } else {
            $this->system->alert("O sistema não pôde identificar o registro a ser excluído.", ALERT_ERROR);
        }

        header("Location: /?c=" . $this->module . "&a=lista");
    }

    public function login() {

        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $db_user = $this->model->_get(array("email" => $_POST['email']));

            if (!empty($db_user)) {
                if ($db_user->senha === md5($_POST['senha'])) {
                    $_SESSION['user'] = $db_user;
                } else {
                    $this->system->alert("O usuário e senha informados não conferem.", ALERT_ERROR);
                }
            } else {
                $this->system->alert("Não há nenhum usuário registrado com o email informado.", ALERT_ERROR);
            }
        } else {
            $this->system->alert("O email informado não é um endereço válido.", ALERT_ERROR);
        }

        header("Location: /");
    }

    public function logout($msg = null) {

        session_destroy();
        session_start();
        
        if (!empty($msg)) {
            $this->system->alert($msg);
        }
        
        header("Location: /");
        die;
    }
    
    public function auth($permission = null, $logout = true) {
        $denied = false;

        if (isset($_SESSION['user'])) {
            
            $db_user = $this->getFromDB(array("id" => $_SESSION['user']->id));
            
            if (!empty($db_user)) {
                if ($_SESSION['user']->senha != $db_user->senha) {
                    $denied = true;
                } else {
                    // verificação de permissão de usuário.
                }
            } else {
                $denied = true;
            }
        } else {
            $denied = true;
        }

        if ($denied) {
            if ($logout)
                $this->logout("Sua autenticação falhou.");
            return false;
        }

        return true;
    }

}

?>