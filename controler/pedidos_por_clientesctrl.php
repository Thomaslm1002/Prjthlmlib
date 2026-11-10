<?php
require_once __DIR__ . '/conexionbd.php';
require_once __DIR__ . '/../model/pedidos_por_clientes.php';

class PedidosPorClientesCtrl {
    private $conn;

    public function __construct() {
        $this->conn = ConexionBD::getConn();
    }

    public function listar() {
        try {
            return $this->conn->query(
                "SELECT p.id_pedido, p.nro_pedido, p.id_cliente, p.isbn, p.fecha_pedido,
                        p.cantidad, p.subtotal, c.nombre AS nombre_cliente,
                        c.apellidos AS apellidos_cliente, l.titulo
                 FROM pedidos_cliente p
                 INNER JOIN clientes c ON c.id_cliente = p.id_cliente
                 INNER JOIN libros l ON l.isbn = p.isbn
                 ORDER BY p.id_pedido DESC"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            error_log("Error al listar pedidos: " . $exc->getMessage());
            return [];
        }
    }

    public function obtenerPorId($idPedido) {
        try {
            $stmt = $this->conn->prepare(
                "SELECT id_pedido, nro_pedido, id_cliente, isbn, fecha_pedido, cantidad, subtotal
                 FROM pedidos_cliente WHERE id_pedido = ?"
            );
            $stmt->execute([$idPedido]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            return $fila ? new Pedidos_por_clientes(
                $fila['id_pedido'], $fila['nro_pedido'], $fila['id_cliente'],
                $fila['isbn'], $fila['fecha_pedido'], $fila['cantidad'], $fila['subtotal']
            ) : null;
        } catch (Exception $exc) {
            error_log("Error al consultar pedido: " . $exc->getMessage());
            return null;
        }
    }

    public function listarClientes() {
        try {
            return $this->conn->query(
                "SELECT id_cliente, nombre, apellidos FROM clientes ORDER BY apellidos, nombre"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            error_log("Error al listar clientes para pedidos: " . $exc->getMessage());
            return [];
        }
    }

    public function listarLibros() {
        try {
            return $this->conn->query(
                "SELECT isbn, titulo, precio FROM libros ORDER BY titulo"
            )->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            error_log("Error al listar libros para pedidos: " . $exc->getMessage());
            return [];
        }
    }

    public function insertar($pedido) {
        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO pedidos_cliente
                    (nro_pedido, id_cliente, isbn, fecha_pedido, cantidad, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            return $stmt->execute([
                $pedido->getNroPedido(), $pedido->getIdCliente(), $pedido->getIsbn(),
                $pedido->getFechaPedido(), $pedido->getCantidad(), $pedido->getSubtotal()
            ]);
        } catch (Exception $exc) {
            error_log("Error al insertar pedido: " . $exc->getMessage());
            return false;
        }
    }

    public function actualizar($pedido) {
        try {
            $stmt = $this->conn->prepare(
                "UPDATE pedidos_cliente SET nro_pedido = ?, id_cliente = ?, isbn = ?,
                    fecha_pedido = ?, cantidad = ?, subtotal = ? WHERE id_pedido = ?"
            );
            return $stmt->execute([
                $pedido->getNroPedido(), $pedido->getIdCliente(), $pedido->getIsbn(),
                $pedido->getFechaPedido(), $pedido->getCantidad(), $pedido->getSubtotal(),
                $pedido->getIdPedido()
            ]);
        } catch (Exception $exc) {
            error_log("Error al actualizar pedido: " . $exc->getMessage());
            return false;
        }
    }

    public function eliminar($idPedido) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM pedidos_cliente WHERE id_pedido = ?");
            return $stmt->execute([$idPedido]);
        } catch (Exception $exc) {
            error_log("Error al eliminar pedido: " . $exc->getMessage());
            return false;
        }
    }
}
