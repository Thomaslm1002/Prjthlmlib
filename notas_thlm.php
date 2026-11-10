<?php
    /*
    @autor: Thomas Lopera Muñoz
    @fecha: 01/09/2026
    @descripción: El reporte de calificaciones de la materia de programación.
    */

    /*
    Ejercicio: se debe mostrar el reporte de calificaciones
    de un estudiante. se debe calcular la nota
    definitiva de éste. 
    El reporte incluye los siguentes datos:
    - Identificación del estudiante
    - Nombre del estudiante
    - Nombre de la asignatura cursada
    - Las 4 notas de la asignatura
    - El promedio de la asignatura con 2 decimales
    - Las notas están en el rango de 0.00 a 5.0
    - Crear un archivo nuevo llamado notas_thlm.php
    - Donde xxx son las iniciales de los nombres y apellidos
    */


    $identificacion = "1038870865";
    $nombre = "Thomas Lopera Muñoz";
    $asignatura = "Programación";
    $nota1 = 4.51;
    $nota2 = 3.84;
    $nota3 = 4.25;
    $nota4 = 4.06;
    $promedio = ($nota1 + $nota2 + $nota3 + $nota4) / 4;
    $promedio = number_format($promedio, 2);

header("Content-Type: text/plain");
echo " Reporte de Calificaciones\n";
echo "Identificación : " . $identificacion . "\n";
echo "Estudiante     : " . $nombre . "\n";
echo "Asignatura     : " . $asignatura . "\n";
echo "Nota 1         : " . $nota1 . "\n";
echo "Nota 2         : " . $nota2 . "\n";
echo "Nota 3         : " . $nota3 . "\n";
echo "Nota 4         : " . $nota4 . "\n";
echo "Nota Definitiva: " . $promedio . "\n";
?>
