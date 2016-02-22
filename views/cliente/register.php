<div id="cliente-register-contents">
<form method="post" action="/cliente/save" enctype="multipart/form-data">
<div class="row">
<div class="col-md-12">
<input id="input-id"  type="hidden" name="id" value="<?php echo !empty($dataset) ? $dataset->id : ""; ?>">
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-nome"  type="text" name="nome" placeholder="nome" value="<?php echo !empty($dataset) ? $dataset->nome : ""; ?>">
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-file" required type="file" name="file" >
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-float" required type="number" name="float" placeholder="float" value="<?php echo !empty($dataset) ? $dataset->float : ""; ?>">
</div>
</div>
<div class="row">
<div class="col-md-12">
<input id="input-boolean" required type="checkbox" name="boolean" value="<?php echo !empty($dataset) ? $dataset->boolean : 0; ?>"> <label for="input-boolean">boolean</label>
</div>
</div>
<div class="row">
<div class="col-md-12"><input type="submit" value="Save"></div>
</div>
</form>
</div>