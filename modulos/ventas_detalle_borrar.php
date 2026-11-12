<?php
// modulos/ventas_detalle_borrar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Al ser un detalle directo, realizamos un DELETE físico de la fila
    $query = "DELETE FROM ventas_detalle WHERE id_ventas_detalle = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo "<script>window.location.href = 'javascript:history.back()';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-4'>Error al intentar eliminar el registro: " . mysqli_error($conexion) . "</div>";
    }
} else {
    echo "<script>window.location.href = 'index.php?page=ventas_detalle';</script>";
    exit;
}
?>