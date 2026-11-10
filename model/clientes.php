<?php

/*
    @Autor: Thomas Lopera Muñoz
    @Fecha: 10/09/2026
    @Descipción: Clase modelo de la tabla 'clientes' de la base de datos
*/

Class Clientes {
    private $idCliente;
    private $identificacion;
    private $nombre;
    private $apellido;
    private $celular;
    private $direccion;
    private $correo;

    public function __construct($identificacion, $nombre, $apellido, $celular, $direccion, $correo) {
        $this->idCliente = null; // Inicializar idCliente como null
        $this->identificacion = $identificacion;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->celular = $celular;
        $this->direccion = $direccion;
        $this->correo = $correo;
    }

    public function getIdCliente() {
        return $this->idCliente;
    }

    public function getIdentificacion() {
        return $this->identificacion;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function getCelular() {
        return $this->celular;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function setIdCliente($idCliente) {
        $this->idCliente = $idCliente;
    }

    public function setIdentificacion($identificacion) {
        $this->identificacion = $identificacion;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function setCelular($celular) {
        $this->celular = $celular;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
    }
}