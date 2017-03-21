<?php

class System {

    // The name of running controller.
    private $controller;
    // The controller's method name which will be called.
    private $method;
    // The arguments which will be supplied for the method.
    private $args;
    // The path to controllers directory
    private $cpath;
    // Helpers class object
    private $helpers;

    // Include some global core classes and uses data passed on POST, GET or URI to set running controller, action and args.
    public function __construct() {
        $this->helpers = self::loadClass(__DIR__ . "/class.helpers.php", "helpers");
        
        foreach (parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "config.ini", true) as $key => $val) {
            if ($key !== "HELPERS") {
                foreach ($val as $k => $v) {
                    define(strtoupper($k), $v);
                }
            }
        }

        require_once "engine/class.controller.php";
        require_once "engine/class.model.php";
        self::loadClass($_SERVER['DOCUMENT_ROOT'] . 'engine/class.errorhandler.php', 'errorhandler');

        $action = explode("/", str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", urldecode($_SERVER["REQUEST_URI"])));
        array_shift($action);

        if ($action[0] == "_asyncload") {
            array_shift($action);
        }

        $this->cpath = $_SERVER["DOCUMENT_ROOT"] . "controllers/";
        $this->controller = (isset($_REQUEST["controller"]) ? $_REQUEST["controller"] : (!empty($action[0]) ? $action[0] : DEFAULT_CONTROLLER));
        $this->method = (isset($_REQUEST["method"]) ? $_REQUEST["method"] : (!empty($action[1]) ? $action[1] : DEFAULT_METHOD));

        if (SETUP_MODE) {
            $this->setupmode($action);
        }

        $this->setargs($action);

        $this->execute();
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
                self::log("sys_error", 'Error message: Argument passed to "System::setargs" must be an array.');
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

        try {
            $c_obj = self::loadClass($this->cpath . $this->controller . ".php", $this->controller, array($this->controller, $this->method));
            return call_user_func_array(array($c_obj, (empty($method) ? $this->method : $method)), (empty($args) ? $this->args : $args));
        } catch (Exception $ex) {
            self::log("sys_error", "From System->execute() - " . $ex->getMessage());
            $this->helpers->insecticide->debug(array("Attempt to execute URL failed.",'The system returned with the following message: "At 2017/03/21 - 18:39:19 - Warning: include_once(/var/www/html/oxyphp/controllers/bar.php): failed to open stream: No such file or directory. The exception occurred in file /home/gabriel/Projects/oxyphp/engine/class.objloader.php on line 18".'));
        }
    }

    public static function loadClass($ab_path, $classname, $args = array()) {
        return ObjLoader::load($ab_path, $classname, $args);
    }

    public static function log($logname, $logmsg) {
        $path = $_SERVER["DOCUMENT_ROOT"] . "log/";
        if (!file_exists($path))
            mkdir($path, 0777, true);
        touch($path);
        chmod($path, 0777);

        $log = fopen($path . $logname . '.log', 'a');
        fwrite($log, $logmsg . str_repeat((PATH_SEPARATOR == ":" ? "\r\n" : "\n"), 2));
        fclose($log);
    }

}

?>
