<?php

class Helpers {
    /* $summary is an index of helpers specified in config.ini file. 
     * It holds data like helper's class name, path and arguments to be passed to helper's construct method.
     */

    private $summary;

    public function __construct() {
        $c = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "config.ini", true);

        foreach ($c["HELPERS"] as $k => $v) {
            $k = strtolower($k);
            $temp = explode("?", $v);
            $v = $temp[0];
            $args = array();
            if (isset($temp[1]))
                $args = explode("&", $temp[1]);
            unset($temp);
            foreach ($args as $i => $val) {
                $args[$i] = trim(substr($val, strpos($val, "=")), "=");
            }

            $this->register($k, $v, $args);

            if ($c["SYSTEM"]["HELPERS_AUTOLOAD"]) {
                $this->load($k);
            }
        }
    }

    public function load($name, $path = null, $args = array()) {
        $name = strtolower($name);
        if (!empty($path) && !array_key_exists($name, $this->summary)) {
            $this->register($name, $path, $args);
        }

        return $this->$name = ObjLoader::load($_SERVER["DOCUMENT_ROOT"] . "helpers/" . $this->summary[$name]->path, $name, $this->summary[$name]->args);
    }

    private function register($name, $path, $args = array()) {
        $this->summary[$name] = (object) array(
                    'path' => $path,
                    'args' => $args
        );
    }

}

?>