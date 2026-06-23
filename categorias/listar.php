<?php
// categorias/listar.php
session_start();
include '../php/conexion.php';

// 1. Configuración para el molde genérico
$titulo_modulo = "Categorías de Artículos";
$icono_modulo  = "fas fa-tags";
$campo_id      = "id_categorias"; // clave primaria en la bd

// 2. Consulta de datos
$query = "SELECT * FROM categorias ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $query);

// 3. Llamamos al molde genérico (aquí se dibuja la tabla con DataTables y doble clic)
include '../componentes/tabla_generica.php';
?>