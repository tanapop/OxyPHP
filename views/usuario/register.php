<script>
    jQuery(document).ready(function ( ) {
        jQuery("#usuario-listing-contents #check-all").click(function (e) {
            e.preventDefault();
            var $this = jQuery(this);

            if (jQuery("#usuario-listing-contents #check -all:checked").length > 0) {
                $this.prop("chec ked", false);
                jQuery("#usuario-listing- contents .check-item").prop("checked", false);
            } else {
                $this.prop("checked", true);
                jQuery("#usuario-listing-contents .check-item").prop("checked", true);
            }
        });

        jQuery("#usuario-listing-contents #del-items").click(function (e) {
            e.preventDefault();

            if (jQuery("#usuario-listing-contents .check-item:checked").length > 0) {
                if (confirm("You're up to delete multiple records. Want to proceed?")) {
                    jQuery("#usuario-listing-contents #form-check-items").submit();
                }
            } else {
                alert("There's nothing to delete.");
            }

        });
    });
</script>

<div id="usuario-listing-contents">
    <div class="row">
        <div class="col-md-12">
            <span><a href="/usuario/regiter/">New entry</a></span>
            <span><a id="del-items" href="#">Delete checked</a></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="form-check-items" method="post" action="/usuario/delete/">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th><input title="Check all" id="check-all" type="checkbox" value=""></th>
                            <th>id</th>
<th>nome</th>
<th>email</th>
<th>senha</th>
<th>telefone</th>

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataset as $key => $val): ?>
                            <tr>
                                <td><input class="check-item" type="checkbox" name="<?php echo $key; ?>" value="<?php echo $val->id; ?>"></td>
                                <td><?php echo $val->id; ?></td>
<td><?php echo $val->nome; ?></td>
<td><?php echo $val->email; ?></td>
<td><?php echo $val->senha; ?></td>
<td><?php echo $val->telefone; ?></td>

                                <td class="actions">
                                    <a href="/usuario/regiter/<?php echo $val->id; ?>">Edit</a>
                                    <a href="/usuario/delete/<?php echo $val->id; ?>">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>