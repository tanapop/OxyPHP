<?php

session_start();

class System {

    // The name of running controller.
    public $controller;
    // The controller's method name which will be called.
    private $method;
    // The arguments which will be supplied for the method.
    private $args;

    // Include global Controller class and uses data passed on POST, GET or URI to set running controller, action and args.
    public function __construct() {
        require_once "engine/class.controller.php";
        
        $action = explode("/",str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
        array_shift($action);
        
        $this->controller = (isset($_REQUEST["c"]) ? $_REQUEST["c"] : (!empty($action[0]) ? $action[0] : DEFAULT_CONTROLLER));
        $this->method = (isset($_REQUEST["a"]) ? $_REQUEST["a"] : (!empty($action[1]) ? $action[1] : DEFAULT_ACTION));
        $this->args = array_slice($action, 2);
        
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

    public function execute($controller = null, $method = null, $args = null) {
        $controller = (empty($controller) ? $this->controller : $controller);

        if (!empty($args) && !is_array($args)) {
            exit('"args" must be an array of arguments.');
        }

        $className = ucfirst($controller);

        try {
            include_once $_SERVER["DOCUMENT_ROOT"] . "/controllers/" . $controller . ".php";
            return call_user_func_array(array(new $className(), (empty($method) ? $this->method : $method)), (empty($args) ? $this->args : $args));
        } catch (Exception $ex) {
            exit($ex->getMessage());
        }
    }

    /* This static method set a custom alert in SESSION for further usage.
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
