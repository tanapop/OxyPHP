<?php

/* //////////////////////
  PDO DATABASE CLASS//
 *///////////////////////

class Dblink {

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
    // Data types constants.
    private $datatypes;
    // If true, deactivate automatic commit.
    private $transaction_mode;
    // If true, allow commits to be sent.
    private $tr_commit_flag;
    // An instance of the last sql result executed.
    private $lastresult;
    // A PDOException object
    private $error;

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
            System::log("db_error", "Error message" . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }

        $this->cnnInfo = new stdClass();
        $this->cnnInfo->info = "No connection info.";
        $this->transaction_mode = false;
        $this->tr_commit_flag = true;

        if (!$this->connect(1))
            System::log("db_error", "Attempt to connect to database server failed. Error:" . $this->error);
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
                $this->error = $ex;
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
            System::log("db_error", "Error message: " . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
        }

        $this->disconnect();
        if (!$this->connect(1))
            System::log("db_error", "Attempt to connect to mysql database failed. Error:" . $this->connection);
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

    public function runsql(Sqlobj $sqlobj) {
        try {
            if ($this->transaction_mode && !$this->lastresult) {
                $this->connection->beginTransaction();
            }

            $stmt = $this->prepare_statement($sqlobj->sqlstring, array_values($sqlobj->sqlvalues));
            $res = $stmt->execute();
            
        } catch (PDOException $ex) {
            if ($this->transaction_mode) {
                $this->connection->rollBack();
                $this->transaction_mode = false;
                $this->lastresult = null;
            }
            System::log("db_error", "Error Message" . $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.');
            throw $ex;
        }

        if ($stmt->columnCount() > 0) {
            $res = array();
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $res[] = $row;
            }

            if ($this->transaction_mode) {
                $this->connection->rollBack();
                $this->tr_commit_flag = false;
                System::log('db_error', date('m/d/Y h:i:s') . " - NOTICE: You tried to use some SELECT query(ies) in a transaction. It makes no sense! Only the first SELECT query was executed.");
            }
        } elseif (strpos(strtoupper($sqlobj->sqlstring), 'INSERT') !== false) {
            $res = $this->connection->lastInsertId();
        }


        $this->cnnInfo = (object) get_object_vars($this->connection);

        $this->lastresult = $res;
        return $res;
    }

    public function transaction($sqlset) {
        $this->connection->beginTransaction();
        $this->transaction_mode = false;
        $this->lastresult = null;

        foreach ($sqlset as $sql) {
            try {
                $res = $this->runsql($sql);
            } catch (PDOException $ex) {
                $this->connection->rollBack();
                break;
            }

            if (strpos(strtoupper($sql->sqlstring), 'SELECT') !== false || $res === false) {
                System::log('db_error', date('m/d/Y h:i:s') . " - NOTICE: You tried to use some SELECT query(ies) in a transaction of queries. It makes no sense! Only the first SELECT query was executed.");
                $this->connection->rollBack();
                $this->tr_commit_flag = false;
                break;
            }
        }

        if ($this->tr_commit_flag) {
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
        if ($this->tr_commit_flag) {
            $this->connection->commit();
        }

        return $r;
    }

    private function mapdata($dataset, $key) {
        if (!$key) {
            System::log("db_error", date('m/d/Y h:i:s') . " - NOTICE: Table from where you selected data has not a primary key. So, dataset could not be mapped. It is extremely recommended to define primary keys for all your database tables.");
            return $dataset;
        }

        $result = array();

        foreach ($dataset as $row) {
            if (!isset($result[$row->$key])) {
                $result[$row->$key] = $row;
            } else {
                foreach ((array) $row as $k => $v) {
                    if ($result[$row->$key]->$k != $v) {
                        if (!is_array($result[$row->$key]->$k)) {
                            $result[$row->$key]->$k = array($result[$row->$key]->$k, $v);
                        } else {
                            $arr = $result[$row->$key]->$k;
                            $arr[] = $v;
                            ;
                            $result[$row->$key]->$k = $arr;
                        }
                    }
                }
            }
        }
        return array_values($result);
    }

}

?>