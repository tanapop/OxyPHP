<form method="post" action="_CONTROLLER_NAME_/delete/">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th><input title="Check all" id="check-all" type="checkbox" value=""></th>
                _TABLE_FIELDS_HEADERS_
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dataset as $key => $val): ?>
                <tr>
                    <td><input type="checkbox" name="args[]" value="<?php echo $val->id; ?>"></td>
                    _TABLE_FIELDS_VALUES_
                    <td class="actions">
                        <a href="/_CONTROLLER_NAME_/regiter/<?php echo $val->id; ?>">Edit</a>
                        <a href="/_CONTROLLER_NAME_/delete/<?php echo $val->id; ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>