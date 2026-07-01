<?php
// modulos/clientes_borrar.php

// 1. Verificamos que el index nos esté pasando un ID válido por la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 2. Ejecutamos el DELETE apuntando exactamente al ID del cliente
    // (Asegurate de que la columna clave primaria en tu BD se llame id_clientes)
    $query_delete = "DELETE FROM clientes WHERE id_clientes = $id";

    if (mysqli_query($conexion, $query_delete)) {
        // Redirección limpia usando JavaScript para regresar al listado de clientes
        echo "<script>window.location.href='index.php?page=clientes';</script>";
        exit();
    } else {
        // Si la base de datos frena el borrado (por ejemplo, si el cliente tiene ventas asociadas)
        die("Error de MySQL al intentar eliminar el cliente: " . mysqli_error($conexion));
    }
} else {
    // Si intentan entrar a esta página sin un ID, los sacamos eyectados a la lista
    echo "<script>window.location.href='index.php?page=clientes';</script>";
    exit();
}
?>