<?php

class Insecticide {

    private $uri_path;
    private $theme;

    public function __construct($uri_path, $theme = 'default') {

        $this->uri_path = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off" ? "https" : "http")."://".$_SERVER["SERVER_NAME"].$uri_path."/insecticide/";
        $this->theme = $theme;
    }

    public function debug($messages = array(), $print_data = array()) {
        
        $request = $_REQUEST;
        $backtrace = debug_backtrace();
        $route = str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]);
        $time = date("Y/m/d - H:i:s", time());

        $uri_path = $this->uri_path;
        $theme = $this->theme;
        
        ob_start();
        include_once __DIR__ . '/view.debug.php';
        include_once __DIR__ . '/view.includes.php';
        echo ob_get_clean();

        die;
    }

    public function dump($var, $name = "", $return = false) {
        $uri_path = $this->uri_path;
        $theme = $this->theme;
        
        $vartype = gettype($var);
            
        ob_start();
        include __DIR__.'/view.dump.php';
        include_once __DIR__ . '/view.includes.php';
        $output = ob_get_clean();
        
        if ($return)
            return $output;
        else
            echo $output;
    }

}

?>