<?php
// php/enviar_factura_ajax.php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/../config/mail.php';

header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

$id_venta = isset($_POST['id_venta']) ? intval($_POST['id_venta']) : 0;
$email_override = isset($_POST['email']) ? trim($_POST['email']) : '';

if ($id_venta <= 0) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Comprobante no válido.']);
    exit;
}

// 1. Obtener datos de la venta y del cliente
$q = "SELECT v.id_ventas, v.fecha_hora, v.total, 
             c.nombre AS cli_nombre, c.apellido AS cli_apellido, c.email AS cli_email
      FROM ventas v
      LEFT JOIN clientes c ON v.id_clientes = c.id_clientes
      WHERE v.id_ventas = $id_venta AND v.esta_eliminado = 0";

$res = mysqli_query($conexion, $q);
$venta = ($res) ? mysqli_fetch_assoc($res) : null;

if (!$venta) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Comprobante no encontrado en el sistema.']);
    exit;
}

$nombre_cliente = trim(($venta['cli_nombre'] ?? '') . ' ' . ($venta['cli_apellido'] ?? ''));
if (empty($nombre_cliente)) $nombre_cliente = 'Cliente';

$email_destino = !empty($email_override) ? $email_override : trim($venta['cli_email'] ?? '');

if (empty($email_destino) || !filter_var($email_destino, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'mensaje' => 'Ingresá una dirección de correo válida para el envío.']);
    exit;
}

$num_factura = str_pad($venta['id_ventas'], 6, '0', STR_PAD_LEFT);
$total_fmt   = number_format($venta['total'], 2, ',', '.');
$fecha_fmt   = date('d/m/Y H:i', strtotime($venta['fecha_hora']));

// 2. Traer los ítems de ventas_detalle para armar la tabla del mail
$q_items = "SELECT vd.*, a.nombre 
            FROM ventas_detalle vd 
            LEFT JOIN articulos a ON vd.id_articulos = a.id_articulos 
            WHERE vd.id_ventas = $id_venta";
$res_items = mysqli_query($conexion, $q_items);

$filas_items = '';
if ($res_items) {
    while ($it = mysqli_fetch_assoc($res_items)) {
        $p_u = number_format($it['precio_unitario'], 2, ',', '.');
        $sub = number_format($it['subtotal'], 2, ',', '.');
        $filas_items .= "
            <tr style='border-bottom: 1px solid #EAE1D0;'>
                <td style='padding: 8px;'>{$it['nombre']}</td>
                <td style='padding: 8px; text-align: center;'>{$it['cantidad']}</td>
                <td style='padding: 8px; text-align: right;'>$ {$p_u}</td>
                <td style='padding: 8px; text-align: right; font-weight: bold;'>$ {$sub}</td>
            </tr>";
    }
}

// 3. Estructura HTML estilizada del comprobante por correo
$cuerpoHtml = "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 25px; border: 1px solid #EAE1D0; border-radius: 12px; background-color: #FFFFFF;'>
    <div style='text-align: center; margin-bottom: 25px;'>
        <h2 style='color: #2E7D32; margin-bottom: 2px; font-size: 26px;'>DecoStore</h2>
        <span style='color: #8D6E63; font-size: 13px;'>Mobiliario & Diseño de Interiores &bull; San Justo, Santa Fe</span>
    </div>

    <div style='background-color: #FDFCF6; padding: 18px; border-radius: 8px; border: 1px solid #EAE1D0; margin-bottom: 20px;'>
        <div style='display: flex; justify-content: space-between; margin-bottom: 8px;'>
            <span style='color: #3E2723; font-size: 14px;'>Cliente: <strong>{$nombre_cliente}</strong></span>
            <span style='color: #3E2723; font-size: 14px; float: right;'>Comprobante: <strong>#{$num_factura}</strong></span>
        </div>
        <div style='color: #795548; font-size: 13px;'>Fecha: {$fecha_fmt}</div>
    </div>

    <table style='width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 20px;'>
        <thead>
            <tr style='background-color: #F4F1EA; color: #3E2723;'>
                <th style='padding: 8px; text-align: left;'>Artículo</th>
                <th style='padding: 8px; text-align: center;'>Cant.</th>
                <th style='padding: 8px; text-align: right;'>Unitario</th>
                <th style='padding: 8px; text-align: right;'>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            {$filas_items}
        </tbody>
        <tfoot>
            <tr>
                <td colspan='3' style='padding: 12px 8px; text-align: right; font-weight: bold; font-size: 15px; color: #3E2723;'>TOTAL FACTURADO:</td>
                <td style='padding: 12px 8px; text-align: right; font-weight: bold; font-size: 17px; color: #2E7D32;'>$ {$total_fmt}</td>
            </tr>
        </tfoot>
    </table>

    <div style='text-align: center; padding-top: 15px; border-top: 1px solid #EAE1D0; color: #8D6E63; font-size: 12px;'>
        Este comprobante fue emitido de manera electrónica por el sistema de gestión DecoStore.
    </div>
</div>";

// 4. Intentar envío
$resultado = enviarCorreoComprobante($email_destino, $nombre_cliente, $num_factura, $cuerpoHtml);
$email_sql = mysqli_real_escape_string($conexion, $email_destino);

if ($resultado['exito']) {
    $q_log = "INSERT INTO ventas_email (id_ventas, email, fecha, estado, intentos, error) 
              VALUES ($id_venta, '$email_sql', NOW(), 'ENVIADA', 1, NULL)";
    mysqli_query($conexion, $q_log);

    echo json_encode([
        'status'  => 'success', 
        'mensaje' => "Comprobante enviado correctamente a $email_destino",
        'fecha'   => date('d/m/Y H:i')
    ]);
} else {
    $err_sql = mysqli_real_escape_string($conexion, $resultado['error']);
    $q_log = "INSERT INTO ventas_email (id_ventas, email, fecha, estado, intentos, error) 
              VALUES ($id_venta, '$email_sql', NOW(), 'ERROR', 1, '$err_sql')";
    mysqli_query($conexion, $q_log);

    echo json_encode([
        'status'  => 'error', 
        'mensaje' => "Fallo de conexión SMTP: " . $resultado['error']
    ]);
}
exit;