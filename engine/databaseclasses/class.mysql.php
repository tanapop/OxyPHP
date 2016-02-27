<?php

class Mysql {

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
    // Stores a query error, if it occurs.
    public $error;

    /* Verifies if database connection data is valid, then sets the properties with those values.
     * Connect to mysql server and save the connection in a property.
     */

    public function __construct($dbinfo = array()) {
        $this->error = 0;

        try {
            $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : DBHOST);
            $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : DBNAME);
            $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : DBUSER);
            $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : DBPASS);
        } catch (Exception $ex) {
            System::debug(array("Error message" => $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.'), array('Parameter dbinfo' => $dbinfo));
        }

        $this->cnnInfo = new stdClass();
        $this->cnnInfo->info = "No connection info.";

        if (!$this->connect())
            System::debug(array("Attempt to connect to mysql database failed. Error:" => $this->connection), array());
    }

    // When this class's object is destructed, close the connection to mysql server.
    public function __destruct() {
        $this->disconnect();
    }

    /* Tries to connect to mysql database much times as configured. If all attempts fails, 
     * write error to property and returns false. Returns true on first success.
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
    protected function disconnect() {
        $this->connection->close();
    }

    /* Verifies if database connection data suplied is valid, sets the properties with those values, then
     * reconnect to mysql server with the new information.
     */

    public function setconnection($dbinfo) {
        try {
            $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : $this->dbhost);
            $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : $this->dbname);
            $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : $this->dbuser);
            $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : $this->dbpass);
        } catch (Exception $ex) {
            System::debug(array("Error message" => $ex->getMessage() . '. In ' . $ex->getFile() . ' on line ' . $ex->getLine() . '.'), array('Parameter dbinfo' => $dbinfo));
        }

        $this->connection->close();
        if (!$this->connect())
            System::debug(array("Attempt to connect to mysql database failed. Error:" => $this->connection), array());
    }

    // Returns all current connection information.
    public function getinfo() {
        return (object) array(
                    "dbhost" => $this->dbhost,
                    "dbname" => $this->dbname,
                    "dbuser" => $this->dbuser,
                    "dbpass" => $this->dbpass,
                    "info" => $this->cnnInfo
        );
    }

    /* Triggers the sql query, save current connection information, then returns result data.
     * If it's a mysql resource, process it into an array of objects before returning.
     */

    public function query($sql) {
        $res = $this->connection->query($sql);

        if ($this->connection->errno) {
            $this->error = "Error " . $this->connection->errno . ": " . $this->connection->error;
            System::debug(array("While executing mysql query an error occured" => $this->error, "Mysql query" => $sql), array());
        }

        if ($res === true || $res === false) {
            $ret = $res;
        } else {
            $ret = array();
            while ($row = $res->fetch_assoc()) {
                $ret[] = (object) $row;
            }
        }

        $this->cnnInfo = (object) get_object_vars($this->connection);

        return $ret;
    }

    // Escape data properly for mysql statements.
    public function escapevar($dataset) {
        if (is_array($dataset)) {
            foreach ($dataset as $key => $data) {
                if (!is_numeric($data))
                    $dataset[$key] = mysqli_real_escape_string($this->connection, $data);
            }
        } elseif (is_object($dataset)) {
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
