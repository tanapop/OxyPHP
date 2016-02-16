<?php
// Includes global configs and global System class.
require_once "config.php";
require_once "constants.php";
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
        
        // Shows an alert dialog to user.(See System::setAlert method).
        if (!empty($_SESSION['sys_alerts'])):
            $i = 0;
            foreach ($_SESSION['sys_alerts'] as $alert):
                ?>
                <div class="sys-alert alert-<?php echo $alert->type; ?>">
                    <img src="/media/img/icon/alert-<?php echo $alert->type; ?>.png">
                    <span title="Close alert" class="close-alert cursor-pointer">[X]</span>
                    <p><?php echo $alert->msg; ?></p>
                </div>
                <?php
                unset($_SESSION['sys_alerts'][$i]);
                $i++;
            endforeach;
        endif;
        ?>
    </body>
</html>
