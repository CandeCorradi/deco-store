<?php
// php/emitir_factura_test.php
require_once __DIR__ . '/arca_service.php';
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $cuit = "27412878278"; 
    $arca = new ArcaService($cuit);

    $ptoVta = 1;
    $totalVenta = 1210.00; // $1000 neto + $210 IVA

    // 1. Emitir en ARCA/AFIP
    $factura = $arca->emitirFacturaB($ptoVta, $totalVenta);

    // 2. Formatear fechas para MySQL
    $fechaVtoSql  = date('Y-m-d', strtotime($factura['cae_vto']));
    $fechaCbteSql = date('Y-m-d', strtotime($factura['fecha']));

    // 3. Preparar variables numéricas y textos
    $idVenta        = 1; // Para prueba, o podés vincular un id_venta existente
    $ptoVtaVal      = (int)$factura['pto_vta'];
    $cbteTipoVal    = 6; // Factura B
    $cbteNroVal     = (int)$factura['cbte_nro'];
    $conceptoVal    = 1; // Productos
    $docTipoVal     = 99; // Consumidor Final
    $docNroVal      = "'0'";
    $condIvaVal     = 5; // Consumidor Final
    $impTotalVal    = (float)$factura['imp_total'];
    $impNetoVal     = round($impTotalVal / 1.21, 2);
    $impIvaVal      = round($impTotalVal - $impNetoVal, 2);
    $caeVal         = "'" . mysqli_real_escape_string($conexion, $factura['cae']) . "'";
    $resultadoVal   = "'" . mysqli_real_escape_string($conexion, $factura['resultado']) . "'";

    // 4. Inserción con tus columnas exactas
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
        throw new Exception("Error al insertar en la base de datos: " . mysqli_error($conexion));
    }

    $idInsertado = mysqli_insert_id($conexion);

    echo json_encode([
        'status'     => 'success',
        'mensaje'    => 'Factura emitida en ARCA y registrada en la base de datos con éxito',
        'factura'    => $factura,
        'id_factura' => $idInsertado
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'mensaje' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}