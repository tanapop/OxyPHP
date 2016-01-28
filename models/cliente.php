<?php

class ModelCliente {

    private $table;

    public function __construct() {
        require_once $_SERVER["DOCUMENT_ROOT"]."/engine/class.dbcom.php";
        $this->table = "cliente";
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
            $sql = "UPDATE ".$this->table." SET nome='".$data->nome."' WHERE id=".$data->id;
        } else{
            if(!empty($test = Dbcom::query("SELECT id FROM ".$this->table." WHERE nome='".$data->nome."'"))){
                $system->alert("Já existe outro cliente registrado com esse nome.");
                return false;
            }
            $sql = "INSERT INTO ".$this->table." (id, nome) VALUES(default,'".$data->nome."')";
        }
        
        return Dbcom::query($sql);
    }
    
    public function _delete($id){
        $sql = "DELETE FROM ".$this->table." WHERE id=".$id;
        
        return Dbcom::query($sql);
    }

}

?>