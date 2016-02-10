<h3 class="page-title">Dados do ingrediente</h3>
<form class="form-register" action="/?c=ingredientes&a=salvar" method="post">
    <input type="hidden" name="id" value="<?php echo (!empty($data->id) ? $data->id : ""); ?>">
    <div><input name="nome" type="text" placeholder="Nome" value="<?php echo (!empty($data->nome) ? $data->nome : ""); ?>"></div>
    <div><input name="custo" type="number" step="0.01" placeholder="Custo" value="<?php echo (!empty($data->custo) ? $data->custo : ""); ?>"></div>
    <div><input type="submit" value="Salvar"></div>
</form>