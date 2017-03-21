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
        
        $this->helpers = System::loadClass($_SERVER['DOCUMENT_ROOT'] . "engine/class.helpers.php", "helpers");
        $this->system = &$system;

        $this->module = $module;
        $this->method = $method;

        if (is_file($_SERVER['DOCUMENT_ROOT'] . "/models/" . $this->module . ".php"))
            $this->_loadmodel($this->module);
    }

    // Show or return the contents of a view file, passing specified variables for this file, if they're supplied.
    protected function _view($file, $varlist = array(), $module = null, $return = false) {
        if (!empty($varlist)) {
            try {
                extract($varlist);
            } catch (Exception $ex) {
                System::log("sys_error","Error message: " . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
            }
        }

        ob_start();
        try {
            include $_SERVER['DOCUMENT_ROOT'] . "views/" . (empty($module) ? $this->module : $module) . "/" . $file . ".php";
        } catch (Exception $ex) {
            System::log("sys_error","Error message: " . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
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
                $ret[$method_alias] .= '/'.$d;
            }
        }

        return $ret;
    }
    
    protected function module(){
        return $this->module;
    }

    protected function _downloadfile($args, $filename) {
        try {
            if (is_string($args)) {
                header('Content-Type: ' . mime_content_type($args) . ';');
                header('Content-Disposition: attachment; filename=' . end(explode("/", $args)));
                header('Pragma: no-cache');
                readfile($args);
            } elseif (is_array($args)) {
                foreach ($this->model->_get($args['field'], $args['conditions'])[0] as $value) {
                    $filedata = explode(";", $value, 2);
                    break;
                }
                header('Content-Type: ' . $filedata[0] . '; charset=' . mb_detect_encoding($filedata[1]));
                header('Content-Disposition: attachment; filename="' . $filename . "." . explode("/", $filedata[0], 2)[1] . '"');
                header("Cache-Control: no-cache");
                ob_clean();
                echo $filedata[1];
            } else {
                trigger_error("Wrong argument type. It must be a string or an array.", E_WARNING);
            }
        } catch (Exception $e) {
            System::log("sys_error","Error message: " . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }
        exit;
    }

    protected function _loadmodel($modelname) {
        $this->model = System::loadClass($_SERVER['DOCUMENT_ROOT'] . "/models/" . $modelname . ".php", "Model" . ucfirst($modelname), array($modelname));
    }

}

?>
