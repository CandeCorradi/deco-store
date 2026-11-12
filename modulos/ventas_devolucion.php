<?php
// modulos/ventas_devolucion.php

$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_usuarios = isset($_SESSION['id_usuarios']) ? intval($_SESSION['id_usuarios']) : 1;

// 1. Obtener la venta original
$q_venta = "SELECT v.*, c.nombre AS cliente_nombre, c.apellido AS cliente_apellido 
            FROM ventas v 
            INNER JOIN clientes c ON v.id_clientes = c.id_clientes 
            WHERE v.id_ventas = $id_venta AND v.esta_eliminado = 0";
$r_venta = mysqli_query($conexion, $q_venta);
$venta   = ($r_venta) ? mysqli_fetch_assoc($r_venta) : null;

if (!$venta) {
    echo "<div class='alert alert-danger shadow-sm'>Venta no encontrada o dada de baja.</div>";
    return;
}

// 2. Obtener los ítems vendidos
$q_items = "SELECT vd.*, a.nombre AS articulo_nombre 
            FROM ventas_detalle vd 
            INNER JOIN articulos a ON vd.id_articulos = a.id_articulos 
            WHERE vd.id_ventas = $id_venta";
$r_items = mysqli_query($conexion, $q_items);

// 3. Obtener formas de pago originales de esta venta
$q_pagos = "SELECT vp.*, mp.nombre AS metodo_nombre 
            FROM ventas_pagos vp 
            INNER JOIN metodos_pago mp ON vp.id_metodos_pago = mp.id_metodos_pago 
            WHERE vp.id_ventas = $id_venta AND vp.esta_eliminado = 0";
$r_pagos = mysqli_query($conexion, $q_pagos);
$pagos_originales = [];
while ($p = mysqli_fetch_assoc($r_pagos)) {
    $pagos_originales[] = $p;
}

// 4. Procesar el POST de la Devolución
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'procesar_devolucion') {
    $devolver_stock   = isset($_POST['devolver_stock']) ? 1 : 0;
    $id_metodo_reintegro = intval($_POST['id_metodos_pago']);
    $monto_reintegro  = floatval($_POST['monto_reintegro']);
    $motivo           = mysqli_real_escape_string($conexion, trim($_POST['motivo']));

    if ($monto_reintegro > 0 && $monto_reintegro <= floatval($venta['total'])) {
        mysqli_begin_transaction($conexion);

        try {
            // A. Registrar el pago en negativo en ventas_pagos (Trazabilidad)
            $monto_negativo = -$monto_reintegro;
            $q_pago_neg = "INSERT INTO ventas_pagos (id_ventas, id_metodos_pago, monto, fecha_pago, esta_eliminado) 
                           VALUES ($id_venta, $id_metodo_reintegro, $monto_negativo, NOW(), 0)";
            mysqli_query($conexion, $q_pago_neg);

            // B. Si es devolución de dinero real (NO Cta Cte = 7), impacta como egreso en CAJA
            if ($id_metodo_reintegro != 7) {
                $id_cuenta = ($id_metodo_reintegro == 4) ? 2 : 1; // Mercado Pago o Efectivo
                $detalle_caja = "Devolución / NC s/ Venta #$id_venta ($motivo)";
                $q_caja = "INSERT INTO caja (fecha, detalle, importe, id_cuenta, esta_eliminado) 
                           VALUES (NOW(), '$detalle_caja', $monto_negativo, $id_cuenta, 0)";
                mysqli_query($conexion, $q_caja);
            }

            // C. Si se devuelve mercadería física, reingresar a stock (IN)
            if ($devolver_stock && isset($_POST['items_cant'])) {
                foreach ($_POST['items_cant'] as $id_art => $cant_dev) {
                    $cant_dev = intval($cant_dev);
                    if ($cant_dev > 0) {
                        $id_art = intval($id_art);
                        $q_stock = "INSERT INTO movimientos_stock (id_productos, id_usuarios, tipo_movimiento, cantidad, fecha_movimiento, comentarios, esta_eliminado) 
                                    VALUES ($id_art, $id_usuarios, 'IN', $cant_dev, NOW(), 'Devolución Venta #$id_venta', 0)";
                        mysqli_query($conexion, $q_stock);
                    }
                }
            }

            mysqli_commit($conexion);
                header("Location: index.php?page=ventas&msg=dev_ok");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($conexion);
            $error = "Error al procesar la devolución: " . $e->getMessage();
        }
    } else {
        $error = "El monto a devolver debe ser mayor a 0 y no superar el total original ($" . number_format($venta['total'], 2, ',', '.') . ").";
    }
}

$r_metodos = mysqli_query($conexion, "SELECT id_metodos_pago, nombre FROM metodos_pago WHERE esta_eliminado = 0 ORDER BY id_metodos_pago ASC");
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-3"><?php echo $error; ?></div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="index.php?page=ventas" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> Volver a Ventas
        </a>
        <h3 class="fw-bold mb-1" style="color: #3E2723;">
            <i class="fas fa-undo-alt me-2 text-danger"></i>Registrar Devolución / Recibo Negativo
        </h3>
        <p class="text-muted small mb-0">Emisión de nota de crédito sobre Venta #<?php echo $id_venta; ?></p>
    </div>
</div>

<form method="POST" action="">
    <input type="hidden" name="accion" value="procesar_devolucion">

    <div class="row g-3 mb-4">
        <!-- Resumen de la Venta Original -->
        <div class="col-md-6">
            <div class="main-card p-3 shadow-sm h-100" style="background: #FFFFFF; border-radius: 10px; border: 1px solid #EAE1D0;">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Datos de la Factura Original</h6>
                <p class="mb-1 small"><strong>Cliente:</strong> <?php echo htmlspecialchars($venta['cliente_nombre'] . ' ' . $venta['cliente_apellido']); ?></p>
                <p class="mb-1 small"><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($venta['fecha_hora'])); ?></p>
                <p class="mb-1 small"><strong>Total Cobrado:</strong> <span class="fw-bold text-success">$<?php echo number_format($venta['total'], 2, ',', '.'); ?></span></p>
                
                <div class="small text-muted mt-2">
                    <strong>Pagos registrados:</strong>
                    <ul class="mb-0 ps-3">
                        <?php foreach ($pagos_originales as $po): ?>
                            <li><?php echo htmlspecialchars($po['metodo_nombre']); ?>: $<?php echo number_format($po['monto'], 2, ',', '.'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Condiciones del Reintegro -->
        <div class="col-md-6">
            <div class="main-card p-3 shadow-sm h-100" style="background: #FFFFFF; border-radius: 10px; border: 1px solid #EAE1D0;">
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Liquidación del Reintegro</h6>

                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Medio de Compensación / Devolución (*)</label>
                    <select name="id_metodos_pago" class="form-select form-select-sm" required>
                        <?php while ($mp = mysqli_fetch_assoc($r_metodos)): ?>
                            <option value="<?php echo $mp['id_metodos_pago']; ?>">
                                <?php echo ($mp['id_metodos_pago'] == 7) ? 'Descontar de Cuenta Corriente (Baja de deuda)' : 'Efectivo / ' . htmlspecialchars($mp['nombre']) . ' (Sale de caja)'; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Monto a Reintegrar (*)</label>
                    <input type="number" step="0.01" max="<?php echo $venta['total']; ?>" name="monto_reintegro" class="form-control form-control-sm fw-bold text-danger" value="<?php echo $venta['total']; ?>" required>
                    <small class="text-muted">Podés ajustar el monto para una devolución parcial.</small>
                </div>

                <div class="mb-2">
                    <label class="form-label small text-muted mb-1">Motivo / Observación</label>
                    <input type="text" name="motivo" class="form-control form-control-sm" placeholder="Ej: Falla en producto / Arrepentimiento" required>
                </div>
            </div>
        </div>
    </div>

    <!-- Reingreso de Mercadería a Inventario -->
    <div class="main-card p-3 shadow-sm mb-4" style="background: #FFFFFF; border-radius: 10px; border: 1px solid #EAE1D0;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold text-dark mb-0">Reingreso Físico de Stock</h6>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="devolver_stock" id="checkStock" value="1" checked>
                <label class="form-check-label small fw-semibold" for="checkStock">Devolver mercadería al stock (IN)</label>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th>Artículo</th>
                        <th class="text-center" style="width: 20%;">Cant. Vendida</th>
                        <th class="text-center" style="width: 25%;">Cant. a Reingresar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($it = mysqli_fetch_assoc($r_items)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($it['articulo_nombre']); ?></td>
                            <td class="text-center"><?php echo $it['cantidad']; ?></td>
                            <td class="text-center">
                                <input type="number" min="0" max="<?php echo $it['cantidad']; ?>" name="items_cant[<?php echo $it['id_articulos']; ?>]" class="form-control form-control-sm text-center mx-auto" style="width: 90px;" value="<?php echo $it['cantidad']; ?>">
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <button type="submit" class="btn btn-danger fw-bold px-4 py-2" onclick="return confirm('¿Confirmás la emisión de esta devolución/recibo negativo?');">
        <i class="fas fa-undo-alt me-1"></i> Confirmar Devolución
    </button>
</form>