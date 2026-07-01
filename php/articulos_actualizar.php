<?php
// php/articulos_actualizar.php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']); // Recibe el ID oculto del mueble
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $id_categoria = intval($_POST['id_categorias']); 
    $precio_costo = floatval($_POST['precio_costo']);
    $precio_venta = floatval($_POST['precio_venta']);
    $stock = intval($_POST['stock']);

    // Modifica el registro existente que coincida con el ID
    $query_update = "UPDATE articulos SET 
                    nombre = '$nombre', 
                    id_categorias = $id_categoria, 
                    precio_costo = $precio_costo, 
                    precio_venta = $precio_venta, 
                    stock = $stock 
                    WHERE id_articulos = $id";

    if (mysqli_query($conexion, $query_update)) {
        header("Location: ../index.php?page=articulos");
        exit();
    }
}
?>