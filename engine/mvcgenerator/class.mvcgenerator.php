<?php

class Mvcgenerator {

    // An instance of Dblink class
    private $dblink;
    // A tables list from database
    private $tables;
    // A dictionary to translate database datatypes to form input's type.
    private $datatypes;
    // The current table's primary key name.
    private $modulename;
    // The global object of Helpers class
    private $helpers;
    private $foreign_referers;

    // Include Mysql class file and instantiate it on this->mysql, write database tables list and set the datatypes dictionary.
    public function __construct() {
        $this->helpers = System::loadClass($_SERVER['DOCUMENT_ROOT'] . "/engine/class.helpers.php", "helpers");

        require_once $_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/class.tbmetadata.php";
        
        $this->dblink = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.dblink.php", 'dblink');
        $this->tables = $this->dblink->dbtables();

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
        include $_SERVER['DOCUMENT_ROOT'] . "/engine/mvcgenerator/view.index.php";

        echo ob_get_clean();
        $this->helpers->phpalert->show();
    }

    public function createmvc($modulename, $fileset) {
        $fields = Tbmetadata::info($modulename)->fields;
        $this->modulename = $modulename;
        $this->foreign_referers = Tbmetadata::info($modulename)->references;
        
        foreach($fields as $k => $f){
            $fields[$k]->Alias = $modulename."_".$f->Field;
            $fields[$k]->Table = $modulename;
         }
        
        foreach ($this->foreign_referers as $r){
            foreach (Tbmetadata::info($r->TABLE_NAME)->fields as $rf){
                $rf->Alias = $r->TABLE_NAME."_".$rf->Field;
                $rf->Table = $r->TABLE_NAME;
                $fields[] = $rf;
            }
        }
        
//        $this->helpers->insecticide->dump(Tbmetadata::info($modulename));
//        $this->helpers->insecticide->dump($fields);
//        die;
        
        call_user_func_array(array($this, "create" . $fileset), array($modulename, $fields));
    }

    // Generate a model file, based on a template, adapting it to the module which is being created.
    private function createmodel($modulename, $fields, $return = false) {
        $breakline = (PATH_SEPARATOR == ";" ? "\r\n" : "\n");
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/engine/mvcgenerator/templates/model.mtx");

        $method_get = 'return $this->_get($fields, $conditions);';
        $method_save = 'return $this->_save($dataset, $conditions);';
        $method_delete = 'return $this->_delete($conditions);';

        if (!empty($this->foreign_referers)) {
            $method_get = '$t = $this->_get_table();' . $breakline . $breakline;
            $joins = '';

            $method_save = '$t = $this->_get_table();' . $breakline . $breakline;
            $method_save .= '$arrSql = array();' . $breakline . $breakline;
            $method_save .= 'if (!empty($conditions)) {' . $breakline;
            $method_save .= '$arrSql[] = $this->sql->update($dataset[$t], $t)' . $breakline
                    . '->where($conditions)' . $breakline
                    . '->output(true);' . $breakline . $breakline;
            
            $method_delete = '$t = $this->_get_table();' . $breakline . $breakline;

            foreach ($this->foreign_referers as $fk) {
                $joins .= '->join("' . $fk->TABLE_NAME . '",array(array(' . $breakline
                        . 'array($t,"' . $fk->REFERENCED_COLUMN_NAME . '"),' . $breakline
                        . 'array("' . $fk->TABLE_NAME . '", "' . $fk->COLUMN_NAME . '"))), "LEFT")' . $breakline;

                $method_save .= '$arrSql[] = $this->sql->delete("' . $fk->TABLE_NAME . '")' . $breakline
                        . '->where(array("' . $fk->COLUMN_NAME . '",$dataset[$t]["' . $fk->REFERENCED_COLUMN_NAME . '"]))' . $breakline
                        . '->output(true);' . $breakline . $breakline;
            }


            $method_get .= '$this->sql' . $breakline . '->select($fields, $t)' . $breakline;
            $method_get .= $joins;
            $method_get .= '->where($conditions);' . $breakline . $breakline;
            $method_get .= 'return $this->dbquery($this->sql->output());';

            $method_save .= 'unset($dataset[$t]);' . $breakline . $breakline;
            $method_save .= '}' . $breakline . $breakline;
            $method_save .= 'foreach($dataset as $table => $data){' . $breakline;
            $method_save .= '$arrSql[] = $this->sql->insert($data, $table)->output(true);' . $breakline;
            $method_save .= '}' . $breakline . $breakline;
            $method_save .= 'return $this->dblink->transaction($arrSsql);' . $breakline;
            
            $method_delete .= '$this->sql' . $breakline . '->delete($t)' . $breakline;
            $method_delete .= $joins;
            $method_delete .= '->where($conditions);' . $breakline . $breakline;
            $method_delete .= 'return $this->dbquery($this->sql->output());';
        }

        $f = str_replace("_CLASS_NAME_", "Model" . ucfirst($modulename), $f);
        $f = str_replace("_METHOD_GET_", $method_get, $f);
        $f = str_replace("_METHOD_SAVE_", $method_save, $f);
        $f = str_replace("_METHOD_DELETE_", $method_delete, $f);

        $path = $_SERVER["DOCUMENT_ROOT"] . "/application/models/";
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
        $f = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/engine/mvcgenerator/templates/controller.mtx");

        $f = str_replace("_CLASS_NAME_", ucfirst($modulename), $f);

        // Replacements for method save() on controller:

        $save_file_handler = "";
        foreach ($fields as $field) {
            $tablekey = preg_replace('/\([^)]*\)|[()]/', '', $field->Type);
            if ($this->datatypes[$tablekey] == "file") {
                Tbmetadata::alter_table($field->Table, "MODIFY ".$field->Field." varchar(255)");
                
                $save_file_handler = 'foreach($_FILES as $k => $f){' . $breakline .
                        'if ($_FILES[$k]["size"]) {' . $breakline .
                        '$filename = uniqid().".".explode(".",$_FILES[$k]["name"])[1];'.$breakline.
                        'if(!move_uploaded_file($_FILES[$k]["tmp_name"], $_SERVER["DOCUMENT_ROOT"]."/application/upload/".$filename)){'.$breakline.'return false;'.$breakline.'}'.$breakline.
                        '$dataset[$k] = $filename;' . $breakline .
                        '}' . $breakline .
                        '}';
                break;
            }
        }

        //

        $listing_search_data = '$fields = "*"';
        if (!empty($this->foreign_referers)) {
            $listing_search_data = '$fields = array("*"';
            foreach ($this->foreign_referers as $fk) {
                $listing_search_data .= ',' . $breakline . 'array("' . $fk->TABLE_NAME . '","*")';
            }
            $listing_search_data .= $breakline . ');';
        }

        $f = str_replace("_SAVE_FILE_HANDLER_", $save_file_handler, $f);
        $f = str_replace("_MODULE_NAME_", $modulename, $f);
        $f = str_replace("_LISTING_SEARCH_DATA_", $listing_search_data, $f);
        $f = str_replace("_REGISTER_SEARCH_DATA_", $listing_search_data, $f);

        $path = $_SERVER["DOCUMENT_ROOT"] . "/application/controllers/";

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
            return "<th>" . ucfirst($f->Alias) . ($f->Type == "tinyint(1)" ? "?" : "") . "</th>" . $breakline;
        else {
            if ($f->Type == "tinyint(1)") {
                $content = '<?php if(is_array($val->' . $f->Alias . ')) foreach($val->' . $f->Alias . ' as $v){echo "<p>".(empty($v) ? "No" : "Yes")."</p>";} else echo (empty($val->' . $f->Alias . ') ? "No" : "Yes"); ?>';
            } elseif ($this->datatypes[preg_replace('/\([^)]*\)|[()]/', '', $f->Type)] == "file") {
                $content = '<?php if(is_array($val->' . $f->Alias . ')) foreach($val->' . $f->Alias . ' as $v){if(!empty($v)): ?><p><a href="/application/upload/<?php echo $v; ?>">File</a></p><?php endif; } else if(!empty($val->' . $f->Alias . ')): ?><a href="/application/upload/<?php echo $val->'.$f->Alias.'; ?>">File</a><?php endif; ?>';
            } else {
                $content = '<?php if(is_array($val->' . $f->Alias . ')) foreach($val->' . $f->Alias . ' as $v){echo "<p>".$v."</p>"; } else echo $val->' . $f->Alias . '; ?>';
            }
            return '<td>' . $content . '</td>' . $breakline;
        }
    }

    // Returns a well formated form input string, based on field type.
    private function formField($f) {
        $breakline = (PATH_SEPARATOR == ":" ? "\r\n" : "\n");
        if ($f->Field == Tbmetadata::info($this->modulename)->key->keyname) {
            $input = '<div class="col-md-12">' . $breakline;
            $input .= '<input id="input-' . $f->Field . '" type="hidden" name="' . $f->Field . '" value="<?php echo !empty($dataset) ? $dataset->' . $f->Field . ' : ""; ?>">' . $breakline;
            $input .= '</div>';
        } elseif ($f->Type == "tinyint(1)") {
            $input = '<div class="col-md-12">' . $breakline;
            $input .= '<label>' . ucfirst($f->Field) . '?</label>' . $breakline;
            $input .= '<input class="input-' . $f->Field . '" type="radio" name="' . $f->Field . '" value="0" <?php echo (empty($dataset->' . $f->Field . ') ? "checked" : ""); ?>> No&nbsp;&nbsp;' . $breakline;
            $input .= '<input class="input-' . $f->Field . '" type="radio" name="' . $f->Field . '" value="1" <?php echo (!empty($dataset->' . $f->Field . ') ? "checked" : ""); ?>> Yes' . $breakline;
            $input .= '</div>';
        } elseif ($this->datatypes[preg_replace('/\([^)]*\)|[()]/', '', $f->Type)] == "file") {
            $input = '<div class="col-md-12">' . $breakline;
            $input .= '<input id="input-' . $f->Field . '" type="file" name="' . $f->Field . '" ' . ($f->Null == "NO" && $f->Default == null ? "required " : "") . '>';
            $input .= '</div>';
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
        $fl = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/engine/mvcgenerator/templates/view_listing.mtx");
        $fr = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/engine/mvcgenerator/templates/view_register.mtx");

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

        $viewpath = $_SERVER['DOCUMENT_ROOT'] . "/application/views/" . $modulename . "/";

        if (!file_exists($viewpath))
            mkdir($viewpath, 0777, true);
        touch($viewpath);
        chmod($_SERVER['DOCUMENT_ROOT'] . "/application/views/", 0777);
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
        if (!$this->createmodel($modulename, $fields, true)) {
            $this->helpers->phpalert->add("Attempt to create module's model failed. No file was created.");
            header('Location: /');
            return false;
        }
        if (!$this->createcontroller($modulename, $fields, true)) {
            $this->helpers->phpalert->add("Attempt to create module's controller failed. But model file was created with success.", "failure");
            header('Location: /');
            return false;
        }
        if (!$this->createviews($modulename, $fields, true)) {
            $this->helpers->phpalert->add("Attempt to create module's views failed. But controller and model files were created with success.");
            header('Location: /');
            return false;
        }

        $this->helpers->phpalert->add("Module " . $modulename . " MVC created successfully.", "success");
        header('Location: /');
    }

}
