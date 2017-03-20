<?php

// This is a dump of data that will serve this page. Erase this line before deploy.
$this->helpers->insecticide->dump($dataset);
?>
<script>
    jQuery(document).ready(function ( ) {
        jQuery("#stamps-listing-contents #check-all").click(function (e) {
            var $this = jQuery(this);

            if (jQuery("#stamps-listing-contents #check-all").hasClass("active")) {
                jQuery("#stamps-listing-contents #check-all").removeClass("active");
                $this.prop("checked", false);
                jQuery("#stamps-listing-contents .check-item").prop("checked", false);
            } else {
                jQuery("#stamps-listing-contents #check-all").addClass("active");
                $this.prop("checked", true);
                jQuery("#stamps-listing-contents .check-item").prop("checked", true);
            }
        });

        jQuery("#stamps-listing-contents #del-items").click(function (e) {
            e.preventDefault();

            if (jQuery("#stamps-listing-contents .check-item:checked").length > 0) {
                if (confirm("You're up to delete multiple records. Want to proceed?")) {
                    jQuery("#stamps-listing-contents #form-check-items").submit();
                }
            } else {
                alert("There's nothing to delete.");
            }

        });
    });
</script>

<div id="stamps-listing-contents">
    <div class="row">
        <div class="col-md-12">
            <span><a href="/stamps/register/">New entry</a></span>
            <span><a id="del-items" href="#">Delete checked</a></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="form-check-items" method="post" action="/stamps/delete/">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th><input title="Check/uncheck all" id="check-all" type="checkbox" value=""></th>
                            <th>Id</th>
<th>Name</th>
<th>Author</th>
<th>Moldura?</th>
<th>Thumb_url</th>
<th>Print_w</th>
<th>Print_h</th>

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
<td><?php echo $val->name; ?></td>
<td><?php echo $val->author; ?></td>
<td><?php echo (empty($val->moldura) ? "No" : "Yes"); ?></td>
<td><?php echo $val->thumb_url; ?></td>
<td><?php echo $val->print_w; ?></td>
<td><?php echo $val->print_h; ?></td>

                            <td class="actions">
                                <a href="/stamps/register/<?php echo $val->id; ?>">Edit</a>
                                <a href="/stamps/delete/<?php echo $val->id; ?>">Delete</a>
                            </td>
                        </tr>
<?php endforeach;
else:?>
                        <tr><td align="center" colspan="9">--- NO DATA RECORDS ---</td></tr>
<?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>