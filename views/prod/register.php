<?php
// This is a dump of data that will serve this page. Erase this line before deploy.
$this->helpers->insecticide->dump($dataset);
?>
<div id="prod-register-contents">
    <form method="post" action="/prod/save" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-12">
                <input id="input-id" type="hidden" name="id" value="<?php echo!empty($dataset) ? $dataset->id : ""; ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input id="input-ref_id" type="text" name="ref_id" value="<?php echo!empty($dataset) ? $dataset->ref_id : ""; ?>" required  placeholder="ref_id">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input id="input-name" type="text" name="name" value="<?php echo!empty($dataset) ? $dataset->name : ""; ?>" required  placeholder="name">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input id="input-faces" type="text" name="faces" value="<?php echo!empty($dataset) ? $dataset->faces : ""; ?>"  placeholder="faces">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input id="input-register_date" type="text" name="register_date" value="<?php echo!empty($dataset) ? $dataset->register_date : ""; ?>" required  placeholder="register_date">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12"><input type="submit" value="Save"></div>
        </div>
    </form>
</div>