<div id="pedido-register-contents">
    <form method="post" action="/pedido/save">
        <div class="row"><div class="col-md-12"><input  type="hidden" name="id" placeholder="id" value="<?php echo $dataset->id; ?>"></div></div><div class="row"><div class="col-md-12"><input required type="number" name="id_cliente" placeholder="id_cliente" value="<?php echo $dataset->id_cliente; ?>"></div></div><div class="row"><div class="col-md-12"><input required type="number" name="id_marmita" placeholder="id_marmita" value="<?php echo $dataset->id_marmita; ?>"></div></div><div class="row"><div class="col-md-12"><input required type="number" name="qtde" placeholder="qtde" value="<?php echo $dataset->qtde; ?>"></div></div><div class="row"><div class="col-md-12"><input required type="text" name="data" placeholder="data" value="<?php echo $dataset->data; ?>"></div></div><div class="row"><div class="col-md-12"><input required type="number" name="id_usuario" placeholder="id_usuario" value="<?php echo $dataset->id_usuario; ?>"></div></div><div class="row"><div class="col-md-12"><input required type="" name="custo" placeholder="custo" value="<?php echo $dataset->custo; ?>"></div></div>
    <div class="row">
        <div class="col-md-12"><input type="submit" value="Save"></div>
    </div>
    </form>
</div>