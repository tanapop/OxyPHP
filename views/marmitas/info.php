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
<div class="white-popup-block">
    <h3 class="page-title">Info Marmita</h3>

    <p><b>Cliente: </b> <?php echo $cliente; ?></p>
    <p><b>Dia da semana: </b> <?php echo $dias[$m_data->dia]; ?></p>

    <p><b>Ingredientes: </b></p>
    <ul>
        <?php foreach ($ingrs as $i): ?>
            <li><?php echo $i->nome; ?></li>
        <?php endforeach; ?>
    </ul>
    
    <p><b>Custo p/ unidade: </b>R$ <?php echo $m_data->custo; ?></p>
</div>