<?php

session_start();

class System {

    // The name of running controller.
    private $controller;
    // The controller's method name which will be called.
    private $method;
    // The arguments which will be supplied for the method.
    private $args;

    // Include global Controller class and uses data passed on POST, GET or URI to set running controller, action and args.
    public function __construct() {
        require_once "engine/class.controller.php";
        require_once "engine/class.model.php";

        $action = explode("/", str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
        array_shift($action);

        $this->controller = (isset($_REQUEST["controller"]) ? $_REQUEST["controller"] : (!empty($action[0]) ? $action[0] : DEFAULT_CONTROLLER));
        $this->method = (isset($_REQUEST["method"]) ? $_REQUEST["method"] : (!empty($action[1]) ? $action[1] : DEFAULT_METHOD));
        $this->args = array_slice($action, 2);

        if (isset($_REQUEST["args"])) {
            if (!is_array($_REQUEST["args"])) {
                self::debug(array('class.system: Argument Error: args must be an array.'), array('args' => $_REQUEST["args"]));
            }
            $this->args = $_REQUEST["args"];
        } elseif (!empty($_POST)) {
            if (isset($_POST['controller']))
                unset($_POST['controller']);
            if (isset($_POST['method']))
                unset($_POST['method']);

            $this->args = array($_POST);
        } elseif (!empty($_GET)) {
            if (isset($_GET['controller']))
                unset($_GET['controller']);
            if (isset($_GET['method']))
                unset($_GET['method']);

            $this->args = array($_GET);
        }
    }

    /* Create an instance of a custom controller and calls it's method, passing specified arguments. 
     * If no controller, action or args is supplied, it uses the ones setted in __construct method, above.
     */

    public function execute($controller = null, $method = null, $args = null) {
        $this->controller = (empty($controller) ? $this->controller : $controller);

        if (!empty($args) && !is_array($args)) {
            self::debug(array('class.system: Argument Error: args must be an array.'), array('args' => $args));
        }

        $className = ucfirst($this->controller);

        try {
            include_once $_SERVER["DOCUMENT_ROOT"] . "/controllers/" . $this->controller . ".php";
            return call_user_func_array(array(new $className($this->controller), (empty($method) ? $this->method : $method)), (empty($args) ? $this->args : $args));
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

    /* This static method gather a colletcion of requesting and processing data, set it into SESSION,
     * then navigate to a special URI where these data will be formated and printed on screen for debug purposes.
     */

    public static function debug($messages = array(), $toPrint = array()) {
        
    }

}

?>
