<?php
// provincias/borrar.php
session_start();
include '../php/conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Eliminación directa de la tabla simple
    $query = "DELETE FROM provincias WHERE id_provincias = $id";
    mysqli_query($conexion, $query);
}

header("Location: listar.php");
exit();
?>