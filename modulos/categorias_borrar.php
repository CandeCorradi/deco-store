<?php
// modulos/categorias_borrar.php

// 1. Validamos que llegue el ID por la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Soft Delete: cambiamos esta_eliminado a 1
    $query = "UPDATE categorias SET esta_eliminado = 1 WHERE id_categorias = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo "<script>window.location.href = 'index.php?page=categorias';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-4'>Error al intentar eliminar la categoría: " . mysqli_error($conexion) . "</div>";
    }
} else {
    echo "<script>window.location.href = 'index.php?page=categorias';</script>";
    exit;
}
?>