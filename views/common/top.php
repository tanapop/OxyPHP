<!DOCTYPE html>
<html>
    <head>

        <!--<META>-->
        <title>Marmitão da Felicidade</title>
        <!--</META>-->


        <!--<JS>-->
        <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script src="http://cdn.datatables.net/1.10.5/js/jquery.dataTables.min.js"></script>
        <script src="/media/js/main.js"></script>
        <script src="/media/js/jquery.magnific-popup.js"></script>
        <!--</JS>-->


        <!--<CSS>-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/s/dt/jq-2.1.4,dt-1.10.10/datatables.min.css"/>
        <link rel="stylesheet" type="text/css" href="/media/css/default.css"/>
        <link rel="stylesheet" type="text/css" href="/media/css/magnific-popup.css"/>
        <!--</CSS>-->
    </head>
    <body>
        <?php global $system; if($system->auth(null, false)): ?>
        <div id="nav">
            <div><a href="/">Home</a></div>
            <div><a href="/?c=pedidos&a=register">Pedidos do dia</a></div>
            <div><a href="/?c=cliente&a=lista">Clientes</a></div>
            <div><a href="/?c=ingredientes&a=lista&args=true">Ingredientes</a></div>
            <div><a href="/?c=usuarios&a=lista">Usuários</a></div>
            <div class="submenu">
                <a href="#">Relatórios</a>
                <ul style="display:none;">
                    <li><a href="/?c=relatorios&a=cozinha">Cozinha</a></li>
                    <li><a href="/?c=relatorios&a=entrega">Entrega</a></li>
                    <li><a href="/?c=relatorios&a=custos">Custos Semanais</a></li>
                </ul>
            </div>
        </div>
        <?php endif; ?>