<?php
// categorias/borrar.php
session_start();
include '../php/conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Eliminación directa de la tabla simple
    $query = "DELETE FROM categorias WHERE id_categorias = $id";
    mysqli_query($conexion, $query);
}

header("Location: listar.php");
exit();
?>