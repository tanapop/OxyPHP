<div id="debug-contents">
    <h2 style="text-align: center;">Insecticide - Debugger Tool - PHP Edition</h2>
    <!--Header with general debug data-->
    <b>URI: </b><?php echo $_SERVER['REQUEST_URI']; ?>
    <br>
    <br>
    <b>Debug called from: </b><?php echo $backtrace[1]['class']; ?><?php echo $backtrace[1]['type']; ?><?php echo $backtrace[1]['function']; ?>()
    in 
    "<?php echo $backtrace[0]['file']; ?>", line <?php echo $backtrace[0]['line']; ?>.
    <br>
    <br>
    <b>Date: </b><?php echo $time; ?>
    <br>
    <br>
    <hr>
    <h5>ARGUMENTS PASSED ON CALLER FUNCTION "<?php echo $backtrace[1]['class']; ?><?php echo $backtrace[1]['type']; ?><?php echo $backtrace[1]['function']; ?>()": </h5>
    <?php
    if (!empty($backtrace[1]['args'][0])):
        ?>
        <div class="blank-screen">
            <?php Insecticide::dump($backtrace[1]['args'][0], "Args"); ?>
        </div>
    <?php else: ?>
        <div style="border: 1px solid;">
            <p>--- No arguments passed on this function ---</p>
        </div>
    <?php endif; ?>
    <br>
    <hr>
    <!--Printing the current session data-->
    <h5>CURRENT SESSION: </h5>
    <?php if (!empty($_SESSION)): ?>
        <div class="blank-screen">
            <?php Insecticide::dump($_SESSION, "Session"); ?>
        </div>
        <?php
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
    <?php if (!empty($request)): ?>
        <div class="blank-screen">
            <?php Insecticide::dump($request); ?>
        </div>
    <?php else:
        ?>
        <div style="border: 1px solid;">
            <p>--- Request is currently empty ---</p>
        </div>
    <?php endif; ?>
    <br>
    <hr>
    <!--Printing custom debug messages-->
    <h5>CUSTOM DEBUG MESSAGES:</h5>
    <?php if (!empty($messages)): ?>
        <div class="blank-screen">
            <?php foreach ($messages as $k => $m):
                Insecticide::dump($m, $k);
                ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="border: 1px solid;">
            <p>--- No custom debug messages ---</p>
        </div>
    <?php endif; ?>
    <br>
    <hr>
    <!--Printing custom debug data-->
    <h5>CUSTOM PRINT DATA:</h5>
    <?php if (!empty($print_data)): ?>
        <div class="blank-screen">
            <?php
            foreach ($print_data as $k => $p):
                ?>
                <p><?php Insecticide::dump($p, $k); ?></p>
            <?php endforeach; ?>
        </div>
        <?php
    else:
        ?>
        <div style="border: 1px solid;">
            <p>--- No custom data to print ---</p>
        </div>
    <?php endif; ?>

    <!--Tracing execution-->
    <h5>EXECUTION HISTORY:</h5>
    <div class="blank-screen">
        It starts with:
        <?php
        $count = 1;
        for ($i = (count($backtrace) - 1); $i >= 1; $i--):
            $method = false;
            if (array_key_exists("class", $backtrace[$i])) {
                $method = true;
            }
            ?>
            <p class="history-item">
                <?php echo $count; ?> - 
                <?php echo ($method ? "Method <b>" . $backtrace[$i]['class'] . $backtrace[$i]['type'] . $backtrace[$i]['function'] : "Function <b>" . $backtrace[$i]['function']); ?>()</b> 
                <?php echo!empty($backtrace[$i]['file']) ? 'called from file"' . $backtrace[$i]['file'] . '" on line ' . $backtrace[$i]['line'] : ''; ?>.
                <br>
                <br>
                <span style="font-size:12px;">The following arguments were passed on this <?php echo $method ? 'method' : 'function'; ?>:</span>
                <br>
                <?php Insecticide::dump($backtrace[$i]['args'], "Args"); ?>
                <b>&#8675;</b>
            </p>
            <?php
            $count++;
        endfor;
        ?>
        <p class="history-item">Then, the script stopped running.</p>
    </div>
</div>