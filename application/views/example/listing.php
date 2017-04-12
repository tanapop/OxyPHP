<?php

if(DEVELOP_MODE)
    $this->helpers->insecticide->dump($dataset);
?>
<script>
    jQuery(document).ready(function ( ) {
        jQuery("#example-listing-contents #check-all").click(function (e) {
            var $this = jQuery(this);

            if (jQuery("#example-listing-contents #check-all").hasClass("active")) {
                jQuery("#example-listing-contents #check-all").removeClass("active");
                $this.prop("checked", false);
                jQuery("#example-listing-contents .check-item").prop("checked", false);
            } else {
                jQuery("#example-listing-contents #check-all").addClass("active");
                $this.prop("checked", true);
                jQuery("#example-listing-contents .check-item").prop("checked", true);
            }
        });

        jQuery("#example-listing-contents #del-items").click(function (e) {
            e.preventDefault();

            if (jQuery("#example-listing-contents .check-item:checked").length > 0) {
                if (confirm("You're up to delete multiple records. Want to proceed?")) {
                    jQuery("#example-listing-contents #form-check-items").submit();
                }
            } else {
                alert("There's nothing to delete.");
            }

        });
    });
</script>

<div id="example-listing-contents">
    <div class="row">
        <div class="col-md-12">
            <span><a href="/example/register/">New entry</a></span>
            <span><a id="del-items" href="#">Delete checked</a></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="form-check-items" method="post" action="/example/delete/">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th><input title="Check/uncheck all" id="check-all" type="checkbox" value=""></th>
                            <th>Id</th>
<th>Name</th>

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

                            <td class="actions">
                                <a href="/example/register/<?php echo $val->id; ?>">Edit</a>
                                <a href="/example/delete/<?php echo $val->id; ?>">Delete</a>
                            </td>
                        </tr>
<?php endforeach;
else:?>
                        <tr><td align="center" colspan="4">--- NO DATA RECORDS ---</td></tr>
<?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>