<?php
require_once __DIR__ . '/conexionbd.php';
require_once __DIR__ . '/../model/autor_por_libro.php';

class AutorPorLibroCtrl {
    private $conn;

    public function __construct() {
        $this->conn = ConexionBD::getConn();
    }

    public function listar() {
        try {
            return $this->conn->query(
                "SELECT apl.id_autor, apl.isbn, a.nombre, a.apellido, l.titulo
                 FROM autor_por_libro apl
                 INNER JOIN autores a ON a.id_autor = apl.id_autor
                 INNER JOIN libros l ON l.isbn = apl.isbn
                 ORDER BY l.titulo, a.apellido, a.nombre"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            error_log("Error al listar relaciones autor-libro: " . $exc->getMessage());
            return [];
        }
    }

    public function obtener($idAutor, $isbn) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT id_autor, isbn FROM autor_por_libro WHERE id_autor = ? AND isbn = ?"
            );
            $stmt->execute([$idAutor, $isbn]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ? new Autor_por_libro($fila['id_autor'], $fila['isbn']) : null;
        } catch (Exception $exc) {
            error_log("Error al consultar relación autor-libro: " . $exc->getMessage());
            return null;
        }
    }

    public function listarAutores() {
        try {
            return $this->conn->query(
                "SELECT id_autor, nombre, apellido FROM autores ORDER BY apellido, nombre"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            error_log("Error al listar autores: " . $exc->getMessage());
            return [];
        }
    }

    public function listarLibros() {
        try {
            return $this->conn->query(
                "SELECT isbn, titulo FROM libros ORDER BY titulo"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            error_log("Error al listar libros para autores: " . $exc->getMessage());
            return [];
        }
    }

    public function insertar($relacion) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO autor_por_libro (id_autor, isbn) VALUES (?, ?)"
            );
            return $stmt->execute([$relacion->getIdAutor(), $relacion->getIsbn()]);
        } catch (Exception $exc) {
            error_log("Error al insertar relación autor-libro: " . $exc->getMessage());
            return false;
        }
    }

    public function actualizar($relacion, $idAutorOriginal, $isbnOriginal) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE autor_por_libro SET id_autor = ?, isbn = ?
                 WHERE id_autor = ? AND isbn = ?"
            );
            return $stmt->execute([
                $relacion->getIdAutor(), $relacion->getIsbn(),
                $idAutorOriginal, $isbnOriginal
            ]);
        } catch (Exception $exc) {
            error_log("Error al actualizar relación autor-libro: " . $exc->getMessage());
            return false;
        }
    }

    public function eliminar($idAutor, $isbn) {
        try {
            $stmt = $this->conn->prepare(
                "DELETE FROM autor_por_libro WHERE id_autor = ? AND isbn = ?"
            );
            return $stmt->execute([$idAutor, $isbn]);
        } catch (Exception $exc) {
            error_log("Error al eliminar relación autor-libro: " . $exc->getMessage());
            return false;
        }
    }
}
