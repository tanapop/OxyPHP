<?php
//< DATABASE CONFIGS:

define("DBNAME", "marmitao");
define("DBHOST", "localhost");
define("DBUSER", "root");
define("DBPASS", "h7t846m2");
define("DBTYPE", 'mysql');
define("DBCONNECTION_MAX_TRIES", 5);
define("DBCLASS", "mysqli");  /* Built in, ready for use classes: pdo, mysqli(DEPRECATED). */

//>

//< SUPER ADMIN USER:
define("ADMIN_EMAIL", "example@example.com");
define("ADMIN_NAME", "Super Administrator");
define("ADMIN_PASS", "oxyphp");
//>

//< SYSTEM:
define("DEFAULT_CONTROLLER","home");
define("DEFAULT_METHOD", "index");

define("SETUP_MODE", true);

define("SHOW_DEBUG", true);
define("DEBUG_LOGGING", true);
define("HANDLE_ERROR_TYPES",E_ALL & ~E_NOTICE & ~E_USER_NOTICE);
define("LOG_FILE_PATH", $_SERVER['DOCUMENT_ROOT'].'log/');
//>

?>
