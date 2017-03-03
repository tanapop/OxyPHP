<?php
// Includes global main classes.
require_once "engine/class.objloader.php";
require_once "engine/class.helpers.php";
require_once "engine/class.system.php";

/* If the keyword "_asyncload" is detected within the URI, system understand that this is an asynchronous request.
 * Then it loads the main class System, which executes the request. Thereafter, it stops script's execution
 * to avoid unwanted data, like HTML <head>, returning from the request.
 */
if (strpos(strtolower(str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", urldecode($_SERVER["REQUEST_URI"]))), "_asyncload") !== false) {
    $helpers = new Helpers();
    $system = new System();
    die;
}
?>
<!DOCTYPE html>
<html>
    <head>

        <!--<META>-->
        <title>Oxy - Object Oriented MVC Framework - PHP Edition</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <link rel="icon" type="image/png" href="/media/img/oxylogo.png">
        <!--</META>-->


        <!--<JS>-->
        <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <!--</JS>-->


        <!--<CSS>-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="/media/css/default.css"/>
        <!--</CSS>-->
    </head>
    <body>
        <?php
        // Loads system, which calls a method from a controller. Method and controller are specified in REQUEST or URI.
        $helpers = new Helpers();
        $system = new System();
        ?>
    </body>
</html>
