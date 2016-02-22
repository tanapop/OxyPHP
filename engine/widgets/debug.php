<?php
// Include Krumo accessory class.
System::loadClass($_SERVER['DOCUMENT_ROOT'] . "accessories/krumo/class.krumo.php", "Krumo");
// Turn debug data from session into separated variables then remove this data from session.
extract($_SESSION['debug']);
unset($_SESSION['debug']);
?>
<style>
    body{
        margin:0px;
        color:#333;
        background-color: #f0f0f0;
    }
    #debug-contents{
        font-family: arial;
        margin:15px;
    }
</style>
<div id="debug-contents">
    <div style="text-align: center;"><img src="/media/img/oxylogo.png"></div>
    <h2 style="text-align: center;">Oxy - MVC Framework - PHP Edition - Debug Screen</h2>
    <!--Header with general debug data-->
    <b>Debug called from: </b><?php echo $backtrace[1]['class']; ?>::<?php echo $backtrace[1]['function']; ?>()
    in 
    "<?php echo $backtrace[0]['file']; ?>", line <?php echo $backtrace[0]['line']; ?>.
    <br>
    <br>
    <b>URI: </b><?php echo $route; ?>
    <br>
    <br>
    <?php $action = array_slice(explode("/", $route), 1); ?>
    <b>Controller: </b><?php echo $action[0]; ?>
    <br>
    <br>
    <b>Method: </b><?php echo (empty($action[1]) ? "index" : $action[1]); ?>
    <br>
    <br>
    <?php
    $args = $backtrace[1]['args'][0];
    if (!empty($args)):
        ?>
        <b>Arguments: </b>
        <?php Krumo($args); ?>
        <br>
    <?php endif; ?>
    <hr>
    <!--Printing the current session data-->
    <h5>CURRENT SESSION: </h5>
    <?php
    if (!empty($_SESSION)):
        Krumo($_SESSION);
    else:
        ?>
        <div style="border: 1px solid;">
            <p>--- Session is currently empty ---</p>
        </div>
    <?php endif; ?>
    <br>
    <hr>
    <!--Printing the current request data-->
    <h5>CURRENT REQUEST: </h5>
    <?php
    if (!empty($request)):
        Krumo($request);
    else:
        ?>
        <div style="border: 1px solid;">
            <p>--- Request is currently empty ---</p>
        </div>
    <?php endif; ?>
    <br>
    <hr>
    <!--Printing custom debug messages-->
    <h5>DEBUG MESSAGES:</h5>
    <div style="border: 1px solid;">
        <?php
        if (!empty($messages)):
            foreach ($messages as $i => $m):
                ?>
                <p><b>Message <?php echo $k = 1; ?>:</b> <?php echo $m; ?></p>
                <?php
            endforeach;
        else:
            ?>
            <p>--- No custom debug messages ---</p>
        <?php endif; ?>
    </div>
    <br>
    <hr>
    <!--Printing custom debug data-->
    <h5>PRINT DATA:</h5>
    <?php
    if (!empty($print_data)):
        foreach ($print_data as $k => $p):
            ?>
            <p><b>[<?php echo $k; ?>]</b> <?php Krumo($p); ?></p>
            <?php
        endforeach;
    else:
        ?>
        <div style="border: 1px solid;">
            <p>--- No custom data to print ---</p>
        </div>
    <?php endif; ?>
</div>