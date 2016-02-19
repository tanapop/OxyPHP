<?php
include $_SERVER['DOCUMENT_ROOT'] . "accessories/krumo/class.krumo.php";
extract($_SESSION['debug']);
unset($_SESSION['debug']);
?>
<b>Uri: </b><?php echo $route; ?>
<br>
<?php $action = array_slice(explode("/", $route), 1); ?>
<b>Controller: </b><?php echo $action[0]; ?>
<br>
<b>Method: </b><?php echo $action[1]; ?>
<hr>
<h5>CURRENT SESSION: </h5>
<?php Krumo($session); ?>
<hr>
<h5>CURRENT REQUEST: </h5>
<?php Krumo($request); ?>
<hr>
<h5>DEBUG MESSAGES:</h5>
<div style="border: 1px solid;">
    <?php foreach ($messages as $i => $m): ?>
        <p><b>Message <?php echo $k = 1; ?>:</b> <?php echo $m; ?></p>
    <?php endforeach; ?>
</div>
<hr>
<h5>PRINT DATA:</h5>
<?php foreach ($print_data as $k => $p): ?>
    <p><b>[<?php echo $k; ?>]</b> <?php Krumo($p); ?></p>
<?php endforeach; ?>

