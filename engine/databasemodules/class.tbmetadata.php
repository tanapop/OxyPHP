<?php

class Tbmetadata {

    private static $collection;

    private function __construct() {
        
    }

    public static function info($tablename) {
        if (!isset(self::$collection[$tablename])) {
            $cnn = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.dblink.php", 'dblink');
            $sql = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.sql.php", 'sql');
            $res_f = $cnn->runsql($sql->write("DESCRIBE `" . $tablename . "`", array(), $tablename)->output(true));

            $fields = array();
            $key = false;
            foreach ($res_f as $row) {
                $fields[] = $row;

                if ($row->Key === "PRI") {
                    $key = (object) array(
                                'keyname' => $row->Field,
                                'keyalias' => $tablename . "_" . $row->Field
                    );
                }
            }

            $res_r = $cnn->runsql($sql->write("SELECT TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME, REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = '" . DBNAME . "' AND REFERENCED_TABLE_NAME = '" . $tablename . "';", array(), $tablename)->output(true));

            self::$collection[$tablename] = (object) array(
                        'fields' => $fields,
                        'references' => $res_r,
                        'key' => $key
            );
        }

        return self::$collection[$tablename];
    }

    public static function alter_table($tablename, $cmd) {
        $cnn = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.dblink.php", 'dblink');
        $sql = System::loadClass($_SERVER["DOCUMENT_ROOT"] . "/engine/databasemodules/" . DBCLASS . "/class.sql.php", 'sql');
        
        return $cnn->runsql($sql->write("ALTER TABLE `" . $tablename . "` ".$cmd, array(), $tablename)->output(true));
    }

}
