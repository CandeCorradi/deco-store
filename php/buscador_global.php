<?php
// php/buscador_global.php

// Al estar dentro de la carpeta 'php/', conexion.php está al lado
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Evitar que warnings rompan la salida JSON
error_reporting(0);

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (mb_strlen($q) < 2) {
    echo json_encode(['articulos' => [], 'clientes' => [], 'ventas' => []]);
    exit;
}

$termino = mysqli_real_escape_string($conexion, $q);

$resultados = [
    'articulos' => [],
    'clientes'  => [],
    'ventas'    => []
];

// 1. Artículos (usando la columna real: precio_venta)
$q_art = "SELECT id_articulos, nombre, precio_venta, stock 
          FROM articulos 
          WHERE esta_eliminado = 0 
            AND nombre LIKE '%$termino%' 
          ORDER BY id_articulos ASC 
          LIMIT 5";
$r_art = mysqli_query($conexion, $q_art);
if ($r_art) {
    while ($row = mysqli_fetch_assoc($r_art)) {
        $precio = number_format($row['precio_venta'] ?? 0, 2, ',', '.');
        $resultados['articulos'][] = [
            'id'     => $row['id_articulos'],
            'titulo' => $row['nombre'],
            'sub'    => "Stock: {$row['stock']} | \${$precio}",
            'url'    => "index.php?page=articulos_editar&id=" . $row['id_articulos']
        ];
    }
}

// 2. Clientes
$q_cli = "SELECT id_clientes, nombre, apellido, telefono 
          FROM clientes 
          WHERE esta_eliminado = 0 
            AND (nombre LIKE '%$termino%' OR apellido LIKE '%$termino%' OR telefono LIKE '%$termino%') 
          ORDER BY id_clientes ASC 
          LIMIT 5";
$r_cli = mysqli_query($conexion, $q_cli);
if ($r_cli) {
    while ($row = mysqli_fetch_assoc($r_cli)) {
        $nom = trim(($row['nombre'] ?? '') . ' ' . ($row['apellido'] ?? ''));
        $tel = !empty($row['telefono']) ? "Tel: " . $row['telefono'] : "Sin teléfono";
        $resultados['clientes'][] = [
            'id'     => $row['id_clientes'],
            'titulo' => $nom ?: 'Cliente General',
            'sub'    => $tel,
            'url'    => "index.php?page=clientes_cuenta_corriente&id=" . $row['id_clientes'] // <-- CAMBIADO A &id=
        ];
    }
}

// 3. Ventas (por N° o por nombre del cliente)
$q_ven = "SELECT v.id_ventas, v.total, v.fecha_hora, c.nombre AS cli_nombre, c.apellido AS cli_apellido 
          FROM ventas v
          LEFT JOIN clientes c ON v.id_clientes = c.id_clientes 
          WHERE v.esta_eliminado = 0 
            AND (v.id_ventas = '$termino' OR c.nombre LIKE '%$termino%' OR c.apellido LIKE '%$termino%') 
          ORDER BY v.id_ventas DESC 
          LIMIT 5";
$r_ven = mysqli_query($conexion, $q_ven);
if ($r_ven) {
    while ($row = mysqli_fetch_assoc($r_ven)) {
        $nom_cli = trim(($row['cli_nombre'] ?? '') . ' ' . ($row['cli_apellido'] ?? 'General'));
        $tot = number_format($row['total'] ?? 0, 2, ',', '.');
        $fec = date('d/m/Y', strtotime($row['fecha_hora']));
        $resultados['ventas'][] = [
            'id'     => $row['id_ventas'],
            'titulo' => "Venta #{$row['id_ventas']} - {$nom_cli}",
            'sub'    => "{$fec} | \${$tot}",
            'url'    => "index.php?page=ventas_detalle&id_venta=" . $row['id_ventas']
        ];
    }
}

echo json_encode($resultados);
exit;