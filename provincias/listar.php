<?php
// provincias/listar.php
session_start();
include '../php/conexion.php';

// 1. Configuración que el molde genérico (tabla_generica.php) necesita saber
$titulo_modulo = "Gestión de Provincias";
$icono_modulo  = "fas fa-map-marker-alt";
$campo_id      = "id_provincias"; // Tu clave primaria exacta de la tabla provincias

// 2. Consulta de datos
$query = "SELECT * FROM provincias ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $query);

// 3. Llamamos al componente maestro
include '../componentes/tabla_generica.php';
?>