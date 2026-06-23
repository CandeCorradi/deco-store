<?php
// articulos/borrar.php
session_start();
include '../php/conexion.php';

// Verificamos que nos llegue el ID por la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // ⚠️ CORREGIDO: Usamos la tabla 'articulos' con tu columna 'id'
    $query = "UPDATE articulos SET esta_eliminado = 1 WHERE id_articulos = $id";
    
    if (mysqli_query($conexion, $query)) {
        // Si sale bien, vuelve al listado con un aviso
        header("Location: listar.php?eliminado=1");
        exit();
    }
}

// Si no vino ID o algo falló, volvemos al listado por seguridad
header("Location: listar.php");
exit();
?>