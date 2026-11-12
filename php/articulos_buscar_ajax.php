<?php
// php/articulos_buscar_ajax.php
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (mb_strlen($q) < 1) {
    echo json_encode(['results' => []]);
    exit;
}

$termino = mysqli_real_escape_string($conexion, $q);

$sql = "SELECT id_articulos, nombre, precio_venta, stock 
        FROM articulos 
        WHERE esta_eliminado = 0 
          AND stock > 0 
          AND nombre LIKE '%$termino%' 
        ORDER BY nombre ASC 
        LIMIT 10";

$res = mysqli_query($conexion, $sql);
$items = [];

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $items[] = [
            'id'           => $row['id_articulos'],
            'nombre'       => $row['nombre'],
            'precio_venta' => floatval($row['precio_venta']),
            'stock'        => intval($row['stock'])
        ];
    }
}

echo json_encode(['results' => $items]);
exit;