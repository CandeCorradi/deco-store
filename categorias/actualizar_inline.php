<?php
// categorias/actualizar_inline.php
session_start();
include '../php/conexion.php';

// Leemos el JSON que envía el JavaScript del molde genérico
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $id = intval($data['id']);
    $valor = mysqli_real_escape_string($conexion, trim($data['valor']));

    // Actualizamos dinámicamente la tabla usando la clave primaria de categorías
    $query = "UPDATE categorias SET nombre = '$valor' WHERE id_categorias = $id";
    mysqli_query($conexion, $query);
}
?>