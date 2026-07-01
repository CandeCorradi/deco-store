<?php
// php/articulos_borrar.php
session_start();
include 'conexion.php';

// Verificamos que nos pasen un ID válido por la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Ejecutamos el DELETE apuntando exactamente al ID del mueble
    $query_delete = "DELETE FROM articulos WHERE id_articulos = $id";

    if (mysqli_query($conexion, $query_delete)) {
        // Redirección limpia de vuelta al catálogo
        header("Location: ../index.php?page=articulos&eliminado=1");
        exit();
    } else {
        echo "Error al eliminar el artículo: " . mysqli_error($conexion);
        exit();
    }
} else {
    header("Location: ../index.php?page=articulos");
    exit();
}
?>