<?php

session_start();

class Main {

    private $controller;
    private $action;
    private $args;

    public function __construct() {
        require_once "engine/class.controller.php";
        $this->controller = (isset($_REQUEST["c"]) ? $_REQUEST["c"] : DEFAULT_CONTROLLER);
        $this->action = (isset($_REQUEST["a"]) ? $_REQUEST["a"] : DEFAULT_ACTION);
        $this->args = (isset($_REQUEST["args"]) ? $_REQUEST["args"] : null);
    }

    public function execute($c = null, $a = null, $args = null) {
        $controller = (empty($c) ? $this->controller : $c);
        $action = (empty($a) ? $this->action : $a);
        $_args = (empty($args) ? $this->args : $args);

        $className = ucfirst($controller);

        try {
            include_once $_SERVER["DOCUMENT_ROOT"] . "/controllers/" . $controller . ".php";
            $c_obj = new $className();
            return $c_obj->$action($_args);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
        }
    }

    public function alert($msg, $type = ALERT_WARNING) {
        $_SESSION['sys_alerts'][] = (object) array(
                    "type" => $type,
                    "msg" => $msg
        );
    }

    public function auth($permission = null, $logout = true) {
        $denied = false;

        if (isset($_SESSION['user'])) {
            
            $db_user = $this->execute("usuarios","_get",array("id" => $_SESSION['user']->id));
            
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
                $this->execute("usuarios", "logout", "Sua autenticação falhou.");
            return false;
        }

        return true;
    }

}

?>
