<?php
// exportar_venta_excel.php
require_once 'php/conexion.php';

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;

$q_venta = "SELECT v.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido 
            FROM ventas v
            LEFT JOIN clientes c ON v.id_clientes = c.id_clientes
            WHERE v.id_ventas = $id_venta AND v.esta_eliminado = 0";
$r_venta = mysqli_query($conexion, $q_venta);
$venta   = mysqli_fetch_assoc($r_venta);

if (!$venta) {
    die("Comprobante no encontrado.");
}

$q_items = "SELECT vd.*, a.nombre AS articulo_nombre 
            FROM ventas_detalle vd 
            INNER JOIN articulos a ON vd.id_articulos = a.id_articulos 
            WHERE vd.id_ventas = $id_venta";
$r_items = mysqli_query($conexion, $q_items);

$nombre_archivo = "DecoStore_Venta_" . $id_venta . "_" . date('Ymd') . ".csv";

// Cabeceras para forzar descarga de CSV compatible con Excel y Google Sheets
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Abrir salida directa en PHP
$salida = fopen('php://output', 'w');

// BOM UTF-8 para que Excel y Google Sheets lean acentos correctamente
fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));

// Encabezados informativos
fputcsv($salida, ['DECOSTORE - FACTURA DE VENTA #' . $id_venta], ';');
fputcsv($salida, ['Fecha:', date('d/m/Y H:i', strtotime($venta['fecha_hora']))], ';');
fputcsv($salida, ['Cliente:', trim(($venta['cli_nombre'] ?? '') . ' ' . ($venta['cli_apellido'] ?? 'General'))], ';');
fputcsv($salida, [], ';'); // Fila en blanco

// Columnas de la tabla
fputcsv($salida, ['Articulo', 'Cantidad', 'Precio Unitario', 'Subtotal'], ';');

// Filas de productos
while ($it = mysqli_fetch_assoc($r_items)) {
    fputcsv($salida, [
        $it['articulo_nombre'],
        $it['cantidad'],
        '$ ' . number_format($it['precio_unitario'], 2, ',', '.'),
        '$ ' . number_format($it['subtotal'], 2, ',', '.')
    ], ';');
}

// Fila Total
fputcsv($salida, [], ';');
fputcsv($salida, ['', '', 'TOTAL:', '$ ' . number_format($venta['total'], 2, ',', '.')], ';');

fclose($salida);
exit;