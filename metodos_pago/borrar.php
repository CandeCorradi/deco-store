<?php
// metodos_pago/borrar.php
session_start();
include '../php/conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $query = "DELETE FROM metodos_pago WHERE id_metodos_pago = $id";
    mysqli_query($conexion, $query);
}

header("Location: listar.php");
exit();
?>