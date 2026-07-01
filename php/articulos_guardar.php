<?php
// php/articulos_guardar.php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $id_categoria = intval($_POST['id_categorias']); 
    $precio_costo = floatval($_POST['precio_costo']);
    $precio_venta = floatval($_POST['precio_venta']);
    $stock = intval($_POST['stock']);

    $query_insert = "INSERT INTO articulos (nombre, id_categorias, precio_costo, precio_venta, stock) 
                    VALUES ('$nombre', $id_categoria, $precio_costo, $precio_venta, $stock)";

    if (mysqli_query($conexion, $query_insert)) {
        header("Location: ../index.php?page=articulos");
        exit();
    }
}
?>