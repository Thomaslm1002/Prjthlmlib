<?php

/*
    @Autor: Thomas Lopera Muñoz
    @Fecha: 10/09/2026
    @Descipción: Clase modelo de la tabla 'autores' de la base de datos
*/

class Autores {
    // Definición de los atributos de la clase (campos de la tabla)

    private $idAutor;
    private $nombres;
    private $apellidos;

    // Defición del Constructor
    public function __construct($nombres, $apellidos) {
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
    }

    //Getters y Setters
    public function getIdAutor() {
        return $this->idAutor;
    }

    public function getNombres() {
        return $this->nombres;
    }

    public function getApellidos() {
        return $this->apellidos;
    }

    public function setIdAutor($idAutor) {
        $this->idAutor = $idAutor;
    }
    public function setNombres($nombres) {
        $this->nombres = $nombres;
    }

    public function setApellidos($apellidos){
        $this->apellidos = $apellidos;
    }
}

?>