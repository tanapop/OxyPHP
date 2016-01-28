<script>
    jQuery(document).ready(function(){
        jQuery("#client-list").DataTable();
        
        jQuery(".del-item").click(function(e){
            e.preventDefault();
            
            var _this = jQuery(this);
            
            if(confirm("Tem certeza que deseja excluir o registro do cliente?")){
                window.location = _this.attr("href");
            }
        });
    });
</script>
<h3 class="page-title">Clientes</h3>
<table id="client-list">
    <thead>
    <th>Nome</th>
    <th>Ações</th>
</thead>
<tbody>
    <?php foreach ($list as $item) : ?>
        <tr>
            <td><?php echo $item->nome; ?></td>
            <td>
                <a href="?c=cliente&a=register&id=<?php echo $item->id; ?>">Editar</a>
                <a class="del-item" href="?c=cliente&a=delete&id=<?php echo $item->id; ?>">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>
<a href="/?c=cliente&a=register" title="Novo Registro"><img src="/media/img/icon/bt-add.png"></a>