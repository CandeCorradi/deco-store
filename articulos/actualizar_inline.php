<?php
// articulos/actualizar_inline.php
session_start();
include '../php/conexion.php';

// Leemos los datos que nos manda JavaScript en formato JSON
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id']) && isset($input['columna']) && isset($input['valor'])) {
    $id = intval($input['id']);
    $columna = mysqli_real_escape_string($conexion, $input['columna']);
    $valor = mysqli_real_escape_string($conexion, trim($input['valor']));

    // Lista de columnas permitidas por seguridad para evitar inyecciones SQL
    $columnas_permitidas = ['nombre', 'precio_costo', 'precio_venta', 'stock'];

    if (in_array($columna, $columnas_permitidas)) {
        // Ejecutamos la actualización automática
        $query = "UPDATE articulos SET $columna = '$valor' WHERE id_articulos = $id";
        
        if (mysqli_query($conexion, $query)) {
            echo json_encode(['status' => 'success']);
            exit();
        }
    }
}

echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar']);
?>