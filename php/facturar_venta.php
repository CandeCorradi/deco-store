<?php
// php/facturar_venta.php
require_once __DIR__ . '/arca_service.php';
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Acepta id_ventas o id_venta según cómo venga en el POST
    $idVenta = 0;
    if (isset($_POST['id_ventas']) && (int)$_POST['id_ventas'] > 0) {
        $idVenta = (int)$_POST['id_ventas'];
    } elseif (isset($_POST['id_venta']) && (int)$_POST['id_venta'] > 0) {
        $idVenta = (int)$_POST['id_venta'];
    }

    if ($idVenta <= 0) {
        throw new Exception("ID de venta inválido: no se recibió un número de venta válido.");
    }

    // 1. Verificar si esta venta ya tiene una factura emitida
    $checkSql = "SELECT id_factura, cae, cbte_nro FROM ventas_facturas WHERE id_venta = $idVenta LIMIT 1";
    $checkRes = mysqli_query($conexion, $checkSql);
    if ($checkRes && mysqli_num_rows($checkRes) > 0) {
        $facturaExistente = mysqli_fetch_assoc($checkRes);
        throw new Exception("Esta venta ya fue facturada (Comprobante N° {$facturaExistente['cbte_nro']} - CAE: {$facturaExistente['cae']}).");
    }

    // 2. Obtener el total de la venta de tu tabla ventas usando id_ventas
    $ventaSql = "SELECT total FROM ventas WHERE id_ventas = $idVenta LIMIT 1";
    $ventaRes = mysqli_query($conexion, $ventaSql);
    if (!$ventaRes || mysqli_num_rows($ventaRes) === 0) {
        throw new Exception("No se encontró la venta #$idVenta en la base de datos.");
    }
    $venta = mysqli_fetch_assoc($ventaRes);
    $totalVenta = (float)$venta['total'];

    if ($totalVenta <= 0) {
        throw new Exception("El importe de la venta debe ser mayor a 0.");
    }

    // 3. Emitir comprobante en ARCA
    $cuit = "27412878278";
    $arca = new ArcaService($cuit);
    $ptoVta = 1;

    $factura = $arca->emitirFacturaB($ptoVta, $totalVenta);

    // 4. Formatear y preparar campos
    $fechaVtoSql  = date('Y-m-d', strtotime($factura['cae_vto']));
    $fechaCbteSql = date('Y-m-d', strtotime($factura['fecha']));

    $ptoVtaVal    = (int)$factura['pto_vta'];
    $cbteTipoVal  = 6; // Factura B
    $cbteNroVal   = (int)$factura['cbte_nro'];
    $conceptoVal  = 1;
    $docTipoVal   = 99; // Consumidor Final
    $docNroVal    = "'0'";
    $condIvaVal   = 5;
    $impTotalVal  = (float)$factura['imp_total'];
    $impNetoVal   = round($impTotalVal / 1.21, 2);
    $impIvaVal    = round($impTotalVal - $impNetoVal, 2);
    $caeVal       = "'" . mysqli_real_escape_string($conexion, $factura['cae']) . "'";
    $resultadoVal = "'" . mysqli_real_escape_string($conexion, $factura['resultado']) . "'";

    // 5. Insertar en ventas_facturas
    $sql = "INSERT INTO ventas_facturas (
                id_venta,
                pto_vta,
                cbte_tipo,
                cbte_nro,
                cbte_fecha,
                concepto,
                doc_tipo,
                doc_nro,
                condicion_iva_receptor_id,
                imp_total,
                imp_neto,
                imp_tot_conc,
                imp_op_ex,
                imp_iva,
                imp_trib,
                mon_id,
                cae,
                cae_vto,
                resultado,
                fecha_registro
            ) VALUES (
                $idVenta,
                $ptoVtaVal,
                $cbteTipoVal,
                $cbteNroVal,
                '$fechaCbteSql',
                $conceptoVal,
                $docTipoVal,
                $docNroVal,
                $condIvaVal,
                $impTotalVal,
                $impNetoVal,
                0,
                0,
                $impIvaVal,
                0,
                'PES',
                $caeVal,
                '$fechaVtoSql',
                $resultadoVal,
                NOW()
            )";

    if (!mysqli_query($conexion, $sql)) {
        throw new Exception("Error al guardar en base de datos: " . mysqli_error($conexion));
    }

    echo json_encode([
        'status'  => 'success',
        'mensaje' => 'Factura electrónica emitida y registrada exitosamente.',
        'factura' => [
            'cbte_nro' => str_pad($cbteNroVal, 8, "0", STR_PAD_LEFT),
            'pto_vta'  => str_pad($ptoVtaVal, 5, "0", STR_PAD_LEFT),
            'cae'      => $factura['cae'],
            'cae_vto'  => date('d/m/Y', strtotime($factura['cae_vto']))
        ]
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'mensaje' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}