<?php
$dias = array(
    "",
    "Segunda-feira",
    "Terça-feira",
    "Quarta-feira",
    "Quinta-feira",
    "Sexta-feira",
    "Sábado"
);
?>
<script>
    jQuery(document).ready(function () {
        jQuery(".qtde").change(function () {
            var input = jQuery(this);
            var span = jQuery("span[data-cliente='" + input.attr("data-cliente") + "']");
            var hidden = jQuery("#custo-cliente-" + input.attr("data-cliente"));

            var custo = parseFloat(input.attr("data-custo"));
            var total = parseFloat(span.html());
            var qtde = parseInt(input.val());

            total = Math.round(100 * (custo * qtde)) / 100;

            span.html(total);
            hidden.val(total);
        });

        jQuery(".info-action").magnificPopup({
            type: 'ajax',
            alignTop: true,
            overflowY: 'scroll',
            closeOnBgClick: true,
        });

        jQuery("#bt-confirma").click(function () {
            jQuery("#form-pedidos").submit();
        });
    });
</script>
<h3 class="page-title">Registar Pedidos do dia</h3>

<p><b>Dia da semana: </b> <?php echo $dias[$dia]; ?></p>
<hr>
<p><b>Clientes do dia: </b></p>
<form action="/?c=pedidos&a=salvar" method="post">
    <table class="dataTable" id="clientes-dia">
        <thead>
            <tr>
                <th>Nome do Cliente</th>
                <th>Informações marmita</th>
                <th>Qtde. Pedido</th>
                <th>Custo total</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=0; foreach ($clients as $c): ?>
                <tr>
                    <td><?php echo $c->cliente; ?></td>
                    <td align="center"><a class="info-action" href="/?c=marmitas&a=info&id=<?php echo $c->id; ?>&cliente=<?php echo $c->cliente; ?>" title="Ver informações da marmita.">Ver info</a></td>
                    <td align="center">
                        <input type="hidden" name="pedidos[<?php echo $i; ?>][id_cliente]" value="<?php echo $c->id_cliente; ?>">
                        <input type="hidden" name="pedidos[<?php echo $i; ?>][id_marmita]" value="<?php echo $c->id; ?>">
                        <input type="hidden" name="pedidos[<?php echo $i; ?>][data]" value="<?php echo mktime(); ?>">
                        <input type="hidden" name="pedidos[<?php echo $i; ?>][id_usuario]" value="<?php echo $_SESSION['user']->id; ?>">
                        <input id="custo-cliente-<?php echo $c->id_cliente; ?>" type="hidden" name="pedidos[<?php echo $i; ?>][custo]" value="">
                        <input name="pedidos[<?php echo $i; ?>][qtde]" class="qtde" type="number" data-custo="<?php echo $c->custo; ?>" data-cliente="<?php echo $c->id_cliente; ?>">
                    </td>
                    <td>R$ <span data-cliente="<?php echo $c->id_cliente; ?>">0.00</span></td>
                </tr>
            <?php $i++; endforeach; ?>
        </tbody>
    </table>
    <input type="submit" value="Fechar pedidos">
</form>
<hr>