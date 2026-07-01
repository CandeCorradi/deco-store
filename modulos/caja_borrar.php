<?php
// modulos/caja_borrar.php

// 1. Validamos que llegue el ID por la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // 💡 Aplicamos Soft Delete: cambiamos esta_eliminado a 1
    $query = "UPDATE caja SET esta_eliminado = 1 WHERE id_caja = $id";
    
    if (mysqli_query($conexion, $query)) {
        // Redirigimos de vuelta a la caja con un script limpio
        echo "<script>window.location.href = 'index.php?page=caja';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-4'>Error al intentar eliminar el registro: " . mysqli_error($conexion) . "</div>";
    }
} else {
    echo "<script>window.location.href = 'index.php?page=caja';</script>";
    exit;
}