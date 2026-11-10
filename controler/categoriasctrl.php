<?php
require_once __DIR__ . '/conexionbd.php';
require_once __DIR__ . '/../model/categorias.php';

class CategoriasCtrl {
    private $conn;

    public function __construct() {
        $this->conn = ConexionBD::getConn();
    }

    public function listar() {
        try {
            $filas = $this->conn->query(
                "SELECT id_categoria, categoria FROM categorias ORDER BY categoria"
            )->fetchAll(PDO::FETCH_ASSOC);
            $categorias = [];
            foreach ($filas as $fila) {
                $categoria = new Categorias($fila['categoria']);
                $categoria->setIdCategoria($fila['id_categoria']);
                $categorias[] = $categoria;
            }
            return $categorias;
        } catch (Exception $exc) {
            error_log("Error al listar categorías: " . $exc->getMessage());
            return [];
        }
    }

    public function obtenerPorId($idCategoria) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT id_categoria, categoria FROM categorias WHERE id_categoria = ?"
            );
            $stmt->execute([$idCategoria]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$fila) {
                return null;
            }
            $categoria = new Categorias($fila['categoria']);
            $categoria->setIdCategoria($fila['id_categoria']);
            return $categoria;
        } catch (Exception $exc) {
            error_log("Error al consultar categoría: " . $exc->getMessage());
            return null;
        }
    }

    public function insertar($categoria) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO categorias (categoria) VALUES (?)");
            return $stmt->execute([$categoria->getCategoria()]);
        } catch (Exception $exc) {
            error_log("Error al insertar categoría: " . $exc->getMessage());
            return false;
        }
    }

    public function actualizar($categoria) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE categorias SET categoria = ? WHERE id_categoria = ?"
            );
            return $stmt->execute([$categoria->getCategoria(), $categoria->getIdCategoria()]);
        } catch (Exception $exc) {
            error_log("Error al actualizar categoría: " . $exc->getMessage());
            return false;
        }
    }

    public function eliminar($idCategoria) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM categorias WHERE id_categoria = ?");
            return $stmt->execute([$idCategoria]);
        } catch (Exception $exc) {
            error_log("Error al eliminar categoría: " . $exc->getMessage());
            return false;
        }
    }
}
