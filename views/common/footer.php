<?php
if (!empty($_SESSION['sys_alerts'])):
    $i = 0;
    foreach ($_SESSION['sys_alerts'] as $alert):
        ?>
        <div class="sys-alert alert-<?php echo $alert->type; ?>">
            <img src="/media/img/icon/alert-<?php echo $alert->type; ?>.png">
            <span title="Fechar alerta" class="close-alert cursor-pointer">[X]</span>
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

