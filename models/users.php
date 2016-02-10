<?php

class ModelUsers {

    private $table;
    private $dbcon;

    public function __construct() {
        require_once $_SERVER["DOCUMENT_ROOT"] . "/engine/class.mysqldb.php";
        $this->dbcon = new MysqlDb();
        $this->table = "users";
    }

    public function _list() {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY id ASC";

        return $this->dbcon->query($sql);
    }

    public function _get($req) {

        $sql = "SELECT * FROM " . $this->table . " WHERE ";

        foreach ($req as $key => $val) {
            $sql .= $key . "=" . (is_numeric($val) ? $val : "'" . $val . "'");
            $sql .= " AND ";
        }

        $sql = rtrim($sql, " AND ");
        
        $result = Dbcom::query($sql);

        return $result[0];
    }

    public function _save($data) {
        global $system;
        if (property_exists($data, "id")) {
            $sql = "UPDATE " . $this->table .
                    " SET nome='" . $data->nome . "'" .
                    ",email='" . $data->email . "'" .
                    (!empty($data->senha) ? ",senha='" . $data->senha . "'" : "") .
                    ",telefone=" . (!empty($data->telefone) ? "'" . $data->telefone . "'" : "default") .
                    " WHERE id=" . $data->id;
        } else {
            if (!empty($test = Dbcom::query("SELECT id FROM " . $this->table . " WHERE nome='" . $data->nome . "'"))) {
                $system->alert("Já existe outro usuário registrado com esse nome.");
                return false;
            }
            $sql = "INSERT INTO " . $this->table . " (id, nome, email, senha, telefone) VALUES(default,'" .
                    $data->nome .
                    "','" . $data->email .
                    "','" . $data->senha .
                    "','" . $data->telefone .
                    "')";
        }

        return Dbcom::query($sql);
    }

    public function _delete($id) {
        $sql = "DELETE FROM " . $this->table . " WHERE id=" . $id;

        return Dbcom::query($sql);
    }

}

?>