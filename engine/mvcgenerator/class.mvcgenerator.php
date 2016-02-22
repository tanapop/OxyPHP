<?php

class Mvcgenerator {

    // An instance of Mysql class
    private $mysql;
    // A tables list from database
    private $tables;
    // A dictionary to translate database datatypes to form input's type.
    private $datatypes;
    // The current table's primary key name.
    private $primarykey;

    // Include Mysql class file and instantiate it on this->mysql, write database tables list and set the datatypes dictionary.
    public function __construct() {
        if (MYSQL_DATABASE_ON)
            $this->mysql = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databaseclasses/class.mysql.php", "Mysql");

        $tables = $this->mysql->query("SHOW TABLES");

        $keyname = "Tables_in_" . MYSQL_DBNAME;
        foreach ($tables as $t) {
            $this->tables[] = $t->$keyname;
        }

        $this->datatypes = array(
            "char" => "text",
            "varchar" => "text",
            "text" => "text",
            "tinytext" => "text",
            "mediumtext" => "text",
            "longtext" => "text",
            "binary" => "file",
            "varbinary" => "file",
            "tinyblob" => "file",
            "mediumblob" => "file",
            "blob" => "file",
            "longblob" => "file",
            "date" => "text",
            "datetime" => "text",
            "timestamp" => "text",
            "time" => "text",
            "year" => "text",
            "bit" => "number",
            "int" => "number",
            "tinyint" => "number",
            "mediumint" => "number",
            "bigint" => "number",
            "decimal" => "number",
            "float" => "number",
            "double" => "number"
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
        $breakline = (PATH_SEPARATOR == ":" ? "\r\n" : "\n");
        $fl = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/view_listing.php");
        $fr = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/view_register.php");

        $fl = str_replace("_MODULE_NAME_", $modulename, $fl);
        $fr = str_replace("_MODULE_NAME_", $modulename, $fr);

        $fields = $this->mysql->query("DESCRIBE " . $modulename);
        foreach ($fields as $row) {
            if ($row->Key == "PRI") {
                $this->primarykey = $row->Field;
                break;
            }
        }

        $list_headers = "";
        $list_values = "";
        $form_fields = "";
        foreach ($fields as $f) {
            $list_headers .= "<th>" . $f->Field . "</th>" . $breakline;
            $list_values .= '<td><?php echo $val->' . $f->Field . '; ?></td>' . $breakline;

            if ($f->Field == $this->primarykey) {
                $ftype = "hidden";
            } elseif ($f->Type == "tinyint(1)") {
                $ftype = "checkbox";
            } elseif (array_key_exists($key = preg_replace('/\([^)]*\)|[()]/', '', $f->Type), $this->datatypes)) {
                $ftype = $this->datatypes[$key];
            } else {
                $ftype = "text";
            }

            $form_fields .= '<div class="row">' .
                    $breakline .
                    '<div class="col-md-12">' .
                    $breakline . '<input id="input-' .
                    $f->Field . '" ' . ($f->Null == "NO" && $f->Field != $this->primarykey ? "required" : "") .
                    ' type="' . $ftype . '" name="' . $f->Field . '" ' . ($ftype == "hidden" || $ftype == "checkbox" || $ftype == "file" ? "" : 'placeholder="' . $f->Field . '" ' ) .
                    ($ftype == "file" ? "" : 'value="<?php echo !empty($dataset) ? $dataset->' . $f->Field . ' : ""; ?>"') . '>' .
                    ($ftype == "checkbox" ? ' <label for="input-' . $f->Field . '">' . $f->Field . "</label>" : "") .
                    $breakline . '</div>' .
                    $breakline . '</div>' . $breakline;
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
