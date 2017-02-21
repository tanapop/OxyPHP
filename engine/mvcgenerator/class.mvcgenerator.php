<?php

class Mvcgenerator {

    // An instance of Mysql class
    private $dbclass;
    // A tables list from database
    private $tables;
    // A dictionary to translate database datatypes to form input's type.
    private $datatypes;
    // The current table's primary key name.
    private $primarykey;
    // The global object of Helpers class
    private $helpers;

    // Include Mysql class file and instantiate it on this->mysql, write database tables list and set the datatypes dictionary.
    public function __construct() {
        global $helpers;
        $this->helpers = &$helpers;
        
        $this->dbclass = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.dbclass.php", 'dbclass');

        $this->tables = $this->dbclass->dbtables();

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
        extract(array("tables" => $this->tables));

        ob_start();
        include $_SERVER['DOCUMENT_ROOT'] . "engine/mvcgenerator/index.php";

        echo ob_get_clean();
        $this->helpers->phpalert->show();
    }

    public function createmvc($modulename, $fileset) {
        $fields = $this->dbclass->describeTable($modulename);
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
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/model.mtx");

        $f = str_replace("_CLASS_NAME_", "Model" . ucfirst($modulename), $f);

        $path = $_SERVER["DOCUMENT_ROOT"] . "models/";
        if (!file_exists($path))
            mkdir($path, 0777, true);
        touch($path);
        chmod($path, 0777);
        if (file_put_contents($path . $modulename . ".php", $f)) {
            touch($path . $modulename . ".php");
            chmod($path . $modulename . ".php", 0777);
            if ($return)
                return true;
            $this->helpers->phpalert->add('"' . ucfirst($modulename) . '" module\'s model created successfully.', "success");
            header('Location: /');
        } else {
            if ($return)
                return false;
            $this->helpers->phpalert->add('Attempt to create "' . ucfirst($modulename) . '" module\'s model failed.', "failure");
            header('Location: /');
        }
    }

    // Generate a controller file, based on a template, adapting it to the module which is being created.
    private function createcontroller($modulename, $fields, $return = false) {
        $breakline = (PATH_SEPARATOR == ":" ? "\r\n" : "\n");
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/controller.mtx");

        $f = str_replace("_MODULE_NAME_", $modulename, $f);
        $f = str_replace("_CLASS_NAME_", ucfirst($modulename), $f);

        $file_handler = "";
        foreach ($fields as $field) {
            $tablekey = preg_replace('/\([^)]*\)|[()]/', '', $field->Type);
            if ($this->datatypes[$tablekey] == "file") {
                $file_handler = 'foreach($_FILES as $k => $f){' . $breakline .
                        'if ($_FILES[$k]["size"]) {' . $breakline .
                        '$dataset[$k] = $_FILES[$k]["type"].";".file_get_contents($_FILES[$k]["tmp_name"]);' . $breakline .
                        '}' . $breakline .
                        '}';
                break;
            }
        }

        $f = str_replace("_SAVE_FILE_HANDLER_", $file_handler, $f);

        $path = $_SERVER["DOCUMENT_ROOT"] . "controllers/";

        if (!file_exists($path))
            mkdir($path, 0777, true);
        touch($path);
        chmod($path, 0777);
        if (file_put_contents($path . $modulename . ".php", $f)) {
            touch($path . $modulename . ".php");
            chmod($path . $modulename . ".php", 0777);
            if ($return)
                return true;
            $this->helpers->phpalert->add('"' . ucfirst($modulename) . '" module\'s controller created successfully.', "success");
            header('Location: /');
        } else {
            if ($return)
                return false;
            $this->helpers->phpalert->add('Attempt to create "' . ucfirst($modulename) . '" module\'s controller failed.', "failure");
            header('Location: /');
        }
    }

    // Returns a well formated HTML table row string, based on field type.
    private function tableListing($f, $modulename, $header = false) {
        $breakline = (PATH_SEPARATOR == ":" ? "\r\n" : "\n");
        if ($header)
            return "<th>" . ucfirst($f->Field) . ($f->Type == "tinyint(1)" ? "?" : "") . "</th>" . $breakline;
        else {
            if ($f->Type == "tinyint(1)") {
                $content = '<?php echo (empty($val->' . $f->Field . ') ? "No" : "Yes"); ?>';
            } elseif ($this->datatypes[preg_replace('/\([^)]*\)|[()]/', '', $f->Type)] == "file") {
                $content = '<?php if(!empty($val->' . $f->Field . ')): ?><a href="/' . $modulename . '/download/?args[0][field]=' . $f->Field . '&args[0][conditions][' . $this->primarykey . ']=<?php echo $val->' . $this->primarykey . '; ?>">Download file</a><?php endif; ?>';
            } else {
                $content = '<?php echo $val->' . $f->Field . '; ?>';
            }
            return '<td>' . $content . '</td>' . $breakline;
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
        $fl = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/view_listing.mtx");
        $fr = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "engine/mvcgenerator/templates/view_register.mtx");

        $fl = str_replace("_MODULE_NAME_", $modulename, $fl);
        $fr = str_replace("_MODULE_NAME_", $modulename, $fr);

        $list_headers = "";
        $list_values = "";
        $form_fields = "";
        foreach ($fields as $f) {
            $list_headers .= $this->tableListing($f, $modulename, true);
            $list_values .= $this->tableListing($f, $modulename, false);
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
        chmod($_SERVER['DOCUMENT_ROOT'] . "views/", 0777);
        chmod($viewpath, 0777);

        if (file_put_contents($viewpath . "listing.php", $fl) && file_put_contents($viewpath . "register.php", $fr)) {
            touch($viewpath . "listing.php");
            chmod($viewpath . "listing.php", 0777);
            touch($viewpath . "register.php");
            chmod($viewpath . "register.php", 0777);
            if ($return)
                return true;
            $this->helpers->phpalert->add('"' . ucfirst($modulename) . '" module\'s views created successfully.', "success");
            header('Location: /');
        } else {
            if ($return)
                return false;
            $this->helpers->phpalert->add('Attempt to create "' . ucfirst($modulename) . '" module\'s views failed.', "failure");
            header('Location: /');
        }
    }

    // Generate a model, a controller and the 2 view files, calling the other creation methods within this class.
    private function createall($modulename, $fields) {
        if (!$this->createcontroller($modulename, $fields, true)) {
            $this->helpers->phpalert->add("Attempt to create module's controller failed. No file created.", "failure");
            header('Location: /');
            return false;
        }
        if (!$this->createmodel($modulename, $fields, true)) {
            $this->helpers->phpalert->add("Attempt to create module's model failed. But controller file created with success.");
            header('Location: /');
            return false;
        }
        if (!$this->createviews($modulename, $fields, true)) {
            $this->helpers->phpalert->add("Attempt to create module's views failed. But controller and model files created with success.");
            header('Location: /');
            return false;
        }

        $this->helpers->phpalert->add("Module " . $modulename . " MVC created successfully.", "success");
        header('Location: /');
    }

}
