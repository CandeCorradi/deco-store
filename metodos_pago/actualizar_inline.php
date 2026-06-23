<?php
// metodos_pago/actualizar_inline.php
session_start();
include '../php/conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $id = intval($data['id']);
    $valor = mysqli_real_escape_string($conexion, trim($data['valor']));

    $query = "UPDATE metodos_pago SET nombre = '$valor' WHERE id_metodos_pago = $id";
    mysqli_query($conexion, $query);
}
?>