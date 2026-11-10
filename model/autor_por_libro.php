<?php

/*
    @Autor: Thomas Lopera Muñoz
    @Fecha: 10/09/2026
    @Descipción: Clase modelo de la tabla 'autores' de la base de datos
*/

Class Autor_por_libro {
    // Definición de los atributos de la clase (campos de la tabla)
    private $idAutor;
    private $isbn;


    public function __construct($idAutor, $isbn) {
        $this->idAutor = $idAutor;
        $this->isbn = $isbn;
    }

    public function getIdAutor() {
        return $this->idAutor;
    }

    public function getIsbn() {
        return $this->isbn;
    }

    public function setIdAutor($idAutor) {
        $this->idAutor = $idAutor;
    }

    public function setIsbn($isbn) {
    $this->isbn = $isbn;
}
}
?>