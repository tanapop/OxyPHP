<?php

class Controller {

    public static function view($dir, $file, $args = null, $return = false) {
        $path = $_SERVER['DOCUMENT_ROOT'] . "/views/" . $dir . "/" . $file . ".php";
        
        if (!empty($args))
            extract($args);
        
        ob_start();
        if(file_exists($path)){
            include $path;
        }
        
        $contents = ob_get_clean();
        
        if($return === true)
            return $contents;
        else echo $contents;
    }

}

?>
