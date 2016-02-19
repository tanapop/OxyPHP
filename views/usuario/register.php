<div id="usuario-register-contents">
    <form method="post" action="/usuario/save">
        <div class="row"><div class="col-md-12"><input  type="hidden" name="id" placeholder="id" value="<?php echo $dataset->id; ?>"></div></div><div class="row"><div class="col-md-12"><input required type="text" name="nome" placeholder="nome" value="<?php echo $dataset->nome; ?>"></div></div><div class="row"><div class="col-md-12"><input required type="text" name="email" placeholder="email" value="<?php echo $dataset->email; ?>"></div></div><div class="row"><div class="col-md-12"><input required type="text" name="senha" placeholder="senha" value="<?php echo $dataset->senha; ?>"></div></div><div class="row"><div class="col-md-12"><input  type="text" name="telefone" placeholder="telefone" value="<?php echo $dataset->telefone; ?>"></div></div>
    <div class="row">
        <div class="col-md-12"><input type="submit" value="Save"></div>
    </div>
    </form>
</div>