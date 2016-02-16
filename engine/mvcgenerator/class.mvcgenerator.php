<?php

class Mvcgenerator {

    private $mysql;
    private $tables;
    private $datatypes;

    public function __construct() {
        require_once $_SERVER['DOCUMENT_ROOT'] . "engine/databaseclasses/class.mysql.php";

        $this->mysql = new Mysql();

        $tables = $this->mysql->query("SHOW TABLES");

        $keyname = "Tables_in_" . MYSQL_DBNAME;
        foreach ($tables as $t) {
            $this->tables[] = $t->$keyname;
        }

        $this->datatypes = array(
            "int(11)" => "number",
            "varchar(255)" => "text"
        );
    }

    public function index() {
        $path = $_SERVER['DOCUMENT_ROOT'] . "engine/mvcgenerator/index.php";

        extract(array("tables" => $this->tables));

        ob_start();
        include $path;

        $contents = ob_get_clean();

        echo $contents;
    }

    public function createmodel($modulename, $return = false) {
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/model.php");

        $f = str_replace("_CLASS_NAME_", "Model" . ucfirst($modulename), $f);

        $path = $_SERVER["DOCUMENT_ROOT"] . "models/";
        chmod($path, 0777);
        if (file_put_contents($path . $modulename . ".php", $f)) {
            chmod($path . $modulename . ".php", 0755);
            chmod($path, 0755);
            if ($return)
                return true;
            System::setAlert($modulename . " model created successfully.", ALERT_SUCCESS);
            header('Location: /');
        } else {
            if ($return)
                return false;
            System::setAlert("Attempt to create " . $modulename . " model failed.", ALERT_FAILURE);
            header('Location: /');
        }
    }

    public function createcontroller($modulename, $return = false) {
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/controller.php");

        echo $f;
        $f = str_replace("_CLASS_NAME_", ucfirst($modulename), $f);
        echo "<hr>";
        echo $f;
        die;

        $path = $_SERVER["DOCUMENT_ROOT"] . "controllers/";
            chmod($path, 0777);
        if (file_put_contents($path . $modulename . ".php", $f)) {
            chmod($path . $modulename . ".php", 0755);
            chmod($path, 0755);
            if ($return)
                return true;
            System::setAlert($modulename . " controller created successfully.", ALERT_SUCCESS);
            header('Location: /');
        } else {
            if ($return)
                return false;
            System::setAlert("Attempt to create " . $modulename . " controller failed.", ALERT_FAILURE);
            header('Location: /');
        }
    }

    public function createviews($modulename, $return = false) {

        $fl = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/view_listing.php");
        $fr = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/view_register.php");

        $fl = str_replace("_MODULE_NAME_", $modulename, $fl);
        $fr = str_replace("_MODULE_NAME_", $modulename, $fr);

        $fields = $this->mysql->query("DESCRIBE " . $modulename);

        $list_headers = "";
        $list_values = "";
        $form_fields = "";
        foreach ($fields as $f) {
            $list_headers .= "<th>" . $f->Field . "</th>" . (PATH_SEPARATOR == ":" ? "\r\n" : "\n");
            $list_values .= '<td><?php echo $val->' . $f->Field . '; ?></td>' . (PATH_SEPARATOR == ":" ? "\r\n" : "\n");

            $form_fields .= '<div class="row"><div class="col-md-12"><input ' . ($f->Null == "NO" && $f->Field != "id" ? "required" : "") . ' type="' . ($f->Field == "id" ? "hidden" : $this->datatypes[$f->Type]) . '" name="' . $f->Field . '" placeholder="' . $f->Field . '" value="<?php echo $dataset->' . $f->Field . '; ?>"></div></div>';
        }

        $fl = str_replace("_TABLE_FIELDS_HEADERS_", $list_headers, $fl);
        $fl = str_replace("_TABLE_FIELDS_VALUES_", $list_values, $fl);

        $fr = str_replace("_REGISTER_FORM_FIELDS_", $form_fields, $fr);

        $viewpath = $_SERVER['DOCUMENT_ROOT'] . "views/" . $modulename . "/";

        mkdir($viewpath, 0777, true);
        touch($viewpath);
        chmod($viewpath, 0777);

        if (file_put_contents($viewpath . "listing.php", $fl) && file_put_contents($viewpath . "register.php", $fl)) {
            chmod($viewpath . "listing.php", 0755);
            chmod($viewpath . "register.php", 0755);
            if ($return)
                return true;
            System::setAlert($modulename . " views created successfully.", ALERT_SUCCESS);
            header('Location: /');
        } else {
            if ($return)
                return false;
            System::setAlert("Attempt to create " . $modulename . " views failed.", ALERT_FAILURE);
            header('Location: /');
        }
    }

    public function createall($modulename) {
        if (!$this->createcontroller($modulename, true)) {
            System::setAlert("Attempt to create module's controller failed. No file created.", ALERT_FAILURE);
            header('Location: /');
            return false;
        }
        if (!$this->createmodel($modulename, true)) {
            System::setAlert("Attempt to create module's model failed. But controller file created with success.");
            header('Location: /');
            return false;
        }
        if (!$this->createviews($modulename, true)) {
            System::setAlert("Attempt to create module's views failed. But controller and model files created with success.");
            header('Location: /');
            return false;
        }

        System::setAlert("Module " . $modulename . " MVC created successfully.", ALERT_SUCCESS);
        header('Location: /mvcgenerator/');
    }

}
