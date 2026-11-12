<?php
// php/clientes_cobro_guardar.php
require_once __DIR__ . '/conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente  = intval($_POST['id_clientes'] ?? 0);
    $monto       = floatval($_POST['monto'] ?? 0);
    $id_metodo   = intval($_POST['id_metodos_pago'] ?? 1);
    $concepto    = trim($_POST['concepto'] ?? 'Pago a cuenta de saldo pendiente');
    $id_usuario  = isset($_SESSION['id_usuarios']) ? intval($_SESSION['id_usuarios']) : 1;

    if ($id_cliente <= 0 || $monto <= 0) {
        header("Location: ../index.php?page=clientes_cuenta_corriente&id=$id_cliente&error=monto_invalido");
        exit;
    }

    mysqli_begin_transaction($conexion);

    try {
        // 1. Obtener nombre del método y del cliente para el detalle de caja
        $r_m = mysqli_query($conexion, "SELECT nombre FROM metodos_pago WHERE id_metodos_pago = $id_metodo");
        $metodo_nom = ($r_m && $row_m = mysqli_fetch_assoc($r_m)) ? $row_m['nombre'] : 'Efectivo';

        $r_c = mysqli_query($conexion, "SELECT nombre, apellido FROM clientes WHERE id_clientes = $id_cliente");
        $cli_nom = ($r_c && $row_c = mysqli_fetch_assoc($r_c)) ? trim($row_c['nombre'] . ' ' . $row_c['apellido']) : "Cliente #$id_cliente";

        // 2. Ingresar a Caja Diaria (id_cuenta = 2 si es MP/Transferencia, sino 1)
        $id_cuenta = ($id_metodo == 4) ? 2 : 1;
        $detalle_caja = "Cobro Cta Cte: $cli_nom ($metodo_nom)";
        
        $q_caja = "INSERT INTO caja (fecha, detalle, importe, id_cuenta, esta_eliminado) 
                   VALUES (NOW(), '$detalle_caja', $monto, $id_cuenta, 0)";
        mysqli_query($conexion, $q_caja);
        $id_caja_generada = mysqli_insert_id($conexion);

        // 3. Registrar el Cobro formal
        $concepto_sql = mysqli_real_escape_string($conexion, $concepto);
        $q_cobro = "INSERT INTO cobros_ctacte (id_clientes, id_usuarios, fecha_pago, monto, id_metodos_pago, concepto, id_caja, esta_eliminado) 
                    VALUES ($id_cliente, $id_usuario, NOW(), $monto, $id_metodo, '$concepto_sql', $id_caja_generada, 0)";
        mysqli_query($conexion, $q_cobro);
        $id_cobro_generado = mysqli_insert_id($conexion);

        mysqli_commit($conexion);

        // Redirigir directamente al nuevo Recibo de Cobranza emitido
        header("Location: ../index.php?page=cobro_recibo&id=$id_cobro_generado");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conexion);
        header("Location: ../index.php?page=clientes_cuenta_corriente&id=$id_cliente&error=fallo_transaccion");
        exit;
    }
}