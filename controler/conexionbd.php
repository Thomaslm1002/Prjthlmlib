<?php
    /*
    @autor: Thomas Lopera Muñoz
    @Fecha: 17/09/2026
    @Descripción: Archivo de conexión a la base de datos
    */

    require_once __DIR__ . '/properties.php';
    class ConexionBD {
    public static function getConn() {
        // se crea la conexión aplicando UTF-8 para los caracteres especiales
        $link = null;
    try {
        $link = new PDO(
            "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASSWORD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
    } catch (PDOException $pdoe) {
        exit("Error: " . $pdoe->getMessage());
    }
    return $link;
    }
    }

?>