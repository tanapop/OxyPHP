<?php

class ModelIngredientes {

    private $table;

    public function __construct() {
        require_once $_SERVER["DOCUMENT_ROOT"]."/engine/class.dbcom.php";
        $this->table = "ingrediente";
    }
    
    public function _list(){
        $sql = "SELECT * FROM ".$this->table." ORDER BY id ASC";
        
        return Dbcom::query($sql);
    }
    
    public function _get($id){
        $sql = "SELECT * FROM ".$this->table. " WHERE id=".$id;
        
        return Dbcom::query($sql);
    }
    
    public function _save($data){
        global $system;
        if(property_exists($data, "id")){
            $sql = "UPDATE ".$this->table." SET nome='".$data->nome."',custo=".$data->custo." WHERE id=".$data->id;
        } else{
            if(!empty($test = Dbcom::query("SELECT id FROM ".$this->table." WHERE nome='".$data->nome."'"))){
                $system->alert("Já existe outro ingrediente registrado com esse nome.");
                return false;
            }
            $sql = "INSERT INTO ".$this->table." (id, nome, custo) VALUES(default,'".$data->nome."',".$data->custo.")";
        }
        
        return Dbcom::query($sql);
    }
    
    public function _delete($id){
        $sql = "DELETE FROM ".$this->table." WHERE id=".$id;
        
        return Dbcom::query($sql);
    }
    
    public function del_assoc($foreign){
        $sql = "DELETE FROM ingredientes_marmita WHERE id_ingrediente=".$foreign;
        
        return Dbcom::query($sql);
    }

}

?>