<script>
    jQuery(document).ready(function ( ) {
        jQuery("#_CONTROLLER_NAME_-listin g-c ontents #check-all").click(function (e) {
            e.preventDefault();
            var $this = jQuery(this);

            if (jQuery("#_C ONTROLLER_NAME_-listing-contents #check -all:checked").length > 0) {
                $this.prop("chec ked", false);
                jQuery("#_CONTROLLER_NAME_-listing- contents .check-item").prop("checked", false);
            } else {
                $this.prop("checked", true);
                jQuery("#_CONTR OLL ER_NAME_-listing-contents .check-item").prop("checked", true);
            }
        });

        jQuery("#_CONTROLLER_NAME_-listing-contents #del-items").click(function (e) {
            e.preventDefault();

            if (jQuery("#_CONTROLLER_NAME_-listing-contents .check-item:checked").length > 0) {
                if (confirm("You're up to delete multiple records. Want to proceed?")) {
                    jQuery("#_CONTROLLER_NAME_-listing-contents #form-check-items").submit();
                }
            } else {
                alert("There's nothing to delete.");
            }

        });
    });
</script>

<div id="_CONTROLLER_NAME_-listing-contents">
    <div class="row">
        <div class="col-md-12">
            <span><a href="/_CONTROLLER_NAME_/regiter/">New entry</a></span>
            <span><a id="del-items" href="#">Delete checked</a></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="form-check-items" method="post" action="/_CONTROLLER_NAME_/delete/">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th><input title="Check all" id="check-all" type="checkbox" value=""></th>
                            _TABLE_FIELDS_HEADERS_
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataset as $key => $val): ?>
                            <tr>
                                <td><input class="check-item" type="checkbox" name="<?php echo $key; ?>" value="<?php echo $val->id; ?>"></td>
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
        </div>
    </div>
</div>