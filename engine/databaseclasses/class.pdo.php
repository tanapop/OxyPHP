<?php

class Oxypdo {

    // Database host server. Example: "localhost".
    private $dbhost;
    // Database's name.
    private $dbname;
    // Database's username
    private $dbuser;
    // Database's password
    private $dbpass;
    // Database type (SGBD)
    private $dbtype;
    // Information of current connection.
    private $cnnInfo;
    // Connection's link identifier. If connection fails, a PDOExcetion object, containing error info.
    private $connection;
    // Stores a query error, if it occurs.
    public $queryerror;
    // Data types constants
    private $datatypes;

    /* Verifies if database connection data is valid, then sets the properties with those values.
     * Connect to database server and save the connection in a property.
     */

    public function __construct($dbinfo = array()) {
        $this->error = 0;
        $this->datatypes = array(
            'boolean' => PDO::PARAM_BOOL,
            'integer' => PDO::PARAM_INT,
            'double' => PDO::PARAM_STR,
            'string' => PDO::PARAM_STR,
            'resource' => PDO::PARAM_LOB
        );

        try {
            $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : DBHOST);
            $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : DBNAME);
            $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : DBUSER);
            $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : DBPASS);
            $this->dbtype = (isset($dbinfo["dbtype"]) ? $dbinfo["dbtype"] : DBTYPE);
        } catch (Exception $ex) {
            System::debug(array("Error message" => $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.'), array('Parameter dbinfo' => $dbinfo));
        }

        $this->cnnInfo = new stdClass();
        $this->cnnInfo->info = "No connection info.";

        if (!$this->connect(1))
            System::debug(array("Attempt to connect to database server failed. Error:" => $this->connection), array());
    }

    // When this class's object is destructed, close the connection to database server.
    public function __destruct() {
        $this->disconnect();
    }

    /* Tries to connect to database server much times as configured. If all attempts fails, 
     * write an error to property and returns false. Returns true on first success.
     */

    private function connect($count) {
        try {
            $this->connection = new PDO($this->dbtype . ':host=' . $this->dbhost . ';dbname=' . $this->dbname, $this->dbuser, $this->dbpass);
            return true;
        } catch (PDOException $ex) {
            $this->connection = null;
            if ($count <= DBCONNECTION_MAX_TRIES) {
                $this->connect($count + 1);
            } else {
                $this->connection = $ex;
                return false;
            }
        }
    }

    // Force the current connection to close.
    public function disconnect() {
        $this->connection = null;
    }

    /* Verifies if database connection data suplied is valid, sets the properties with those values, then
     * reconnect to database server with the new information.
     */

    public function reconnect($dbinfo) {
        try {
            $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : $this->dbhost);
            $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : $this->dbname);
            $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : $this->dbuser);
            $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : $this->dbpass);
            $this->dbtype = (isset($dbinfo["dbtype"]) ? $dbinfo["dbtype"] : $this->dbtype);
        } catch (Exception $ex) {
            System::debug(array("Error message" => $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.'), array('Parameter dbinfo' => $dbinfo));
        }

        $this->disconnect();
        if (!$this->connect(1))
            System::debug(array("Attempt to connect to mysql database failed. Error:" => $this->connection), array());
    }

    // Returns all current connection information.
    public function info() {
        return (object) array(
                    "dbhost" => $this->dbhost,
                    "dbname" => $this->dbname,
                    "dbuser" => $this->dbuser,
                    "dbpass" => $this->dbpass,
                    "dbtype" => $this->dbtype,
                    "info" => $this->cnnInfo
        );
    }

    public function describeTable($tablename) {
        $res = $this->connection->query("DESCRIBE " . $tablename);
        $ret = array();
        while ($row = $res->fetch(PDO::FETCH_OBJ)) {
            $ret[] = $row;
        }

        return $ret;
    }

    public function dbtables() {
        $res = $this->connection->query("SHOW TABLES");
        $ret = array();
        $keyname = "Tables_in_" . DBNAME;
        foreach ($res as $t) {
            $ret[] = $t[$keyname];
        }

        return $ret;
    }

    public function query($sqldata) {
        list($presql, $values) = $sqldata;

        try {
            $presql = $this->connection->prepare($presql);
            if (!empty($values)) {
                foreach ($values as $key => $val) {
                    $presql->bindParam((is_numeric($key) ? $key + 1 : $key), $val, $this->datatypes[gettype($val)]);
                }
            }
            $res = $presql->execute();

            if (!$res) {
                $this->queryerror = $this->connection->errorInfo();
                System::debug(array("SQL" => $sqldata[0], 'Error Message' => "While executing query, an error occured. More info listed bellow"), array('Values' => $values, 'Query Error' => $this->queryerror));
                return false;
            }

            $this->cnnInfo = (object) get_object_vars($this->connection);

            if ($res === true || $res === false) {
                return $res;
            } else {
                $ret = array();
                while ($row = $presql->fetch(PDO::FETCH_OBJ)) {
                    $ret[] = $row;
                }
                return $ret;
            }
        } catch (PDOException $ex) {
            System::debug(array("Error Message" => $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.'), array('Parameter dbinfo' => $dbinfo));
        }
    }

}

?>