<?php

class ModelMarmitas {

    private $table;

    public function __construct() {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/engine/class.dbcom.php";
        $this->table = "marmita";
    }

    public function _list($id_cliente) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_cliente=" . $id_cliente . " ORDER BY dia ASC";

        return Dbcom::query($sql);
    }

    public function get_ingr($m_id) {
        $sql = "SELECT "
                . "i.id,"
                . "i.nome,"
                . "i.custo "
                . "FROM ingredientes_marmita as m_i "
                . "LEFT JOIN ingrediente as i "
                . "ON i.id=m_i.id_ingrediente "
                . "WHERE m_i.id_marmita=" . $m_id;

        return Dbcom::query($sql);
    }

    public function _get($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id=" . $id;
        $res = Dbcom::query($sql);
        return $res[0];
    }

    public function _save($data) {
        global $system;
        
        if(!empty(Dbcom::query("SELECT id FROM ".$this->table." WHERE id_cliente=".$data->id_cliente." AND dia=".$data->dia))){
            $system->alert("Já existe uma marmita registrada neste dia para este cliente.", ALERT_ERROR);
            return false;
        }
        
        $sql = "INSERT INTO " . $this->table . " (id, custo, dia, id_cliente) VALUES("
                . "default,"
                . $data->custo . ","
                . $data->dia . ","
                . $data->id_cliente . ")";

        return Dbcom::insert($sql);
    }
    
    public function save_assoc($data,$foreign_key){
        $sql = "INSERT INTO ingredientes_marmita (id,id_marmita,id_ingrediente) VALUES ";
        
        foreach($data as $foreign){
            $sql .= "(default,".$foreign_key.",".$foreign."),";
        }
        
        $sql = rtrim($sql,",");
        
        return Dbcom::query($sql);
    }

    public function _delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id=" . $id;

        return Dbcom::query($sql);
    }
    
    public function del_assoc($foreign){
        $sql = "DELETE FROM ingredientes_marmita WHERE id_marmita=".$foreign;
        
        return Dbcom::query($sql);
    }

}

?>