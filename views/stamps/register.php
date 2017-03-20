<?php
// This is a dump of data that will serve this page. Erase this line before deploy.
$this->helpers->insecticide->dump($dataset);
?>
<div id="stamps-register-contents">
    <form method="post" action="/stamps/save" enctype="multipart/form-data">
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
<div class="col-md-12">
<input id="input-author" type="text" name="author" value="<?php echo !empty($dataset) ? $dataset->author : ""; ?>" required  placeholder="author">
</div>
</div>
<div class="row">
<div class="col-md-12">
<label>Moldura?</label>
<input class="input-moldura" type="radio" name="moldura" value="0" <?php echo (empty($dataset->moldura) ? "checked" : ""); ?>> No&nbsp;&nbsp;
<input class="input-moldura" type="radio" name="moldura" value="1" <?php echo (!empty($dataset->moldura) ? "checked" : ""); ?>> Yes
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-thumb_url" type="text" name="thumb_url" value="<?php echo !empty($dataset) ? $dataset->thumb_url : ""; ?>" required  placeholder="thumb_url">
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-print_w" type="number" name="print_w" value="<?php echo !empty($dataset) ? $dataset->print_w : ""; ?>" required  placeholder="print_w">
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-print_h" type="number" name="print_h" value="<?php echo !empty($dataset) ? $dataset->print_h : ""; ?>" required  placeholder="print_h">
</div>
</div>
<div class="row">
            <div class="col-md-12"><input type="submit" value="Save"></div>
        </div>
    </form>
</div>