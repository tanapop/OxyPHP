<?php

/* //////////////////////
  MYSQLI DATABASE CLASS//
 *///////////////////////

class Dbclass {

    // Mysql database host server. Example: "localhost".
    private $dbhost;
    // Database's name.
    private $dbname;
    // Database's username
    private $dbuser;
    // Database's password
    private $dbpass;
    // Information of current connection.
    private $cnnInfo;
    // Connection's link identifier. If connection fails, a string containing the connection error description.
    private $connection;
    // If true, deactivate automatic commit.
    private $transaction_mode;
    // An instance of the last sql result executed.
    private $lastresult;

    /* Verifies if database connection data is valid, then sets the properties with those values.
     * Connect to mysql server and save the connection in a property.
     */

    public function __construct($dbinfo = array()) {

        try {
            $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : DBHOST);
            $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : DBNAME);
            $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : DBUSER);
            $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : DBPASS);
        } catch (Exception $ex) {
            System::log("db_error", "Message: " . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }

        $this->cnnInfo = new stdClass();
        $this->cnnInfo->info = "No connection info.";


        if (!$this->connect())
            System::log("db_error", "Attempt to connect to mysql database failed. Error:" . $this->connection);
    }

    // When this class's object is destructed, close the connection to mysql server.
    public function __destruct() {
        $this->disconnect();
    }

    /* Tries to connect to mysql database much times as configured. If all attempts fails, 
     * write an error to property and returns false. Returns true on first success.
     */

    private function connect() {

        $this->connection = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);

        if ($this->connection->connect_error) {
            $this->connection->close();
            $currenttry = 1;

            while ($currenttry < MYSQL_CONNECTION_MAX_TRIES) {
                $this->connection = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
                if (!$this->connection->connect_error) {
                    return true;
                } else {
                    $error = $this->connection->connect_error;
                    $this->connection->close();
                }
                $currenttry++;
            }
            $this->connection = $error;
            return false;
        }

        return true;
    }

    // Force the current connection to close.
    public function disconnect() {
        $this->connection->close();
    }

    /* Verifies if database connection data suplied is valid, sets the properties with those values, then
     * reconnect to mysql server with the new information.
     */

    public function reconnect($dbinfo) {
        try {
            $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : $this->dbhost);
            $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : $this->dbname);
            $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : $this->dbuser);
            $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : $this->dbpass);
        } catch (Exception $ex) {
            System::log("db_error", "Error message" . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }

        $this->connection->close();
        if (!$this->connect())
            System::log("db_error", "Attempt to connect to mysql database failed. Error:" . $this->connection);
    }

    // Returns all current connection information.
    public function info() {
        return (object) array(
                    "dbhost" => $this->dbhost,
                    "dbname" => $this->dbname,
                    "dbuser" => $this->dbuser,
                    "dbpass" => $this->dbpass,
                    "info" => $this->cnnInfo
        );
    }

    public function describeTable($tablename) {
        $res = $this->connection->query("DESCRIBE " . $tablename);
        $ret = array();
        while ($row = mysqli_fetch_assoc($res)) {
            $ret[] = (object) $row;
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

    /* Triggers the sql query, save current connection information, then returns result data.
     * If it's a mysql resource, process it into an array of objects before returning.
     */

    public function query($sql) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        if ($this->transaction_mode && !$this->lastresult) {
            $this->connection->autocommit(false);
        }

        try {
            $res = $this->connection->query($sql);
        } catch (mysqli_sql_exception $ex) {
            if ($this->transaction_mode) {
                $this->connection->rollback();
                $this->transaction_mode = false;
                $this->lastresult = null;
            }
            System::log("db_error", "While executing mysql query an error occured. " . $ex->getMessage() . ". Mysql query:'" . $sql . "'");
            throw $ex;
        }

        if ($res === true || $res === false) {
            $ret = $res;
        } else {
            $ret = array();
            while ($row = $res->fetch_assoc()) {
                $ret[] = (object) $row;
            }
            $res->close();
        }

        $this->cnnInfo = (object) get_object_vars($this->connection);
        $this->lastresult = $ret;

        return $ret;
    }

    public function transaction($sqlset) {
        $this->connection->autocommit(false);
        $this->transaction_mode = false;
        $this->lastresult = null;
        $commit = true;

        foreach ($sqlset as $sql) {
            try {
                $res = $this->query($sql);
            } catch (mysqli_sql_exception $ex) {
                $this->connection->rollback();
            }
            
            if (strpos(strtoupper($sql), 'SELECT') !== false) {
                System::log('db_error', date('m/d/Y h:i:s') . " - NOTICE: You tried to use some SELECT query(ies) in a transaction of queries. It makes no sense! Only the first SELECT query was executed.");
                $this->connection->rollback();
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
            System::log("db_error", "There is an active transaction. It must be finished before turning on or off the transaction mode.");
            return false;
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

    // Escape data properly for mysql statements.
    public function escapevar($dataset) {
        if (is_array($dataset)) {
            foreach ($dataset as $key => $data) {
                if (!is_numeric($data))
                    $dataset[$key] = mysqli_real_escape_string($this->connection, $data);
            }
        } elseif (gettype($dataset) === "object") {
            foreach ($dataset as $key => $data) {
                if (!is_numeric($data))
                    $dataset->$key = mysqli_real_escape_string($this->connection, $data);
            }
        } elseif (!is_numeric($dataset)) {
            $dataset = mysqli_real_escape_string($this->connection, $dataset);
        }

        return $dataset;
    }

}

?>
