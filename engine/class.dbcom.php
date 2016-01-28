<?php

class MysqlDb {

    private $dbhost;
    private $dbname;
    private $dbuser;
    private $dbpass;
    private $cnnInfo;

    public function __construct($dbobj = array(
        "dbhost" => DBHOST,
        "dbuser" => DBUSER,
        "dbpass" => DBPASS,
        "dbname" => DBNAME)) {

        $this->dbhost = DBHOST;
        $this->dbname = DBNAME;
        $this->dbuser = DBUSER;
        $this->dbpass = DBPASS;
        $this->cnnInfo = new stdClass();
        $this->cnnInfo->info = "No connection info.";
    }

    public function setInfo($dbinfo) {
        if(!is_array($dbinfo)){
            echo 'MysqlDb Error: Invalid $dbinfo argument supplied. It must be an array.';
            return false;
        }
        
        foreach($dbobj as $key => $val){
            if($key != "dbhost" && $key != "dbname" && $key != "dbuser" && $key != "dbpass"){
            echo 'MysqlDb Error: Invalid key name for the given argument $dbinfo';
                return false;
            }
        }
        
        $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : $this->dbhost);
        $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : $this->dbname);
        $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : $this->dbuser);
        $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : $this->dbpass);
    }
    
    public function getInfo(){
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
