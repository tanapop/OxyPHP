<?php

class Controller {

    // The running global instance of system class
    protected $system;
    // Current module. It's the same name of the running controller.
    private $module;
    // An instance of the module's model, if it exists.
    protected $model;

    // Set the global system property, set the module and create module's model instance.
    public function __construct($module) {
        global $system;
        $this->system = $system;

        $this->module = $module;

        $modelPath = $_SERVER['DOCUMENT_ROOT'] . "/models/" . $this->module . ".php";
        if (file_exists($modelPath)) {
            require_once $modelPath;
            $classname = "Model" . ucfirst($this->module);
            $this->model = new $classname($this->module);
        }
    }

    // Show or return the contents of a view file, passing specified variables for this file, if they're supplied.
    protected function view($file, $varlist = null, $module = null, $return = false) {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/views/" . (empty($module) ? $this->module : $module) . "/" . $file . ".php";

        if (!empty($varlist)) {
            if (!is_array($varlist))
                System::debug(array("class.controller: Argument Error: varlist must be an array."), array('$varlist' => $varlist));

            extract($varlist);
        }

        ob_start();
        if (file_exists($path)) {
            include $path;
        }

        $contents = ob_get_clean();

        if ($return === true)
            return $contents;
        else
            echo $contents;
    }

}

?>
