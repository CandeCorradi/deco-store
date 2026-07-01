<?php
// php/conexion.php

//conexión local
/*
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "d26com_db_cande";

// Creamos la conexión
$conexion = mysqli_connect($host, $usuario, $password, $base_datos);
*/

//conexión con el servidor
$db_host = "192.185.194.188";
$db_user = "d26com_usr_general";
$db_pass = "Isp203040";
$db_name = "d26com_db_cande";


$conexion = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Verificamos si la conexión falló
if (!$conexion) {
    die("Error de conexión a la nube: " . mysqli_connect_error());
}

// Configurar caracteres para que no rompa los acentos ni las Ñ
mysqli_set_charset($conexion, "utf8");
?>