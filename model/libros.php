<?php

/*
    @Autor: Thomas Lopera Muñoz
    @Fecha: 10/09/2026
    @Descipción: Clase modelo de la tabla 'libros' de la base de datos
*/

Class Libros {
    // Definición de los atributos de la clase (campos de la tabla)

    private $isbn;
    private $titulo;
    private $categoria;
    private $fechaPub;
    private $precio;
    private $portada;
    private $cantidadStock;
    private $nombreCategoria;

    // Defición del Constructor
    public function __construct($isbn, $titulo, $categoria, $fechaPub, $precio, $portada, $cant_stock) {
        $this->isbn = $isbn;
        $this->titulo = $titulo;
        $this->categoria = $categoria;
        $this->fechaPub = $fechaPub;
        $this->precio = $precio;
        $this->portada = $portada;
        $this->cantidadStock = $cant_stock;
        $this->nombreCategoria = null;
    }
    //Getters y Setters
    public function getIsbn() {
        return $this->isbn;
    }

    public function getTitulo() {
        return $this->titulo;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function getFecha() {
        return $this->fechaPub;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function getPortada() {
        return $this->portada;
    }

    public function getCantidadStock() {
        return $this->cantidadStock;
    }

    public function getNombreCategoria() {
        return $this->nombreCategoria;
    }

    public function setNombreCategoria($nombreCategoria) {
        $this->nombreCategoria = $nombreCategoria;
    }

    public function setIsbn($isbn) {
        $this->isbn = $isbn;
    }

    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }

    public function setFecha($fechaPub) {
        $this->fechaPub = $fechaPub;
    }  

    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    public function setPortada($portada) {
        $this->portada = $portada;
    }

    public function setCantidadStock($cantidadStock) {
        $this->cantidadStock = $cantidadStock;
    }

}
