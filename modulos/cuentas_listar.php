<?php
// modulos/cuentas_listar.php

$q_totales = "SELECT COUNT(*) AS total FROM cuentas WHERE esta_eliminado = 0";
$r_totales = mysqli_query($conexion, $q_totales);
$f_totales = mysqli_fetch_assoc($r_totales);
$total_cuentas = $f_totales['total'] ?? 0;

$query = "SELECT * FROM cuentas WHERE esta_eliminado = 0 ORDER BY cuenta ASC";
$resultado = mysqli_query($conexion, $query);
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Cuentas Financieras</div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_cuentas; ?> Activas</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-wallet me-2 text-success"></i>Cuentas / Cajas</h3>
        <p class="text-muted small mb-0">Administrá las cuentas de origen o destino para los movimientos.</p>
    </div>
    <a href="index.php?page=cuentas_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nueva Cuenta
    </a>
</div>

<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-cuentas" class="table table-hover align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 15%;">Código</th>
                    <th>Nombre de la Cuenta</th>
                    <th class="text-center" style="width: 20%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($f = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?php echo $f['id_cuenta']; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($f['cuenta']); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=cuentas_editar&id=<?php echo $f['id_cuenta']; ?>" class="btn btn-outline-secondary" title="Editar"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="btn btn-outline-danger btn-borrar-estetico" data-url="index.php?page=cuentas_borrar&id=<?php echo $f['id_cuenta']; ?>" title="Eliminar"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center text-muted py-4">No hay cuentas cargadas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-cuentas').DataTable({ "pageLength": 10, "destroy": true, "language": { "search": "Buscar:" } });
    }
});
</script>