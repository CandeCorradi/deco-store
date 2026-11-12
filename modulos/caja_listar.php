<?php
// modulos/caja_listar.php

// Capturar fechas del filtro si fueron enviadas
$fecha_desde = $_GET['fecha_desde'] ?? '';
$fecha_hasta = $_GET['fecha_hasta'] ?? '';

// 1. Filtro exclusivo para las consultas de totales (tabla caja directa)
$where_caja = "WHERE esta_eliminado = 0";
if (!empty($fecha_desde) && !empty($fecha_hasta)) {
    $where_caja .= " AND DATE(fecha) BETWEEN '$fecha_desde' AND '$fecha_hasta'";
}

// Consultas de Totales
$q_ingresos = "SELECT SUM(importe) AS total FROM caja $where_caja AND importe > 0";
$r_ingresos = mysqli_query($conexion, $q_ingresos);
$f_ingresos = mysqli_fetch_assoc($r_ingresos);
$total_ingresos = $f_ingresos['total'] ?? 0;

$q_egresos = "SELECT SUM(importe) AS total FROM caja $where_caja AND importe < 0";
$r_egresos = mysqli_query($conexion, $q_egresos);
$f_egresos = mysqli_fetch_assoc($r_egresos);
$total_egresos = abs($f_egresos['total'] ?? 0);

$saldo_neto = $total_ingresos - $total_egresos;

// Rentabilidad dinámica
$rentabilidad_total = $total_ingresos * 0.35;

// 2. Filtro exclusivo para la tabla principal (con el alias c.)
$where_principal = "WHERE c.esta_eliminado = 0";
if (!empty($fecha_desde) && !empty($fecha_hasta)) {
    $where_principal .= " AND DATE(c.fecha) BETWEEN '$fecha_desde' AND '$fecha_hasta'";
}

// Consulta principal con JOIN
$query = "SELECT c.*, cu.cuenta AS nombre_cuenta 
          FROM caja c 
          INNER JOIN cuentas cu ON c.id_cuenta = cu.id_cuenta 
          $where_principal 
          ORDER BY c.fecha DESC, c.id_caja DESC";
$resultado = mysqli_query($conexion, $query);
?>

<!-- Formulario de Filtro por Fecha -->
<div class="card shadow-sm border-0 p-3 mb-4" style="border-radius: 10px; background-color: #FFFFFF;">
    <form method="GET" action="index.php" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="caja">
        <div class="col-md-4">
            <label class="form-label small text-muted fw-bold">Fecha Desde</label>
            <input type="date" name="fecha_desde" class="form-control" value="<?php echo htmlspecialchars($fecha_desde); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted fw-bold">Fecha Hasta</label>
            <input type="date" name="fecha_hasta" class="form-control" value="<?php echo htmlspecialchars($fecha_hasta); ?>">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-success w-100 fw-semibold">
                <i class="fas fa-filter me-1"></i> Filtrar
            </button>
            <a href="index.php?page=caja" class="btn btn-outline-secondary" title="Limpiar Filtros">
                <i class="fas fa-sync-alt"></i>
            </a>
        </div>
    </form>
</div>

<!-- Tarjetas de Totales y Rentabilidad -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Total Ingresos</div>
            <div class="h4 fw-bold text-success mt-1">$<?php echo number_format($total_ingresos, 2, ',', '.'); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #C1392B !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Total Egresos</div>
            <div class="h4 fw-bold text-danger mt-1">$<?php echo number_format($total_egresos, 2, ',', '.'); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid <?php echo $saldo_neto >= 0 ? '#2E7D32' : '#C1392B'; ?> !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Saldo de Caja</div>
            <div class="h4 fw-bold mt-1 <?php echo $saldo_neto >= 0 ? 'text-success' : 'text-danger'; ?>">
                $<?php echo number_format($saldo_neto, 2, ',', '.'); ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #0d6efd !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Rentabilidad</div>
            <div class="h4 fw-bold text-primary mt-1">$<?php echo number_format($rentabilidad_total, 2, ',', '.'); ?></div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #3E2723;">
            <i class="fas fa-cash-register me-2 text-success"></i>Control de Caja Diaria
        </h4>
        <p class="text-muted small mb-0">Visualización de flujos de dinero y movimientos de caja ordenados de forma descendente.</p>
    </div>

    <!-- Contenedor con botón nuevo movimiento -->
    <div class="d-flex align-items-center gap-2">
        <a href="index.php?page=caja_agregar" class="btn btn-success fw-semibold">
            <i class="fas fa-plus me-1"></i> Nuevo Movimiento
        </a>
    </div>
</div>

<div class="main-card p-4">
    <div class="table-responsive">
        <table id="tabla-caja-diaria" class="table table-hover align-middle mb-0" style="width:100%" data-tabla="caja">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Fecha / Hora</th>
                    <th>Cuenta</th>
                    <th>Detalle / Motivo</th>
                    <th>Importe</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr data-id="<?php echo $fila['id_caja']; ?>">
                            <td>#<?php echo $fila['id_caja']; ?></td>
                            <td class="small"><?php echo date('d/m/Y H:i', strtotime($fila['fecha'])); ?></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?php echo htmlspecialchars($fila['nombre_cuenta'] ?? 'Efectivo'); ?>
                                </span>
                            </td>
                            <td class="editable fw-semibold" data-columna="detalle">
                                <?php echo htmlspecialchars($fila['detalle']); ?>
                            </td>
                            <td class="editable fw-bold <?php echo $fila['importe'] >= 0 ? 'text-success' : 'text-danger'; ?>" data-columna="importe">
                                <?php echo ($fila['importe'] < 0 ? '-' : '') . '$' . number_format(abs($fila['importe']), 2, ',', '.'); ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=caja_editar&id=<?php echo $fila['id_caja']; ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-borrar-estetico" 
                                       data-url="index.php?page=caja_borrar&id=<?php echo $fila['id_caja']; ?>" 
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay registros de caja cargados para este período.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-caja-diaria').DataTable({
            "pageLength": 10,
            "ordering": true,
            "order": [[1, "desc"]],
            "destroy": true,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "search": "Buscar:",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });
    }
});
</script>