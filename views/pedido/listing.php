<script>
    jQuery(document).ready(function ( ) {
        jQuery("#pedido-listing-contents #check-all").click(function (e) {
            e.preventDefault();
            var $this = jQuery(this);

            if (jQuery("#pedido-listing-contents #check -all:checked").length > 0) {
                $this.prop("chec ked", false);
                jQuery("#pedido-listing- contents .check-item").prop("checked", false);
            } else {
                $this.prop("checked", true);
                jQuery("#pedido-listing-contents .check-item").prop("checked", true);
            }
        });

        jQuery("#pedido-listing-contents #del-items").click(function (e) {
            e.preventDefault();

            if (jQuery("#pedido-listing-contents .check-item:checked").length > 0) {
                if (confirm("You're up to delete multiple records. Want to proceed?")) {
                    jQuery("#pedido-listing-contents #form-check-items").submit();
                }
            } else {
                alert("There's nothing to delete.");
            }

        });
    });
</script>

<div id="pedido-listing-contents">
    <div class="row">
        <div class="col-md-12">
            <span><a href="/pedido/regiter/">New entry</a></span>
            <span><a id="del-items" href="#">Delete checked</a></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="form-check-items" method="post" action="/pedido/delete/">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th><input title="Check all" id="check-all" type="checkbox" value=""></th>
                            <th>id</th>
<th>id_cliente</th>
<th>id_marmita</th>
<th>qtde</th>
<th>data</th>
<th>id_usuario</th>
<th>custo</th>

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataset as $key => $val): ?>
                            <tr>
                                <td><input class="check-item" type="checkbox" name="<?php echo $key; ?>" value="<?php echo $val->id; ?>"></td>
                                <td><?php echo $val->id; ?></td>
<td><?php echo $val->id_cliente; ?></td>
<td><?php echo $val->id_marmita; ?></td>
<td><?php echo $val->qtde; ?></td>
<td><?php echo $val->data; ?></td>
<td><?php echo $val->id_usuario; ?></td>
<td><?php echo $val->custo; ?></td>

                                <td class="actions">
                                    <a href="/pedido/regiter/<?php echo $val->id; ?>">Edit</a>
                                    <a href="/pedido/delete/<?php echo $val->id; ?>">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>