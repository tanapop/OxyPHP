<script>
    jQuery(document).ready(function () {
        jQuery(".check-ingr").change(function () {
            var c_input = document.getElementById("custoTotal");

            var custo = parseFloat(c_input.value);
            var sum = parseFloat(jQuery(this).attr("data-custo"));

            if (jQuery(this).is(":checked")) {
                custo += sum;
            } else {
                custo -= sum;
            }

            c_input.value = custo;
            jQuery("#show-custo").html(custo);
        });
    });
</script>
<style>
    #cheklist{
        border-radius:5px;
        box-shadow: 0px 0px 3px inset;
        overflow-y: scroll;
        height:250px;
    }
    .white-popup-block>h3{
        text-align:center;
    }
</style>
<div class="white-popup-block">
    <h3 class="page-title">Criar Marmita</h3>
    <br>
    <span><b>Cliente: </b><?php echo $c_nome; ?></span>
    <br>
    <form id="reg-marmita" action="/?c=marmitas&a=salvar" method="post">
        <input type="hidden" name="id_cliente" value="<?php echo $c_id; ?>">
        <input id="custoTotal" type="hidden" name="custo" value="0">
        <b>Dia da semana: </b><select name="dia">
            <option value="1">Segunda-feira</option>
            <option value="2">Terça-feira</option>
            <option value="3">Quarta-feira</option>
            <option value="4">Quinta-feira</option>
            <option value="5">Sexta-feira</option>
            <option value="6">Sábado</option>
        </select>
        <br>
        <br>
        <b>Ingredientes: </b>
        <br>
        <div id="cheklist">
            <?php foreach ($ingr as $i): ?>
                <input class="check-ingr" data-custo="<?php echo $i->custo; ?>" type="checkbox" name="ingredients[]" value="<?php echo $i->id; ?>"> <?php echo $i->nome; ?>
                <br>
            <?php endforeach; ?>
        </div>
        <br>
        <input type="submit" value="Salvar">
    </form>
    <div><b>Custo: </b>R$ <span id="show-custo">0.00</span></div>
</div>