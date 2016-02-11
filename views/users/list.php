<script>
    jQuery(document).ready(function(){
        jQuery("#user-list").DataTable();
        
        jQuery(".del-item").click(function(e){
            e.preventDefault();
            
            var _this = jQuery(this);
            
            if(confirm("Tem certeza que deseja excluir o registro?")){
                window.location = _this.attr("href");
            }
        });
    });
</script>
<h3 class="page-title">Usuários</h3>
<table id="user-list">
    <thead>
    <th>Nome</th>
    <th>Email</th>
    <th>Telefone</th>
    <th>Ações</th>
</thead>
<tbody>
    <?php foreach ($list as $item) : ?>
        <tr>
            <td><?php echo $item->nome; ?></td>
            <td><?php echo $item->email; ?></td>
            <td><?php echo (!empty($item->telefone) ? $item->telefone : ""); ?></td>
            <td>
                <a href="?c=usuarios&a=register&id=<?php echo $item->id; ?>">Editar</a>
                <a class="del-item" href="?c=usuarios&a=delete&id=<?php echo $item->id; ?>">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>
<a href="/?c=usuarios&a=register" title="Novo Registro"><img src="/media/img/icon/bt-add.png"></a>