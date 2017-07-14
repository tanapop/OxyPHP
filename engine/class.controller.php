<?php

class Controller {

    // The running global instance of system class
    protected $system;
    // The global object of Helpers class
    protected $helpers;
    // Current module. It's the same name of the running controller.
    private $module;
    // Current method.
    private $method;
    // An instance of the module's model, if it exists.
    protected $model;

    // Set the global system property, set the module and create module's model instance.
    public function __construct($module, $method) {
        global $system;

        $this->helpers = System::loadClass($_SERVER['DOCUMENT_ROOT'] . "/engine/class.helpers.php", "helpers");
        $this->system = &$system;

        $this->module = $module;
        $this->method = $method;

        if (is_file($_SERVER['DOCUMENT_ROOT'] . "/application/models/" . $this->module . ".php"))
            $this->_loadmodel($this->module);
    }

    // Show or return the contents of a view file, passing specified variables for this file, if they're supplied.
    protected function _view($file, $varlist = array(), $module = null, $return = false) {
        if (!empty($varlist)) {
            try {
                extract($varlist);
            } catch (Exception $ex) {
                System::log("sys_error", "Error message: " . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
            }
        }

        ob_start();
        try {
            include $_SERVER['DOCUMENT_ROOT'] . "/application/views/" . (empty($module) ? $this->module : $module) . "/" . $file . ".php";
        } catch (Exception $ex) {
            System::log("sys_error", "Error message: " . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }

        if ($return === true)
            return ob_get_clean();
        else
            echo ob_get_clean();
    }

    protected function _route($module_alias, $method_alias = null, $data = array()) {
        $ret = array(
            ucfirst(DEFAULT_CONTROLLER) => '/',
            $module_alias => '/' . $this->module
        );

        if (!empty($method_alias)) {
            $ret[$method_alias] = '/' . $this->module . '/' . $this->method;
            foreach ($data as $d) {
                $ret[$method_alias] .= '/' . $d;
            }
        }

        return $ret;
    }

    protected function module() {
        return $this->module;
    }

    protected function _loadmodel($modelname) {
        $this->model = System::loadClass($_SERVER['DOCUMENT_ROOT'] . "/application/models/" . $modelname . ".php", "Model" . ucfirst($modelname), array($modelname));
    }

}

?>
