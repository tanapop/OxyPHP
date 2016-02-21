<?php
// Includes global configs and global System class.
require_once "config.php";
require_once "constants.php";
require_once "engine/class.objloader.php";
require_once "engine/class.system.php";

// Create the global instance of System class.
$system = new System();
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
        <script src="/media/js/main.js"></script>
        <!--</JS>-->


        <!--<CSS>-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="/media/css/default.css"/>
        <!--</CSS>-->
    </head>
    <body>
        <?php
        // Calls a method from a controller. Method and controller are specified in REQUEST or URI.
        $system->execute();
        ?>
    </body>
</html>
