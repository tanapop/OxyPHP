<?php

session_start();

class System extends ObjLoader{

    // The name of running controller.
    private $controller;
    // The controller's method name which will be called.
    private $method;
    // The arguments which will be supplied for the method.
    private $args;
    // The path to controllers directory
    private $cpath;

    // Include global Controller and Model classs and uses data passed on POST, GET or URI to set running controller, action and args.
    public function __construct() {
        require_once "engine/class.controller.php";
        require_once "engine/class.model.php";

        $action = explode("/", str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
        array_shift($action);

        if ($action[0] == "debug") {
            include $_SERVER['DOCUMENT_ROOT'] . "engine/widgets/debug.php";
            die;
        }

        $this->cpath = $_SERVER["DOCUMENT_ROOT"] . "/controllers/";
        $this->controller = (isset($_REQUEST["controller"]) ? $_REQUEST["controller"] : (!empty($action[0]) ? $action[0] : DEFAULT_CONTROLLER));
        $this->method = (isset($_REQUEST["method"]) ? $_REQUEST["method"] : (!empty($action[1]) ? $action[1] : DEFAULT_METHOD));

        if (SETUP_MODE) {
            $this->setupmode($action);
        }

        $this->setargs($action);
        
    }

    // Change path of controller classes to mvcgenerator, set running controller to mvcgenerator and method to index, if not defined.
    private function setupmode($action) {
        if (empty($action[0]) || $action[0] == 'mvcgenerator') {
            $this->cpath = $_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/class.";

            $this->controller = "mvcgenerator";
            $this->method = (empty($this->method) ? "index" : $this->method);
        }
    }

    // Set the args which will be passed to the called method using data from URI or REQUEST.
    private function setargs($action) {
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

        try {
            return call_user_func_array(array(self::loadClass($this->cpath . $this->controller . ".php", $this->controller, array($this->controller)), (empty($method) ? $this->method : $method)), (empty($args) ? $this->args : $args));
        } catch (Exception $ex) {
            exit($ex->getMessage());
        }
    }

    // This static method set a custom alert in SESSION for further usage.

    public static function setAlert($msg, $type = ALERT_WARNING) {
        $_SESSION['sys_alerts'][] = (object) array(
                    "type" => $type,
                    "msg" => $msg
        );
    }

    // Shows all alerts setted in System::setAlert as dialogs to the user.
    public static function showAlerts() {
        include $_SERVER["DOCUMENT_ROOT"] . "engine/widgets/system_alerts.php";
    }

    /* This static method gather a colletcion of requesting and processing data, set it into SESSION,
     * then navigate to a special URI where these data will be formated and printed on screen for debug purposes.
     */

    public static function debug($messages = array(), $print_data = array()) {
        $session = $_SESSION;
        $_SESSION['debug']['session'] = $session;

        $_SESSION['debug']['request'] = $_REQUEST;

        $_SESSION['debug']['backtrace'] = debug_backtrace();

        $_SESSION['debug']['route'] = str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]);
        $_SESSION['debug']['messages'] = $messages;
        $_SESSION['debug']['print_data'] = $print_data;

        echo "<script>window.open('/debug');</script>";
        die;
    }

    public static function loadClass($ab_path, $classname, $args = array()) {
        return self::load($ab_path, $classname, $args);
    }

}

?>
