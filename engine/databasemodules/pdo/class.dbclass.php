<?php

/* //////////////////////
  PDO DATABASE CLASS//
 *///////////////////////

class Dbclass {

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
    // If true, deactivate automatic commit
    private $transaction_mode;
    private $lastresult;

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
            System::log("db_error","Error message" . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }

        $this->cnnInfo = new stdClass();
        $this->cnnInfo->info = "No connection info.";
        $this->transaction_mode = false;

        if (!$this->connect(1))
            System::log("db_error","Attempt to connect to database server failed. Error:" . $this->connection);
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
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
            System::log("db_error","Error message: " . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }

        $this->disconnect();
        if (!$this->connect(1))
            System::log("db_error","Attempt to connect to mysql database failed. Error:" . $this->connection);
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

    private function prepare_statement($sqlstring, $sqlvalues = array()) {
        $stmt = $this->connection->prepare($sqlstring);

        if (!empty($sqlvalues)) {
            foreach ($sqlvalues as $key => $val) {
                $stmt->bindValue(($key + 1), $val, $this->datatypes[gettype($val)]);
            }
        }

        return $stmt;
    }

    public function query(Sqlobj $sqlobj, $debug = true) {
        try {
            if ($this->transaction_mode && !$this->lastresult) {
                $this->connection->beginTransaction();
            }

            $stmt = $this->prepare_statement($sqlobj->sqlstring, array_values($sqlobj->sqlvalues));
            $res = $stmt->execute();

            if (strpos(strtoupper($sqlobj->sqlstring), 'SELECT') !== false) {
                $res = array();
                while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                    $res[] = $row;
                }
            } elseif (strpos(strtoupper($sqlobj->sqlstring), 'INSERT') !== false) {
                $res = $this->connection->lastInsertId();
            }


            $this->cnnInfo = (object) get_object_vars($this->connection);

            $this->lastresult = $res;
            return $res;
        } catch (PDOException $ex) {
            if ($this->transaction_mode) {
                $this->connection->rollBack();
                $this->transaction_mode = false;
                $this->lastresult = null;
            }
            if ($debug) {
                System::log("db_error","Error Message" . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
            }
        }
    }

    public function transaction($sqlset) {
        $this->connection->beginTransaction();
        $this->transaction_mode = false;
        $this->lastresult = null;
        $commit = true;

        foreach ($sqlset as $sql) {
            try {
                $res = $this->query($sql, false);
            } catch (PDOException $ex) {
                $this->connection->rollBack();
                System::log("db_error","Error Message" . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
            }

            if (strpos(strtoupper($sql->sqlstring), 'SELECT') !== false || $res === false) {
                System::log('db_error', date('m/d/Y h:i:s') . " - NOTICE: You tried to use some SELECT query(ies) in a transaction of queries. It makes no sense! Only the first SELECT query was executed.");
                $this->connection->rollBack();
                $commit = false;
                break;
            }
        }

        if ($commit) {
            $this->connection->commit();
        }

        return $res;
    }

    public function transaction_mode($usemode = true) {
        if (!empty($this->lastresult)) {
            System::log("db_error","There is an active transaction. It must be finished before turning on or off the transaction mode.");
        }
        $this->transaction_mode = $usemode;
        $this->lastresult = false;
    }

    public function commit() {
        $r = $this->lastresult;
        $this->lastresult = null;
        $this->connection->commit();
        return $r;
    }

}

?>