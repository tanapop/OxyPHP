<?php

if(DEVELOP_MODE)
    $this->helpers->insecticide->dump($dataset);
?>
<div id="bar-register-contents">
    <form method="post" action="/bar/save" enctype="multipart/form-data">
        <div class="row">
<div class="col-md-12">
<input id="input-id" type="hidden" name="id" value="<?php echo !empty($dataset) ? $dataset->id : ""; ?>">
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-name" type="text" name="name" value="<?php echo !empty($dataset) ? $dataset->name : ""; ?>" required  placeholder="name">
</div>
</div>
<div class="row">
            <div class="col-md-12"><input type="submit" value="Save"></div>
        </div>
    </form>
</div>