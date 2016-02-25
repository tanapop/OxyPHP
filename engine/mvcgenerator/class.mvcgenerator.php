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

    public function createmvc($modulename, $fileset) {
        $fields = $this->mysql->query("DESCRIBE " . $modulename);
        foreach ($fields as $row) {
            if ($row->Key == "PRI") {
                $this->primarykey = $row->Field;
                break;
            }
        }

        call_user_func_array(array($this, "create" . $fileset), array($modulename, $fields));
    }

    // Generate a model file, based on a template, adapting it to the module which is being created.
    private function createmodel($modulename, $fields, $return = false) {
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/model");

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
    private function createcontroller($modulename, $fields, $return = false) {
        $breakline = (PATH_SEPARATOR == ":" ? "\r\n" : "\n");
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/controller");

        $f = str_replace("_MODULE_NAME_", $modulename, $f);
        $f = str_replace("_CLASS_NAME_", ucfirst($modulename), $f);

        $file_handler = "";
        foreach ($fields as $field) {
            $tablekey = preg_replace('/\([^)]*\)|[()]/', '', $field->Type);
            if ($this->datatypes[$tablekey] == "file") {
                $file_handler = 'if(!empty($_FILES)){' . $breakline .
                        'foreach($_FILES as $k => $f){' . $breakline .
                        '$dataset[$k] = $_FILES[$k]["type"].";".file_get_contents($_FILES[$k]["tmp_name"]);' . $breakline .
                        '}' . $breakline .
                        '}';
                break;
            }
        }

        $f = str_replace("_SAVE_FILE_HANDLER_", $file_handler, $f);

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

    // Returns a well formated HTML table row string, based on field type.
    private function tableListing($f,$modulename, $header = false) {
        $breakline = (PATH_SEPARATOR == ":" ? "\r\n" : "\n");
        if ($header)
            return "<th>" . ucfirst($f->Field) .($f->Type == "tinyint(1)" ? "?" : ""). "</th>" . $breakline;
        else{
            if($f->Type == "tinyint(1)"){
                $content = '<?php echo (empty($val->' . $f->Field . ') ? "No" : "Yes"); ?>';
            } elseif($this->datatypes[preg_replace('/\([^)]*\)|[()]/', '', $f->Type)] == "file"){
                $content = '<a href="/'.$modulename.'/download/?args[0][field]=' . $f->Field . '&args[0][conditions]['.$this->primarykey.']=<?php echo $val->' . $this->primarykey . '; ?>">Download file</a>';
            }else{
                $content = '<?php echo $val->' . $f->Field . '; ?>';
            }
            return '<td>'.$content.'</td>' . $breakline;
        }
    }

    // Returns a well formated form input string, based on field type.
    private function formField($f) {
        $breakline = (PATH_SEPARATOR == ":" ? "\r\n" : "\n");
        if ($f->Field == $this->primarykey) {
            $input = '<div class="col-md-12">' . $breakline;
            $input .= '<input id="input-' . $f->Field . '" type="hidden" name="' . $f->Field . '" value="<?php echo !empty($dataset) ? $dataset->' . $f->Field . ' : ""; ?>">' . $breakline;
            $input .= '</div>';
        } elseif ($f->Type == "tinyint(1)") {
            $input = '<div class="col-md-12">' . $breakline;
            $input .= '<label>' . ucfirst($f->Field) . '?</label>' . $breakline;
            $input .= '<input class="input-' . $f->Field . '" type="radio" name="' . $f->Field . '" value="0" <?php echo (empty($dataset->' . $f->Field . ') ? "checked" : ""); ?>> No&nbsp;&nbsp;' . $breakline;
            $input .= '<input class="input-' . $f->Field . '" type="radio" name="' . $f->Field . '" value="1" <?php echo (!empty($dataset->' . $f->Field . ') ? "checked" : ""); ?>> Yes' . $breakline;
            $input.= '</div>';
        } elseif ($this->datatypes[preg_replace('/\([^)]*\)|[()]/', '', $f->Type)] == "file") {
            $input = '<div class="col-md-12">' . $breakline;
            $input .= '<input id="input-' . $f->Field . '" type="file" name="' . $f->Field . '" ' . ($f->Null == "NO" && $f->Default == null ? "required " : "") . '>';
            $input.= '</div>';
        } elseif (array_key_exists($key = preg_replace('/\([^)]*\)|[()]/', '', $f->Type), $this->datatypes)) {
            $input = '<div class="col-md-12">' . $breakline;
            $input .= '<input id="input-' . $f->Field . '"'
                    . ' type="' . $this->datatypes[$key] . '"'
                    . ' name="' . $f->Field . '"'
                    . ' value="<?php echo !empty($dataset) ? $dataset->' . $f->Field . ' : ""; ?>"'
                    . ' ' . ($f->Null == "NO" && $f->Default == null ? "required " : "") . ''
                    . ' placeholder="' . $f->Field . '">' . $breakline;
            $input .= '</div>';
            $ftype = $this->datatypes[$key];
        } else {
            $input = '<div class="col-md-12">' . $breakline;
            $input .= '<input id="input-' . $f->Field . '"'
                    . ' type="text"'
                    . ' name="' . $f->Field . '"'
                    . ' value="<?php echo !empty($dataset) ? $dataset->' . $f->Field . ' : ""; ?>"'
                    . ' ' . ($f->Null == "NO" && $f->Default == null ? "required " : "") . ''
                    . ' placeholder="' . $f->Field . '">' . $breakline;
            $input .= '</div>';
            $ftype = "text";
        }

        return '<div class="row">' . $breakline . $input . $breakline . '</div>' . $breakline;
    }

    // Generate "listing" and "register" view files, based on templates, adapting them to the module which is being created.
    private function createviews($modulename, $fields, $return = false) {
        $fl = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/view_listing");
        $fr = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/view_register");

        $fl = str_replace("_MODULE_NAME_", $modulename, $fl);
        $fr = str_replace("_MODULE_NAME_", $modulename, $fr);

        $list_headers = "";
        $list_values = "";
        $form_fields = "";
        foreach ($fields as $f) {
            $list_headers .= $this->tableListing($f,$modulename, true);
            $list_values .= $this->tableListing($f,$modulename, false);
            $form_fields .= $this->formField($f);
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
    private function createall($modulename, $fields) {
        if (!$this->createcontroller($modulename, $fields, true)) {
            System::setAlert("Attempt to create module's controller failed. No file created.", ALERT_FAILURE);
            header('Location: /');
            return false;
        }
        if (!$this->createmodel($modulename, $fields, true)) {
            System::setAlert("Attempt to create module's model failed. But controller file created with success.");
            header('Location: /');
            return false;
        }
        if (!$this->createviews($modulename, $fields, true)) {
            System::setAlert("Attempt to create module's views failed. But controller and model files created with success.");
            header('Location: /');
            return false;
        }

        System::setAlert("Module " . $modulename . " MVC created successfully.", ALERT_SUCCESS);
        header('Location: /');
    }

}
