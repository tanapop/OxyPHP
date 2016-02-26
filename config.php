<?php
//< DATABASE CONFIGS:

define("MYSQL_DATABASE_ON", true);
define("MYSQL_DBNAME", "marmitao");
define("MYSQL_DBHOST", "localhost");
define("MYSQL_DBUSER", "root");
define("MYSQL_DBPASS", "h7t846m2");
define("MYSQL_CONNECTION_MAX_TRIES", 5);

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
