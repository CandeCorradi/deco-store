<?php
// php/conexion.php

$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "d26com_db_cande";

// Creamos la conexión
$conexion = mysqli_connect($host, $usuario, $password, $base_datos);

// Verificamos si la conexión falló
if (!$conexion) {
    die("Error de conexión a la nube: " . mysqli_connect_error());
}

// Configurar caracteres para que no rompa los acentos ni las Ñ
mysqli_set_charset($conexion, "utf8");
?>