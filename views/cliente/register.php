<div id="cliente-register-contents">
<form method="post" action="/cliente/save" enctype="multipart/form-data">
<div class="row">
<div class="col-md-12">
<input id="input-id" type="hidden" name="id" value="<?php echo !empty($dataset) ? $dataset->id : ""; ?>">
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-nome" type="text" name="nome" value="<?php echo !empty($dataset) ? $dataset->nome : ""; ?>"  placeholder="nome">
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-file" type="file" name="file" ></div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-floating" type="number" name="floating" value="<?php echo !empty($dataset) ? $dataset->floating : ""; ?>" required  placeholder="floating">
</div>
</div>
<div class="row">
<div class="col-md-12">
<label>Boolean?</label>
<input class="input-boolean" type="radio" name="boolean" value="0" <?php echo (empty($dataset->boolean) ? "checked" : ""); ?>> No&nbsp;&nbsp;
<input class="input-boolean" type="radio" name="boolean" value="1" <?php echo (!empty($dataset->boolean) ? "checked" : ""); ?>> Yes
</div>
</div>
<div class="row">
<div class="col-md-12"><input type="submit" value="Save"></div>
</div>
</form>
</div>