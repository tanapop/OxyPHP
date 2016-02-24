<script>
    jQuery(document).ready(function ( ) {
        jQuery("#cliente-listing-contents #check-all").click(function (e) {
            var $this = jQuery(this);

            if (jQuery("#cliente-listing-contents #check-all").hasClass("active")) {
                jQuery("#cliente-listing-contents #check-all").removeClass("active");
                $this.prop("checked", false);
                jQuery("#cliente-listing-contents .check-item").prop("checked", false);
            } else {
                jQuery("#cliente-listing-contents #check-all").addClass("active");
                $this.prop("checked", true);
                jQuery("#cliente-listing-contents .check-item").prop("checked", true);
            }
        });

        jQuery("#cliente-listing-contents #del-items").click(function (e) {
            e.preventDefault();

            if (jQuery("#cliente-listing-contents .check-item:checked").length > 0) {
                if (confirm("You're up to delete multiple records. Want to proceed?")) {
                    jQuery("#cliente-listing-contents #form-check-items").submit();
                }
            } else {
                alert("There's nothing to delete.");
            }

        });
    });
</script>

<div id="cliente-listing-contents">
<div class="row">
<div class="col-md-12">
<span><a href="/cliente/register/">New entry</a></span>
<span><a id="del-items" href="#">Delete checked</a></span>
</div>
</div>
<div class="row">
<div class="col-md-12">
<form id="form-check-items" method="post" action="/cliente/delete/">
<table class="table table-hover table-striped">
<thead>
<tr>
<th><input title="Check/uncheck all" id="check-all" type="checkbox" value=""></th>
<th>Id</th>
<th>Nome</th>
<th>File</th>
<th>Floating</th>
<th>Boolean?</th>

<th>Actions</th>
</tr>
</thead>
<tbody>
<?php
if(!empty($dataset)):
foreach ($dataset as $key => $val): ?>
<tr>
<td><input class="check-item" type="checkbox" name="<?php echo $key; ?>" value="<?php echo $val->id; ?>"></td>
<td><?php echo $val->id; ?></td>
<td><?php echo $val->nome; ?></td>
<td><?php echo $val->file; ?></td>
<td><?php echo $val->floating; ?></td>
<td><?php echo (empty($val->boolean) ? "No" : "Yes"); ?></td>

<td class="actions">
<a href="/cliente/register/<?php echo $val->id; ?>">Edit</a>
<a href="/cliente/delete/<?php echo $val->id; ?>">Delete</a>
</td>
</tr>
<?php endforeach;
else:?>
<tr><td align="center" colspan="7">--- NO DATA RECORDS ---</td></tr>
<?php endif; ?>
</tbody>
</table>
</form>
</div>
</div>
</div>