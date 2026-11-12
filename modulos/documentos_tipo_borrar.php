<?php
// modulos/documentos_tipo_borrar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $query = "UPDATE documentos_tipo SET esta_eliminado = 1 WHERE id_documentos_tipo = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo "<script>window.location.href = 'index.php?page=documentos_tipo';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-4'>Error al intentar eliminar el registro: " . mysqli_error($conexion) . "</div>";
    }
} else {
    echo "<script>window.location.href = 'index.php?page=documentos_tipo';</script>";
    exit;
}
?>