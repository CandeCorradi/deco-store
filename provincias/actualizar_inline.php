<?php
// provincias/actualizar_inline.php
session_start();
include '../php/conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $id = intval($data['id']);
    $valor = mysqli_real_escape_string($conexion, trim($data['valor']));

    // Actualizamos dinámicamente la tabla provincias usando su ID correspondiente
    $query = "UPDATE provincias SET nombre = '$valor' WHERE id_provincias = $id";
    mysqli_query($conexion, $query);
}
?>