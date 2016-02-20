<?php

class Mvcgenerator {

    // An instance of Mysql class
    private $mysql;
    // A tables list from database
    private $tables;
    // A dictionary to translate database datatypes to form input's type.
    private $datatypes;

    // Include Mysql class file and instantiate it on this->mysql, write database tables list and set the datatypes dictionary.
    public function __construct() {
        require_once $_SERVER['DOCUMENT_ROOT'] . "engine/databaseclasses/class.mysql.php";

        if (MYSQL_DATABASE_ON)
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

    // Include mvcgenerator index page and show its contents, passing the tables list to it.
    public function index() {
        $path = $_SERVER['DOCUMENT_ROOT'] . "engine/mvcgenerator/index.php";

        extract(array("tables" => $this->tables));

        ob_start();
        include $path;

        $contents = ob_get_clean();

        echo $contents;
        System::showAlerts();
    }

    // Generate a model file, based on a template, adapting it to the module which is being created.
    public function createmodel($modulename, $return = false) {
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/model.php");

        $f = str_replace("_CLASS_NAME_", "Model" . ucfirst($modulename), $f);

        $path = $_SERVER["DOCUMENT_ROOT"] . "models/";
        if (file_put_contents($path . $modulename . ".php", $f)) {
            touch($path . $modulename . ".php");
            chmod($path . $modulename . ".php", 0777);
            if ($return)
                return true;
            System::setAlert('"' . ucfirst($modulename) . '" module\'s model created successfully.', ALERT_SUCCESS);
            header('Location: /');
        } else {
            if ($return)
                return false;
            System::setAlert('Attempt to create "' . ucfirst($modulename) . '" module\'s model failed.', ALERT_FAILURE);
            header('Location: /');
        }
    }

    // Generate a controller file, based on a template, adapting it to the module which is being created.
    public function createcontroller($modulename, $return = false) {
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/controller.php");

        $f = str_replace("_MODULE_NAME_", $modulename, $f);
        $f = str_replace("_CLASS_NAME_", ucfirst($modulename), $f);

        $path = $_SERVER["DOCUMENT_ROOT"] . "controllers/";
        if (file_put_contents($path . $modulename . ".php", $f)) {
            touch($path . $modulename . ".php");
            chmod($path . $modulename . ".php", 0777);
            if ($return)
                return true;
            System::setAlert('"' . ucfirst($modulename) . '" module\'s controller created successfully.', ALERT_SUCCESS);
            header('Location: /');
        } else {
            if ($return)
                return false;
            System::setAlert('Attempt to create "' . ucfirst($modulename) . '" module\'s controller failed.', ALERT_FAILURE);
            header('Location: /');
        }
    }

    // Generate "listing" and "register" view files, based on templates, adapting them to the module which is being created.
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

        $fl = str_replace("_COUNT_COLUMNS_", count($fields) + 2, $fl);
        $fl = str_replace("_TABLE_FIELDS_HEADERS_", $list_headers, $fl);
        $fl = str_replace("_TABLE_FIELDS_VALUES_", $list_values, $fl);

        $fr = str_replace("_REGISTER_FORM_FIELDS_", $form_fields, $fr);

        $viewpath = $_SERVER['DOCUMENT_ROOT'] . "views/" . $modulename . "/";

        if (!file_exists($viewpath))
            mkdir($viewpath, 0777, true);
        touch($viewpath);
        chmod($viewpath, 0777);

        if (file_put_contents($viewpath . "listing.php", $fl) && file_put_contents($viewpath . "register.php", $fr)) {
            touch($viewpath . "listing.php");
            chmod($viewpath . "listing.php", 0777);
            touch($viewpath . "register.php");
            chmod($viewpath . "register.php", 0777);
            if ($return)
                return true;
            System::setAlert('"' . ucfirst($modulename) . '" module\'s views created successfully.', ALERT_SUCCESS);
            header('Location: /');
        } else {
            if ($return)
                return false;
            System::setAlert('Attempt to create "' . ucfirst($modulename) . '" module\'s views failed.', ALERT_FAILURE);
            header('Location: /');
        }
    }

    // Generate a model, a controller and the 2 view files, calling the other creation methods within this class.
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
        header('Location: /');
    }

}
