<?php

class Home extends Controller {
    
    public function index(){
        global $system;
        
        $this->view("common", "top");
        if($this->system->execute("users", "auth", array(null, false))){
            $this->view("home", "index");
        } else{
            $this->view("home", "login");
        }
        $this->view("common", "footer");
    }

}

?>