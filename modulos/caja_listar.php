<?php
// modulos/caja_listar.php

// 1. Consultas para las tarjetas de totales basadas en la nueva estructura (Importes +/-)
// Total Ingresos (Importes mayores a 0)
$q_ingresos = "SELECT SUM(importe) AS total FROM caja WHERE importe > 0 AND esta_eliminado = 0";
$r_ingresos = mysqli_query($conexion, $q_ingresos);
$f_ingresos = mysqli_fetch_assoc($r_ingresos);
$total_ingresos = $f_ingresos['total'] ?? 0;

// Total Egresos (Importes menores a 0. Usamos ABS para mostrarlo positivo en la tarjeta)
$q_egresos = "SELECT SUM(importe) AS total FROM caja WHERE importe < 0 AND esta_eliminado = 0";
$r_egresos = mysqli_query($conexion, $q_egresos);
$f_egresos = mysqli_fetch_assoc($r_egresos);
$total_egresos = abs($f_egresos['total'] ?? 0);

// Saldo Neto Total
$saldo_neto = $total_ingresos - $total_egresos;

// 2. Consulta principal cruzando 'caja' con la tabla simple 'cuentas'
$query = "SELECT c.*, cu.cuenta AS nombre_cuenta 
          FROM caja c
          INNER JOIN cuentas cu ON c.id_cuenta = cu.id_cuenta
          WHERE c.esta_eliminado = 0 
          ORDER BY c.fecha DESC";
$resultado = mysqli_query($conexion, $query);
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Total Ingresos</div>
            <div class="h3 fw-bold text-success mt-1">$<?php echo number_format($total_ingresos, 2, ',', '.'); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #C1392B !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Total Egresos</div>
            <div class="h3 fw-bold text-danger mt-1">$<?php echo number_format($total_egresos, 2, ',', '.'); ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid <?php echo $saldo_neto >= 0 ? '#2E7D32' : '#C1392B'; ?> !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Saldo de Caja</div>
            <div class="h3 fw-bold mt-1 <?php echo $saldo_neto >= 0 ? 'text-success' : 'text-danger'; ?>">
                $<?php echo number_format($saldo_neto, 2, ',', '.'); ?>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-cash-register me-2 text-success"></i>Control de Caja Diaria</h3>
        <p class="text-muted small mb-0">Visualización de flujos de dinero y movimientos de caja.</p>
    </div>
    <a href="index.php?page=caja_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nuevo Movimiento
    </a>
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
                                    <?php echo htmlspecialchars($fila['nombre_cuenta']); ?>
                                </span>
                            </td>
                            <td class="editable fw-semibold" data-columna="detalle">
                                    <?php echo htmlspecialchars($fila['detalle']); ?>
                                </td>

                                <td class="editable fw-bold <?php echo $fila['importe'] >= 0 ? 'text-success' : 'text-danger'; ?>" data-columna="importe">
                                    $<?php echo number_format(abs($fila['importe']), 2, ',', '.'); ?>
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
                        <td colspan="6" class="text-center text-muted py-4">No hay registros de caja cargados aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        // Inicializamos con la configuración limpia sobre el ID
        $('#tabla-caja-diaria').DataTable({
            "pageLength": 10,
            "ordering": true,
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