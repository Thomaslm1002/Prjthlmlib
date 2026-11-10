<?php
require_once __DIR__ . '/conexionbd.php';
require_once __DIR__ . '/../model/autores.php';

class AutoresCtrl {
    private $conn;

    public function __construct() {
        $this->conn = ConexionBD::getConn();
    }

    public function listar() {
        try {
            $filas = $this->conn->query(
                "SELECT id_autor, nombre, apellido FROM autores ORDER BY apellido, nombre"
            )->fetchAll(PDO::FETCH_ASSOC);
            $autores = [];
            foreach ($filas as $fila) {
                $autor = new Autores($fila['nombre'], $fila['apellido']);
                $autor->setIdAutor($fila['id_autor']);
                $autores[] = $autor;
            }
            return $autores;
        } catch (Exception $exc) {
            error_log("Error al listar autores: " . $exc->getMessage());
            return [];
        }
    }

    public function obtenerPorId($idAutor) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT id_autor, nombre, apellido FROM autores WHERE id_autor = ?"
            );
            $stmt->execute([$idAutor]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$fila) {
                return null;
            }
            $autor = new Autores($fila['nombre'], $fila['apellido']);
            $autor->setIdAutor($fila['id_autor']);
            return $autor;
        } catch (Exception $exc) {
            error_log("Error al consultar autor: " . $exc->getMessage());
            return null;
        }
    }

    public function insertar($autor) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO autores (nombre, apellido) VALUES (?, ?)"
            );
            return $stmt->execute([$autor->getNombres(), $autor->getApellidos()]);
        } catch (Exception $exc) {
            error_log("Error al insertar autor: " . $exc->getMessage());
            return false;
        }
    }

    public function actualizar($autor) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE autores SET nombre = ?, apellido = ? WHERE id_autor = ?"
            );
            return $stmt->execute([
                $autor->getNombres(), $autor->getApellidos(), $autor->getIdAutor()
            ]);
        } catch (Exception $exc) {
            error_log("Error al actualizar autor: " . $exc->getMessage());
            return false;
        }
    }

    public function eliminar($idAutor) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM autores WHERE id_autor = ?");
            return $stmt->execute([$idAutor]);
        } catch (Exception $exc) {
            error_log("Error al eliminar autor: " . $exc->getMessage());
            return false;
        }
    }
}
