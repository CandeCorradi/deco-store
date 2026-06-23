<?php
// metodos_pago/listar.php
session_start();
include '../php/conexion.php';

// 1. Configuración para el molde maestro
$titulo_modulo = "Métodos de Pago";
$icono_modulo  = "fas fa-credit-card";
$campo_id      = "id_metodos_pago"; // Tu clave primaria exacta de la tabla metodos_pago

// 2. Consulta de datos
$query = "SELECT * FROM metodos_pago ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $query);

// 3. Llamamos al componente maestro
include '../componentes/tabla_generica.php';
?>