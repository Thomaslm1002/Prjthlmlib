<?php

/*
    @Autor: Thomas Lopera Muñoz
    @Fecha: 10/09/2026
    @Descipción: Clase modelo de la tabla 'categorias' de la base de datos
*/

Class Pedidos_por_clientes {
    // Definición de los atributos de la clase (campos de la tabla)
    private $idPedido;
    private $nroPedido;
    private $idCliente;
    private $isbn;
    private $fechaPedido;

    private $cantidad;
    private $subtotal;

    public function __construct($id_pedido, $nro_pedido, $id_cliente, $isbn, $fecha_pedido, $cantidad, $subtotal) {
        $this->idPedido = $id_pedido;
        $this->nroPedido = $nro_pedido;
        $this->idCliente = $id_cliente;
        $this->isbn = $isbn;
        $this->fechaPedido = $fecha_pedido;
        $this->cantidad = $cantidad;
        $this->subtotal = $subtotal;
    }

    public function getIdPedido() {
        return $this->idPedido;
    }

    public function getNroPedido() {
        return $this->nroPedido;
    }

    public function getIdCliente() {
        return $this->idCliente;
    }

    public function getIsbn() {
        return $this->isbn;
    }

    public function getFechaPedido() {
        return $this->fechaPedido;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function getSubtotal() {
        return $this->subtotal;
    }

    public function setIdPedido($id_pedido) {
        $this->idPedido = $id_pedido;
    }

    public function setNroPedido($nro_pedido) {
        $this->nroPedido = $nro_pedido;
    }

    public function setIdCliente($id_cliente) {
        $this->idCliente = $id_cliente;
    }

    public function setIsbn($isbn) {
        $this->isbn = $isbn;
    }

    public function setFechaPedido($fecha_pedido) {
        $this->fechaPedido = $fecha_pedido;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    public function setSubtotal($subtotal) {
        $this->subtotal = $subtotal;
    }

}