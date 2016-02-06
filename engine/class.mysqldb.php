<?php

class MysqlDb {

    private $dbhost;
    private $dbname;
    private $dbuser;
    private $dbpass;
    private $cnnInfo;

    public function __construct($dbinfo = array()) {

        if (!is_array($dbinfo)) {
            exit('Error: Invalid argument supplied for method __construct within class.mysqldb.php. It must be an array.');
        }

        if (!empty($dbinfo)) {
            foreach ($dbinfo as $key => $data) {
                switch ($key) {
                    case 'dbhost':
                        continue;

                    case 'dbuser':
                        continue;

                    case 'dbpass':
                        continue;

                    case 'dbname':
                        continue;

                    default:
                        exit("Error: Invalid argument keyname for method __construct within class.mysqldb.php");
                }
            }
        }

        $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : DBHOST);
        $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : DBNAME);
        $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : DBUSER);
        $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : DBPASS);

        $this->cnnInfo = new stdClass();
        $this->cnnInfo->info = "No connection info.";
    }

    public function setInfo($dbinfo) {
        if (!is_array($dbinfo) || empty($dbinfo)) {
            exit('Error: Invalid argument supplied for method setInfo within class.mysqldb.php. It must be an array.');
        }

        foreach ($dbinfo as $key => $data) {
            switch ($key) {
                case 'dbhost':
                    continue;

                case 'dbuser':
                    continue;

                case 'dbpass':
                    continue;

                case 'dbname':
                    continue;

                default:
                    exit("Error: Invalid argument keyname for method setInfo within class.mysqldb.php");
            }
        }

        $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : $this->dbhost);
        $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : $this->dbname);
        $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : $this->dbuser);
        $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : $this->dbpass);
    }

    public function getInfo() {
        return (object) array(
                    "dbhost" => $this->dbhost,
                    "dbname" => $this->dbname,
                    "dbuser" => $this->dbuser,
                    "dbpass" => $this->dbpass,
                    "info" => $this->cnnInfo
        );
    }

    public static function query($sql) {
        $cnn = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);

        $res = $cnn->query($sql);

        if ($res === true || $res === false) {
            $ret = $res;
        } else {
            $ret = array();
            while ($row = $res->fetch_assoc()) {
                $ret[] = (object) $row;
            }
        }

        $this->cnnInfo = (object) get_object_vars($cnn);

        $cnn->close();
        return $ret;
    }

}

?>
