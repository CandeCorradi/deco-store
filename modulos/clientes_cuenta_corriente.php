<?php
// modulos/clientes_cuenta_corriente.php
$id_cliente = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. Datos del cliente
$q_cliente = "SELECT c.*, p.nombre AS nombre_provincia 
              FROM clientes c 
              LEFT JOIN provincias p ON c.id_provincias = p.id_provincias 
              WHERE c.id_clientes = $id_cliente AND c.esta_eliminado = 0";
$r_cliente = mysqli_query($conexion, $q_cliente);
$cliente   = ($r_cliente) ? mysqli_fetch_assoc($r_cliente) : null;

if (!$cliente) {
    echo "<div class='alert alert-danger shadow-sm'>Cliente no encontrado o dado de baja.</div>";
    return;
}

// 2. Comprobantes Fiados (Debe)
// Buscamos todas las ventas asociadas al cliente cuya forma de pago sea Cta Cte (id 7 o por nombre)
$q_ctacte = "SELECT DISTINCT v.id_ventas, v.fecha_hora, v.total, 
                    COALESCE(mp.nombre, 'Cuenta Corriente') AS nombre_metodo 
             FROM ventas v 
             INNER JOIN ventas_pagos vp ON v.id_ventas = vp.id_ventas
             LEFT JOIN metodos_pago mp ON vp.id_metodos_pago = mp.id_metodos_pago 
             WHERE v.id_clientes = $id_cliente 
               AND v.esta_eliminado = 0 
               AND (vp.id_metodos_pago = 7 OR LOWER(mp.nombre) LIKE '%cta%' OR LOWER(mp.nombre) LIKE '%corriente%')
             ORDER BY v.fecha_hora DESC";

$r_ctacte = mysqli_query($conexion, $q_ctacte);

$total_fiado = 0;
$ventas_fiadas = [];

if ($r_ctacte) {
    while ($row = mysqli_fetch_assoc($r_ctacte)) {
        $total_fiado += floatval($row['total']);
        $ventas_fiadas[] = $row;
    }
}

// 3. Pagos Registrados (Haber)
$q_pagos = "SELECT cc.*, mp.nombre AS metodo_nombre, u.nombre_usuario 
            FROM cobros_ctacte cc 
            LEFT JOIN metodos_pago mp ON cc.id_metodos_pago = mp.id_metodos_pago 
            LEFT JOIN usuarios u ON cc.id_usuarios = u.id_usuarios 
            WHERE cc.id_clientes = $id_cliente AND cc.esta_eliminado = 0 
            ORDER BY cc.fecha_pago DESC";
$r_pagos = mysqli_query($conexion, $q_pagos);

$total_pagado = 0;
$lista_pagos = [];
if ($r_pagos) {
    while ($p = mysqli_fetch_assoc($r_pagos)) {
        $total_pagado += floatval($p['monto']);
        $lista_pagos[] = $p;
    }
}

$saldo_deudor = $total_fiado - $total_pagado;
$r_metodos = mysqli_query($conexion, "SELECT * FROM metodos_pago WHERE esta_eliminado = 0 AND id_metodos_pago != 7 ORDER BY id_metodos_pago ASC");
?>

<!-- Encabezado -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="color: #3E2723;">
            <i class="fas fa-file-invoice-dollar me-2 text-warning"></i>Estado de Cuenta Corriente
        </h3>
        <p class="text-muted small mb-0">Detalle de facturación a crédito, cobranzas y saldo acumulado del cliente.</p>
    </div>
</div>

<!-- Tarjetas de Información -->
<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="card p-3 border-0 shadow-sm h-100" style="background: #FFFFFF; border-radius: 12px; border: 1px solid #EAE1D0;">
            <div class="text-muted text-uppercase fw-bold small mb-2">Ficha del Cliente</div>
            <div class="h5 fw-bold text-dark mb-1">
                <i class="fas fa-user-circle text-muted me-1"></i> 
                <?php echo htmlspecialchars(trim(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellido'] ?? ''))); ?>
            </div>
            <div class="small text-muted mb-1">
                <i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($cliente['telefono'] ?? 'Sin registrar'); ?>
            </div>
            <div class="small text-muted mb-1">
                <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($cliente['email'] ?? 'Sin registrar'); ?>
            </div>
            <div class="small text-muted">
                <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($cliente['nombre_provincia'] ?? 'Santa Fe'); ?>
            </div>
        </div>
    </div>

    <!-- Saldo y Acción de Pago -->
    <div class="col-md-5">
        <div class="card p-3 border-0 shadow-sm h-100 d-flex flex-column justify-content-center align-items-center" 
             style="background: #FFF9E6; border: 2px dashed #F59E0B !important; border-radius: 12px;">
            <span class="text-muted text-uppercase fw-bold small">Saldo Total Deudor</span>
            <div class="display-6 fw-bold text-danger mt-1">
                $ <?php echo number_format($saldo_deudor, 2, ',', '.'); ?>
            </div>
            
            <div class="d-flex align-items-center gap-2 mt-2">
                <?php if ($saldo_deudor > 0): ?>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">Pendiente de Cobro</span>
                    <button type="button" class="btn btn-success btn-sm fw-bold px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarPago">
                        <i class="fas fa-hand-holding-usd me-1"></i> Registrar Pago
                    </button>
                <?php else: ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">Cuenta al Día</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Pago de Cta Cte -->
<div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid #EAE1D0;">
            <form action="php/clientes_cobro_guardar.php" method="POST">
                <input type="hidden" name="id_clientes" value="<?php echo $id_cliente; ?>">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" style="color: #3E2723;">
                        <i class="fas fa-cash-register text-success me-2"></i>Cobro de Cuenta Corriente
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="alert alert-light border small text-muted mb-3">
                        Cliente: <strong><?php echo htmlspecialchars(trim($cliente['nombre'] . ' ' . $cliente['apellido'])); ?></strong><br>
                        Deuda pendiente: <strong class="text-danger">$ <?php echo number_format($saldo_deudor, 2, ',', '.'); ?></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Monto a Cobrar ($) (*)</label>
                        <input type="number" name="monto" class="form-control form-control-lg fw-bold text-success" 
                               value="<?php echo max(0, $saldo_deudor); ?>" step="0.01" min="0.01" max="<?php echo max(0, $saldo_deudor); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Forma de Ingreso (*)</label>
                        <select name="id_metodos_pago" class="form-select" required>
                            <?php while ($mp = mysqli_fetch_assoc($r_metodos)): ?>
                                <option value="<?php echo $mp['id_metodos_pago']; ?>">
                                    <?php echo htmlspecialchars($mp['nombre']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="form-text small text-muted">Este importe ingresará automáticamente a la Caja Diaria.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Concepto / Observación</label>
                        <input type="text" name="concepto" class="form-control form-control-sm" value="Cancelación / Pago a cuenta de saldo pendiente">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-3">
                        <i class="fas fa-check-circle me-1"></i> Confirmar e Imprimir Recibo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pestañas de Comprobantes y Pagos -->
<div class="main-card p-4 shadow-sm" style="background: #FFFFFF; border-radius: 12px; border: 1px solid #EAE1D0;">
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="tab-deuda-tab" data-bs-toggle="pill" data-bs-target="#tab-deuda" type="button" role="tab">
                <i class="fas fa-receipt me-1"></i> Comprobantes Fiados (<?php echo count($ventas_fiadas); ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="tab-pagos-tab" data-bs-toggle="pill" data-bs-target="#tab-pagos" type="button" role="tab">
                <i class="fas fa-hand-holding-usd me-1"></i> Historial de Cobros Recibidos (<?php echo count($lista_pagos); ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <!-- 1. Pestaña de Ventas Fiadas -->
        <div class="tab-pane fade show active" id="tab-deuda" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Venta</th>
                            <th>Fecha y Hora</th>
                            <th>Condición</th>
                            <th class="text-end">Importe Fiado</th>
                            <th class="text-center">Comprobante</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ventas_fiadas)): ?>
                            <?php foreach ($ventas_fiadas as $v): ?>
                                <tr>
                                    <td class="fw-bold text-dark">#<?php echo $v['id_ventas']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($v['fecha_hora'])); ?></td>
                                    <td><span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Cuenta Corriente</span></td>
                                    <td class="text-end fw-bold text-danger">$ <?php echo number_format($v['total'], 2, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <a href="index.php?page=ventas_recibo&id=<?php echo $v['id_ventas']; ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Ver comprobante de venta">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Este cliente no registra compras en cuenta corriente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Pestaña de Pagos Realizados -->
        <div class="tab-pane fade" id="tab-pagos" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Recibo Pago</th>
                            <th>Fecha Cobro</th>
                            <th>Forma de Pago</th>
                            <th>Concepto</th>
                            <th class="text-end">Monto Cobrado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($lista_pagos)): ?>
                            <?php foreach ($lista_pagos as $p): ?>
                                <tr>
                                    <td class="fw-bold text-dark">#REC-<?php echo str_pad($p['id_cobros_ctacte'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($p['fecha_pago'])); ?></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($p['metodo_nombre'] ?? 'Efectivo'); ?></span></td>
                                    <td class="text-muted small"><?php echo htmlspecialchars($p['concepto']); ?></td>
                                    <td class="text-end fw-bold text-success">$ <?php echo number_format($p['monto'], 2, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <a href="index.php?page=cobro_recibo&id=<?php echo $p['id_cobros_ctacte']; ?>" class="btn btn-sm btn-outline-primary py-0 px-2">
                                            <i class="fas fa-print me-1"></i> Recibo
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay pagos registrados para este cliente todavía.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>