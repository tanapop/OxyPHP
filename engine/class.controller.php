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
        $this->system = &$system;

        $this->module = $module;

        $this->model = System::loadClass($_SERVER['DOCUMENT_ROOT'] . "/models/" . $this->module . ".php", "Model" . ucfirst($this->module), array($this->module));
    }

    // Show or return the contents of a view file, passing specified variables for this file, if they're supplied.
    protected function _view($file, $varlist = null, $module = null, $return = false) {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/views/" . (empty($module) ? $this->module : $module) . "/" . $file . ".php";

        if (!empty($varlist)) {
            try {
                extract($varlist);
            } catch (Exception $e) {
                System::debug(array("Error message" => $e->message));
            }
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

    protected function _downloadfile($args, $filename) {
        try {
            if (is_string($args)) {
                header('Content-Type: ' . mime_content_type($args).';');
                header('Content-Disposition: attachment; filename=' . end(explode("/", $args)));
                header('Pragma: no-cache');
                readfile($args);
            } elseif (is_array($args)) {
                $db = $this->model->_get($args['field'], $args['conditions'])[0];
                foreach ($db as $value) {
                    $filedata = explode(";", $value, 2);
                    break;
                }
                header('Content-Type: ' . $filedata[0].'; charset='.mb_detect_encoding($filedata[1]));
                header('Content-Disposition: attachment; filename="' . $filename . "." . explode("/", $filedata[0], 2)[1].'"');
                header("Cache-Control: no-cache");
                ob_clean();
                echo $filedata[1];
                exit;
            } else{
                throw new Exception("Wrong argument type. It must be a string or an array.",1);
            }
        } catch (Exception $e) {
            System::debug(array("Error message" => $e->message));
        }
    }

}

?>
