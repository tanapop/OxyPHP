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
    // Connection's link identifier. If connection fails, a string containing the error description.
    private $connection;

    // Verifies if database connection data is valid, then sets the properties with those values.
    public function __construct($dbinfo = array()) {

        if (!is_array($dbinfo)) {
            System::debug(array("class.myswql: Argument Error: Invalid argument supplied for method __construct. It must be an array."), array('$dbinfo' => $dbinfo));
        }

        if (!empty($dbinfo)) {
            foreach ($dbinfo as $key => $data) {
                if ($key != "dbhost" && $key != "dbname" && $key != "dbuser" && $key != "dbpass")
                    System::debug(array('class.myswql: Argument Error: Invalid argument keyname for method __construct. Valid names: "dbhost", "dbname", "dbuser", "dbpass"'), array('$dbinfo' => $dbinfo));
            }
        }

        $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : MYSQL_DBHOST);
        $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : MYSQL_DBNAME);
        $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : MYSQL_DBUSER);
        $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : MYSQL_DBPASS);

        $this->cnnInfo = new stdClass();
        $this->cnnInfo->info = "No connection info.";
    }

    /* Tries to connect to mysql database much times as configured. If all attempts fails, write error to property and returns false.
     * Returns true on first success.
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

    /* Verifies if database connection data suplied is valid, then sets the properties with those values.
     * Use it for runtime connection changes.
     */

    public function setInfo($dbinfo) {
        if (!is_array($dbinfo)) {
            System::debug(array("class.myswql: Argument Error: Invalid argument supplied for method setInfo. It must be an array."), array('$dbinfo' => $dbinfo));
        }

        if (!empty($dbinfo)) {
            foreach ($dbinfo as $key => $data) {
                if ($key != "dbhost" && $key != "dbname" && $key != "dbuser" && $key != "dbpass")
                    System::debug(array('class.myswql: Argument Error: Invalid argument keyname for method setInfo. Valid names: "dbhost", "dbname", "dbuser", "dbpass"'), array('$dbinfo' => $dbinfo));
            }
        }

        $this->dbhost = (isset($dbinfo["dbhost"]) ? $dbinfo["dbhost"] : $this->dbhost);
        $this->dbname = (isset($dbinfo["dbname"]) ? $dbinfo["dbname"] : $this->dbname);
        $this->dbuser = (isset($dbinfo["dbuser"]) ? $dbinfo["dbuser"] : $this->dbuser);
        $this->dbpass = (isset($dbinfo["dbpass"]) ? $dbinfo["dbpass"] : $this->dbpass);
    }

    // Returns all current connection information.
    public function getInfo() {
        return (object) array(
                    "dbhost" => $this->dbhost,
                    "dbname" => $this->dbname,
                    "dbuser" => $this->dbuser,
                    "dbpass" => $this->dbpass,
                    "info" => $this->cnnInfo
        );
    }

    /* Connect to database, triggers the sql query, save current connection information, then returns result data.
     * If it's a mysql resource, process it into an array of objects before returning.
     */

    public function query($sql) {
        if (!$this->connect())
            System::debug(array("Attempt to connect to mysql database failed."), array("Error" => $this->connection));

        $res = $this->connection->query($sql);

        if ($res === true || $res === false) {
            $ret = $res;
        } else {
            $ret = array();
            while ($row = $res->fetch_assoc()) {
                $ret[] = (object) $row;
            }
        }

        $this->cnnInfo = (object) get_object_vars($this->connection);

        $this->connection->close();
        return $ret;
    }

}

?>
