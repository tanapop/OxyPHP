<?php

class _CLASS_NAME_ extends Model {

    public function _get($fields, $conditions, $debug = false) {
        $sql = "SELECT ";

        if (is_array($fields)) {
            foreach ($fields as $f) {
                $sql .= $f . ",";
            }
            $sql = rtrim($sql, ",");
        } elseif (is_string($fields)) {
            $sql .= $fields;
        } else {
            return false;
        }

        $sql .= " FROM " . $this->table;

        if (!empty($conditions) && is_array($conditions)) {
            $sql .= " WHERE ";
            foreach ($conditions as $key => $val) {
                $sql .= $key . "=" . (is_numeric($val) ? $val : "'" . $val . "'");
                $sql .= " AND ";
            }
            $sql = rtrim($sql, " AND ");
        }

        if ($result = $this->mysql->query($sql)) {
            if (count($result) > 1) {
                return $result;
            } else {
                return $result[0];
            }
        } else
            return false;
    }

    public function _save($data, $debug = false) {
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

    public function _delete($list, $debug = false) {
        $sql = "DELETE FROM " . $this->table . " WHERE id";

        if (is_array($list)) {
            $sql .= " IN (";
            foreach ($list as $id) {
                $sql .= $id . ",";
            }
            $sql = rtrim($sql, ",");
            $sql .= ")";
        } elseif (is_numeric($list)) {
            $sql .= "=" . $list;
        } else {
            return false;
        }

        return $this->mysql->query($sql);
    }

}

?>