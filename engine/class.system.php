<?php

session_start();

class System {

    // The name of running controller.
    private $controller;
    
    // The controller's method name which will be called.
    private $action;
    
    // The arguments which will be supplied for the method.
    private $args;

    // Include global Controller class and uses data passed on REQUEST or URL to set running controller, action and args.
    public function __construct() {
        require_once "engine/class.controller.php";
        $this->controller = (isset($_REQUEST["c"]) ? $_REQUEST["c"] : DEFAULT_CONTROLLER);
        $this->action = (isset($_REQUEST["a"]) ? $_REQUEST["a"] : DEFAULT_ACTION);
        $this->args = array();

        if (isset($_REQUEST["args"])) {
            if (!is_array($_REQUEST["args"])) {
                exit('"args" must be an array of arguments.');
            }
            $this->args = $_REQUEST["args"];
        }
    }

    /* Create an instance of a custom controller and calls it's method, passing specified arguments. 
     * If no controller, action or args is supplied, it uses the ones setted in __construct method, above.
     */
    public function execute($c = null, $a = null, $args = null) {
        $controller = (empty($c) ? $this->controller : $c);
        $action = (empty($a) ? $this->action : $a);
        $_args = array();

        if (!empty($args)) {
            if (!is_array($args)) {
                exit('"args" must be an array of arguments.');
            }
            $_args = $args;
        } else{
            $_args = $this->args;
        }

        $className = ucfirst($controller);

        try {
            include_once $_SERVER["DOCUMENT_ROOT"] . "/controllers/" . $controller . ".php";
            $c_obj = new $className();
            return call_user_func_array(array($c_obj, $action), $_args);
        } catch (Exception $ex) {
            exit($ex->getMessage());
        }
    }

    /* This static method set a custom alert data in SESSION for further usage.
     * By default, it is shown to user(See index.php).
     */
    public static function setAlert($msg, $type = ALERT_WARNING) {
        $_SESSION['sys_alerts'][] = (object) array(
                    "type" => $type,
                    "msg" => $msg
        );
    }

}

?>
