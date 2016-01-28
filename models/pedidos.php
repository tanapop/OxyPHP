<?php

class ModelPedidos {

    private $table;

    public function __construct() {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/engine/class.dbcom.php";
        $this->table = "pedido";
    }

    public function _list() {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY id ASC";

        return Dbcom::query($sql);
    }

    public function _get($id) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id=" . $id;

        return Dbcom::query($sql);
    }

    public function _save($data) {
        global $system;

        $sql = "INSERT INTO pedido (id,id_cliente,id_marmita,qtde,data,id_usuario,custo) VALUES ";

        foreach ($data as $pedido) {
            $p = (object) $pedido;
            $sql .= "(default," . $p->id_cliente . "," . $p->id_marmita . ",".$p->qtde.",'".$p->data."',".$p->id_usuario.",".$p->custo."),";
        }

        $sql = rtrim($sql, ",");
        
        return Dbcom::query($sql);
    }

    public function _delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id=" . $id;

        return Dbcom::query($sql);
    }

    public function clientes_dia($dia) {
        $sql = "SELECT c.nome as cliente,"
                . "m.* "
                . "FROM marmita as m "
                . "LEFT JOIN cliente as c "
                . "ON c.id=m.id_cliente "
                . "WHERE m.dia=" . $dia;

        return Dbcom::query($sql);
    }
    
    public function test_day($begin){
        $sql = "SELECT id FROM pedido WHERE data>".$begin;
        
        return Dbcom::query($sql);
    }

}

?>