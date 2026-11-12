<?php
// modulos/tipos_caja_borrar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $query = "UPDATE tipos_caja SET esta_eliminado = 1 WHERE id_tipo_caja = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo "<script>window.location.href = 'index.php?page=tipos_caja';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-4'>Error al intentar eliminar el registro: " . mysqli_error($conexion) . "</div>";
    }
} else {
    echo "<script>window.location.href = 'index.php?page=tipos_caja';</script>";
    exit;
}
?>