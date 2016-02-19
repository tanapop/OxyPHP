<div id="cliente-register-contents">
    <form method="post" action="/cliente/save">
        <div class="row"><div class="col-md-12"><input  type="hidden" name="id" placeholder="id" value="<?php echo $dataset->id; ?>"></div></div><div class="row"><div class="col-md-12"><input  type="text" name="nome" placeholder="nome" value="<?php echo $dataset->nome; ?>"></div></div>
    <div class="row">
        <div class="col-md-12"><input type="submit" value="Save"></div>
    </div>
    </form>
</div>