<div class="ins-container">
    <ul class="ins-dump">
        <?php if (!is_array($var) && $vartype !== 'object'): ?>
            <li><b>&#8627;</b> 
                <span class="ins-varname">
                    <?php echo '[' . $name . ']'; ?>
                </span> <?php echo '(' . $vartype . ' - length:' . strlen((string) $var) . ')' ?> = 
                <b><?php echo $var; ?></b>
            </li>
        <?php else: $length = count((array) $var); ?>
            <li class="ins-dropdown">
                <b>&#8600;</b> 
                <span class="ins-varname"><?php echo '[' . $name . ']'; ?></span> 
                <?php echo '(' . $vartype . ' - length:' . $length . ')'; ?></li>
            <li class="ins-hidden">
                <ul>
                    <?php if ($vartype == "object"): ?>
                        <li class='ins-obs'>
                            *This is an object. It may contain inaccessible(private or protected) properties that will not be shown in this dump list.
                        </li>
                    <?php endif; ?>
                    <?php if (empty($length)): ?>
                        <li class='ins-obs'>
                            *This list is empty. No items to dump.
                        </li>
                    <?php endif; ?>
                    <?php foreach ($var as $k => $v): ?>
                        <li>
                            <?php echo $this->dump($v, $k, true); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</div>
