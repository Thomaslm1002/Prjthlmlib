<?php
require_once __DIR__ . '/conexionbd.php';
require_once __DIR__ . '/../model/libros.php';

class LibrosCtrl {
    private $conn;

    public function __construct() {
        $this->conn = ConexionBD::getConn();
    }

    public function listar() {
        try {
            $stmt = $this->conn->query(
                "SELECT l.isbn, l.titulo, l.categoria, c.categoria AS nombre_categoria,
                        l.fecha_publicacion, l.precio, l.portada, l.cantidad_stock
                 FROM libros l
                 LEFT JOIN categorias c ON c.id_categoria = l.categoria
                 ORDER BY l.titulo"
            );
            $libros = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
                $libro = new Libros(
                    $fila['isbn'], $fila['titulo'], $fila['categoria'],
                    $fila['fecha_publicacion'], $fila['precio'],
                    $fila['portada'], $fila['cantidad_stock']
                );
                $libro->setNombreCategoria($fila['nombre_categoria']);
                $libros[] = $libro;
            }
            return $libros;
        } catch (Exception $exc) {
            error_log("Error al listar libros: " . $exc->getMessage());
            return [];
        }
    }

    public function obtenerPorIsbn($isbn) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT isbn, titulo, categoria, fecha_publicacion, precio, portada, cantidad_stock
                 FROM libros WHERE isbn = ?"
            );
            $stmt->execute([$isbn]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$fila) {
                return null;
            }
            return new Libros(
                $fila['isbn'], $fila['titulo'], $fila['categoria'],
                $fila['fecha_publicacion'], $fila['precio'],
                $fila['portada'], $fila['cantidad_stock']
            );
        } catch (Exception $exc) {
            error_log("Error al consultar libro: " . $exc->getMessage());
            return null;
        }
    }

    public function listarCategorias() {
        try {
            return $this->conn->query(
                "SELECT id_categoria, categoria FROM categorias ORDER BY categoria"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            error_log("Error al listar categorías para libros: " . $exc->getMessage());
            return [];
        }
    }

    public function insertar($libro) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO libros (isbn, titulo, categoria, fecha_publicacion, precio, portada, cantidad_stock)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $libro->getIsbn(), $libro->getTitulo(), $libro->getCategoria(),
                $libro->getFecha(), $libro->getPrecio(), $libro->getPortada(),
                $libro->getCantidadStock()
            ]);
        } catch (Exception $exc) {
            error_log("Error al insertar libro: " . $exc->getMessage());
            return false;
        }
    }

    public function actualizar($libro) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE libros SET titulo = ?, categoria = ?, fecha_publicacion = ?,
                    precio = ?, portada = ?, cantidad_stock = ? WHERE isbn = ?"
            );
            return $stmt->execute([
                $libro->getTitulo(), $libro->getCategoria(), $libro->getFecha(),
                $libro->getPrecio(), $libro->getPortada(), $libro->getCantidadStock(),
                $libro->getIsbn()
            ]);
        } catch (Exception $exc) {
            error_log("Error al actualizar libro: " . $exc->getMessage());
            return false;
        }
    }

    public function eliminar($isbn) {
        try {
            $libro = $this->obtenerPorIsbn($isbn);
            $stmt = $this->conn->prepare("DELETE FROM libros WHERE isbn = ?");
            $eliminado = $stmt->execute([$isbn]);
            if ($eliminado && $libro !== null) {
                $portada = $libro->getPortada();
                if (is_string($portada)
                    && preg_match('~^uploads/libros/[a-f0-9]{32}\.(jpg|png|webp)$~', $portada)) {
                    $rutaPortada = __DIR__ . '/../' . $portada;
                    if (is_file($rutaPortada) && !unlink($rutaPortada)) {
                        error_log('No se pudo eliminar la portada del libro: ' . $rutaPortada);
                    }
                }
            }
            return $eliminado;
        } catch (Exception $exc) {
            error_log("Error al eliminar libro: " . $exc->getMessage());
            return false;
        }
    }
}
