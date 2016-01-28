<script>
    jQuery(document).ready(function(){
        jQuery("#input-submit").click(function(e){
            e.preventDefault();
            
            var input_senha = jQuery("#input-senha");
            var input_c_senha = jQuery("#input-confirm-senha");
            
            var attr = input_senha.attr('required');
            
            if (typeof attr !== typeof undefined && attr !== false) {
                if(input_senha.val() === ""){
                    alert('O campo "Nova senha" é obrigatório para criação de novos usuários.');
                    return false;
                }
            }
            
            if(input_senha.val() == input_c_senha.val()){
                jQuery(this).parents("form").submit();
            } else{
                alert('Os valores nos campos "Nova senha" e "Confirme nova senha" não conferem.');
                return false;
            }
        });
    });
</script>
<h3 class="page-title">Dados do usuário</h3>
<form class="form-register" action="/?c=usuarios&a=salvar" method="post">
    <input type="hidden" name="id" value="<?php echo (!empty($data->id) ? $data->id : ""); ?>">
    <div><input required name="nome" type="text" placeholder="Nome" value="<?php echo (!empty($data->nome) ? $data->nome : ""); ?>"></div>
    <div><input required name="email" type="text"  placeholder="Email" value="<?php echo (!empty($data->email) ? $data->email : ""); ?>"></div>
    <div><input id="input-senha" name="senha" type="password" <?php echo (empty($data->id) ? "required" : ""); ?>  placeholder="Nova senha<?php echo (!empty($data->id) ? "(Deixe em branco para manter a atual)": ""); ?>" value=""></div>
    <div><input id="input-confirm-senha" type="password" <?php echo (empty($data->id) ? "required" : ""); ?>  placeholder="Confirme nova senha" value=""></div>
    <div><input name="telefone" type="text" placeholder="Telefone" value="<?php echo (!empty($data->telefone) ? $data->telefone : ""); ?>"></div>
    <div><input id="input-submit" type="submit" value="Salvar"></div>
</form>