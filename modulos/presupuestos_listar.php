<?php
// modulos/presupuestos_listar.php

$q_totales = "SELECT COUNT(*) AS total FROM presupuestos WHERE esta_eliminado = 0";
$r_totales = mysqli_query($conexion, $q_totales);
$total_p = mysqli_fetch_assoc($r_totales)['total'] ?? 0;

$query = "SELECT p.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido, u.nombre_usuario 
          FROM presupuestos p
          LEFT JOIN clientes c ON p.id_clientes = c.id_clientes
          LEFT JOIN usuarios u ON p.id_usuarios = u.id_usuarios
          WHERE p.esta_eliminado = 0 ORDER BY p.fecha_creacion DESC";
$resultado = mysqli_query($conexion, $query);
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Presupuestos Cargados</div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_p; ?> Registrados</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-file-invoice-dollar me-2 text-success"></i>Presupuestos</h3>
        <p class="text-muted small mb-0">Cotizaciones emitidas para clientes con sus fechas límite.</p>
    </div>
    <a href="index.php?page=presupuestos_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nuevo Presupuesto
    </a>
</div>

<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-presupuestos" class="table table-hover align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th>Emisión</th>
                    <th>Vencimiento</th>
                    <th>Cliente</th>
                    <th>Atendido Por</th>
                    <th>Total</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center" style="width: 15%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($resultado)): ?>
                        <?php 
                            $cli = trim(($p['cli_nombre'] ?? '') . ' ' . ($p['cli_apellido'] ?? ''));
                            if (empty($cli)) $cli = 'Cliente General';
                        ?>
                        <tr>
                            <td class="fw-bold text-dark">#<?php echo $p['id_presupuestos']; ?></td>
                            <td class="small"><?php echo date('d/m/Y H:i', strtotime($p['fecha_creacion'])); ?></td>
                            <td class="small text-muted"><?php echo $p['fecha_limite'] ? date('d/m/Y', strtotime($p['fecha_limite'])) : '-'; ?></td>
                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($cli); ?></td>
                            <td><span class="badge bg-light text-dark border"><i class="fas fa-user me-1 text-muted"></i><?php echo htmlspecialchars($p['nombre_usuario'] ?? 'Sistema'); ?></span></td>
                            <td class="fw-bold text-success">$<?php echo number_format($p['cantidad_total'], 2, ',', '.'); ?></td>
                            <td class="text-center">
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">
                                    <?php echo htmlspecialchars($p['estado'] ?? 'Pending'); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=presupuestos_editar&id=<?php echo $p['id_presupuestos']; ?>" class="btn btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="btn btn-outline-danger btn-borrar-estetico" data-url="index.php?page=presupuestos_borrar&id=<?php echo $p['id_presupuestos']; ?>"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay presupuestos cargados aún.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-presupuestos').DataTable({ "pageLength": 10, "destroy": true, "language": { "search": "Buscar:" } });
    }
});
</script>