<?php
// php/test_arca.php
require_once 'arca_service.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Tu CUIT registrado en WSASS
    $cuit = "27412878278"; 
    
    $arca = new ArcaService($cuit);

    // Consultamos el último número para Punto de Venta 1, Factura B (tipo 6) o Factura C (tipo 11)
    $ptoVta = 1;
    $tipoCbte = 6; // 6 = Factura B, 11 = Factura C

    $ultimo = $arca->getUltimoComprobante($ptoVta, $tipoCbte);

    echo json_encode([
        'status' => 'success',
        'mensaje' => 'Conexión con ARCA exitosa',
        'punto_venta' => $ptoVta,
        'tipo_comprobante' => $tipoCbte,
        'ultimo_comprobante' => $ultimo,
        'siguiente_a_emitir' => $ultimo + 1
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'mensaje' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}