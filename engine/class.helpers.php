<?php

class Helpers extends ObjLoader {

    private $summary;

    public function __construct() {
        $c = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "config.ini", true);

        foreach ($c["HELPERS"] as $k => $v) {
            $temp = explode("?", $v);
            $v = $temp[0];
            $args = explode("&", $temp[1]);
            unset($temp);
            foreach ($args as $i => $val) {
                $args[$i] = substr($val, strpos($val, "="));
            }

            $this->register($k, $v, $args);

            if ($c["SYSTEM"]["HELPERS_AUTOLOAD"]) {
                $this->load($k);
            }
        }
        
        echo '<pre>';
        print_r($this->summary);
        print_r(get_object_vars($this));
        echo '<pre>';
        die;
    }

    public function load($name, $path = null, $args = array()) {
        if(!empty($path) && !array_key_exists($name, $this->summary)){
            $this->register($name, $path, $args);
        }
        
        return $this->$name = self::loadObject($_SERVER["DOCUMENT_ROOT"] . "helpers/" . $this->summary[$name]->path, $name, $this->summary[$name]->args);
    }

    private function register($name, $path, $args = array()) {
        $this->summary[$name] = (object) array(
                    'path' => $path,
                    'args' => $args
        );
    }

}

?>