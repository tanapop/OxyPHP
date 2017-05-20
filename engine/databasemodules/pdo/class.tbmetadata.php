<?php

class Tbmetadata {

    private static $collection;

    private function __construct() {
        
    }

    public static function info($tablename) {
        if (!isset(self::$collection[$tablename])) {
            $cnn = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.dbclass.php", 'dbclass');

            $res_f = $cnn->query("DESCRIBE " . $tablename);
            $fields = array();
            $key = false;
            while ($row = $res_f->fetch(PDO::FETCH_OBJ)) {
                $fields[] = $row;
                
                if($row->Key === "PRI"){
                    $key = (object) array(
                        'keyname' => $row->Field,
                        'keyalias' => $tablename."_".$row->Field
                    );
                }
            }
            $fields = (object) $fields;

            $res_r = $cnn->query("SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = '" . DBNAME . "' AND REFERENCED_TABLE_NAME = '" . $tablename . "';");
            $refs = array();
            while($row1 = $res_r->fetch(PDO::FETCH_OBJ)){
                $refs[] = $row1;
            }
            $refs = (object) $refs;
            
            self::$collection[$tablename] = (object) array(
                        'fields' => $fields,
                        'references' => $refs,
                        'key' => $key
                
            );
        }
        
        return self::$collection[$tablename];
    }
    
}
