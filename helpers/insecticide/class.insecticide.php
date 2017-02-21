<?php

class Insecticide {

    private $uri_path;
    private $theme;

    public function __construct($uri_path, $theme = 'default') {

        $this->uri_path = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off" ? "https" : "http")."://".$_SERVER["SERVER_NAME"].$uri_path."/insecticide/";
        $this->theme = $theme;
    }

    public function debug($messages = array(), $print_data = array()) {
        echo '<script src="http://code.jquery.com/jquery-latest.min.js"></script>';
        echo '<script src="' . $this->uri_path . 'js/insecticide.js"></script>';
        echo '<link rel="stylesheet" href="' . $this->uri_path . 'style/insecticide.css">';
        echo '<link rel="stylesheet" href="' . $this->uri_path . 'style/themes/' . $this->theme . '.css">';
        
        $request = $_REQUEST;
        $backtrace = debug_backtrace();
        $route = str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]);
        $time = date("Y/m/d - H:i:s", time());

        ob_start();
        include __DIR__ . '/debug.php';
        echo ob_get_clean();

        die;
    }

    public static function dump($var, $name = "", $return = false) {
        $vartype = gettype($var);
        $output = '<div class="ins-container">';
        $output .= '<ul class="ins-dump">';
        if (!is_array($var) && $vartype !== 'object') {
            $output .= '<li><b>&#8627;</b> <span class="ins-varname">[' . $name . ']</span> (' . $vartype . ' - length:' . strlen((string) $var) . ') = <b>' . $var . '</b></li>';
        } else {
            $length = count((array) $var);
            $output .= '<li class="ins-dropdown"><b>&#8600;</b> <span class="ins-varname">[' . $name . ']</span> (' . $vartype . ' - length:' . $length . ')</li><li class="ins-hidden"><ul>';
            $output .= $vartype == "object" ? "<li class='ins-obs'>*This is an object. It may contain inaccessible(private or protected) properties that will not be shown in this dump list.</li>" : "";
            $output .= empty($length) ? "<li class='ins-obs'>*This list is empty. No items to dump.</li>" : "";
            foreach ($var as $k => $v) {
                $output .= '<li>';
                $output .= self::dump($v, $k, true);
                $output .= '</li>';
            }
            $output .= '</ul></li>';
        }
        $output .= '</ul></div>';

        if ($return)
            return $output;
        else
            echo $output;
    }

}

?>