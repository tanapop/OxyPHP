<?php

class Home extends Controller {
    
    public function index($arg, $arg2){
        echo $arg;
        echo '<br>';
        echo $arg2;
//        $this->view("top", null, "common");
//        if($this->system->execute("users", "auth", array(null, false))){
//            $this->view("home", "index");
//        } else{
//            $this->view("home", "login");
//        }
//        $this->view("footer", null, "common");
    }

}

?>