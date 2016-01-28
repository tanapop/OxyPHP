<?php

class Home extends Controller {

    private $module;

    public function __construct() {
        $this->module = "home";
    }
    
    public function index(){
        global $system;
        
        self::view("common", "top");
        if($system->auth(null, false)){
            self::view("home", "index");
        } else{
            self::view("home", "login");
        }
        self::view("common", "footer");
    }

}

?>