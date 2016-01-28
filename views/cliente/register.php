<script>
    jQuery(document).ready(function(){
        
        jQuery(".edit-action,.info-action").magnificPopup({
            type: 'ajax',
            alignTop: true,
            overflowY: 'scroll',
            closeOnBgClick: false,
            callbacks: {
                close: function () {
                    location.reload();
                }
            }
        });
    });
</script>
<style>
    #marm-c-list{
        width:100%;
    }
    #marm-c-list td{
        border:1px solid;
    }
</style>
<h3 class="page-title">Dados do cliente</h3>
<form class="form-register" action="/?c=cliente&a=salvar" method="post">
    <input type="hidden" name="id" value="<?php echo (!empty($data->id) ? $data->id : ""); ?>">
    <div><input name="nome" type="text" placeholder="Nome" value="<?php echo (!empty($data->nome) ? $data->nome : ""); ?>"></div>
    <div><input type="submit" value="Salvar"></div>
</form>
<hr>
<h3>Marmitas da semana: </h3>
<table id="marm-c-list">
    <thead>
        <tr>
            <th>Dia da Semana</th>
            <th>Custo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $custo_semana = 0;
        $dias = array(
            "",
            "Segunda-feira",
            "Terça-feira",
            "Quarta-feira",
            "Quinta-feira",
            "Sexta-feira",
            "Sábado"
        );

        foreach ($marmitas as $m):
            $custo_semana += $m->custo;
            ?>
            <tr>
                <td><?php echo $dias[$m->dia]; ?></td>
                <td>R$ <?php echo $m->custo; ?> p/ unidade</td>
                <td>
                    <a class="info-action" href="/?c=marmitas&a=info&id=<?php echo $m->id; ?>&cliente=<?php echo $data->nome;?>">Informações</a>
                    <a class="del-action" href="/?c=marmitas&a=delete&id=<?php echo $m->id; ?>&id_cliente=<?php echo $data->id; ?>">Excluir</a>
                </td>
            </tr>
<?php endforeach; ?>
    </tbody>
</table>
<a title="Adicionar marmita" class="edit-action" href="/?c=marmitas&a=register&c_id=<?php echo $data->id; ?>&cliente=<?php echo $data->nome; ?>"><img src="/media/img/icon/bt-add.png"></a>
<hr>
<div><b>Custo total p/ semana: </b>R$ <?php echo $custo_semana; ?></div>