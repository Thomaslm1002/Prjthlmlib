<?php

/*
    @Autor: Thomas Lopera Muñoz
    @Fecha: 10/09/2026
    @Descipción: Clase modelo de la tabla 'categorias' de la base de datos
*/

class Categorias {
    // Definición de los atributos de la clase (campos de la tabla)

    private $idCategoria;
    private $categoria;

    // Defición del Constructor
    public function __construct($categoria) {
        $this->categoria = $categoria;
    }

    //Getters y Setters
    public function getIdCategoria() {
        return $this->idCategoria;
    }

    public function getCategoria() {
        return $this->categoria;
    }

    public function setIdCategoria($idCategoria) {
        $this->idCategoria = $idCategoria;
    }

    public function setCategoria($categoria) {
        $this->categoria = $categoria;
    }
}



?>