<?php
// modulos/metodos_pago_borrar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $query = "UPDATE metodos_pago SET esta_eliminado = 1 WHERE id_metodos_pago = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo "<script>window.location.href = 'index.php?page=metodos_pago';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-4'>Error al intentar eliminar el registro: " . mysqli_error($conexion) . "</div>";
    }
} else {
    echo "<script>window.location.href = 'index.php?page=metodos_pago';</script>";
    exit;
}
?>