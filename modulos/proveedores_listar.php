<?php
// modulos/proveedores_listar.php

$q_totales = "SELECT COUNT(*) AS total FROM proveedores WHERE esta_eliminado = 0";
$r_totales = mysqli_query($conexion, $q_totales);
$total_prov = mysqli_fetch_assoc($r_totales)['total'] ?? 0;

$query = "SELECT pr.*, p.nombre AS provincia 
          FROM proveedores pr 
          LEFT JOIN provincias p ON pr.id_provincias = p.id_provincias 
          WHERE pr.esta_eliminado = 0 
          ORDER BY pr.nombre ASC";
$resultado = mysqli_query($conexion, $query);
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Proveedores Registrados</div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_prov; ?> Activos</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-truck me-2 text-success"></i>Gestión de Proveedores</h3>
        <p class="text-muted small mb-0">Contacto, CUIT y origen de fabricantes y distribuidores.</p>
    </div>
    <a href="index.php?page=proveedores_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nuevo Proveedor
    </a>
</div>

<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-proveedores" class="table table-hover align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th>Razón Social / Nombre</th>
                    <th>CUIT</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Provincia</th>
                    <th class="text-center" style="width: 15%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($pr = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?php echo $pr['id_proveedores']; ?></td>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($pr['nombre']); ?></td>
                            <td class="small"><?php echo htmlspecialchars($pr['cuit'] ?? '-'); ?></td>
                            <td class="small"><?php echo htmlspecialchars($pr['telefono'] ?? '-'); ?></td>
                            <td class="small text-muted"><?php echo htmlspecialchars($pr['email'] ?? '-'); ?></td>
                            <td class="small"><?php echo htmlspecialchars($pr['provincia'] ?? '-'); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=proveedores_editar&id=<?php echo $pr['id_proveedores']; ?>" class="btn btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="btn btn-outline-danger btn-borrar-estetico" data-url="index.php?page=proveedores_borrar&id=<?php echo $pr['id_proveedores']; ?>"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay proveedores registrados aún.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-proveedores').DataTable({ "pageLength": 10, "destroy": true, "language": { "search": "Buscar:" } });
    }
});
</script>