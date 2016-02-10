<script>
    jQuery(document).ready(function(){
        jQuery("#ingredient-list").DataTable();
        
        jQuery(".del-item").click(function(e){
            e.preventDefault();
            
            var _this = jQuery(this);
            
            if(confirm("Tem certeza que deseja excluir o registro?")){
                window.location = _this.attr("href");
            }
        });
    });
</script>
<h3 class="page-title">Ingredientes</h3>
<table id="ingredient-list">
    <thead>
    <th>Nome</th>
    <th>Custo</th>
    <th>Ações</th>
</thead>
<tbody>
    <?php foreach ($list as $item) : ?>
        <tr>
            <td><?php echo $item->nome; ?></td>
            <td><?php echo $item->custo; ?></td>
            <td>
                <a href="?c=ingredientes&a=register&id=<?php echo $item->id; ?>">Editar</a>
                <a class="del-item" href="?c=ingredientes&a=delete&id=<?php echo $item->id; ?>">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>
<a href="/?c=ingredientes&a=register" title="Novo Registro"><img src="/media/img/icon/bt-add.png"></a>