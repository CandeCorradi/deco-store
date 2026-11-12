<?php
// modulos/ventas_borrar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Soft Delete de la venta
    $query = "UPDATE ventas SET esta_eliminado = 1 WHERE id_ventas = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo "<script>window.location.href = 'index.php?page=ventas';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-4'>Error al intentar eliminar la venta: " . mysqli_error($conexion) . "</div>";
    }
} else {
    echo "<script>window.location.href = 'index.php?page=ventas';</script>";
    exit;
}
?>