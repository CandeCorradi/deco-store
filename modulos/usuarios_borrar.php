<?php
// modulos/usuarios_borrar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Aplicamos Soft Delete para preservar la integridad de datos
    $query = "UPDATE usuarios SET esta_eliminado = 1 WHERE id_usuarios = $id";
    
    if (mysqli_query($conexion, $query)) {
        echo "<script>window.location.href = 'index.php?page=usuarios';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-4'>Error al intentar eliminar el usuario: " . mysqli_error($conexion) . "</div>";
    }
} else {
    echo "<script>window.location.href = 'index.php?page=usuarios';</script>";
    exit;
}
?>