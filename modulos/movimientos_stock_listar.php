<?php
// modulos/movimientos_stock_listar.php

// 1. Totales
$q_totales = "SELECT COUNT(*) AS total FROM movimientos_stock WHERE esta_eliminado = 0";
$r_totales = mysqli_query($conexion, $q_totales);
$f_totales = mysqli_fetch_assoc($r_totales);
$total_movimientos = $f_totales['total'] ?? 0;

// 2. Consulta principal uniendo con artículos y usuarios
$query = "SELECT ms.*, 
                 a.nombre AS articulo_nombre, 
                 u.nombre_usuario 
          FROM movimientos_stock ms
          LEFT JOIN articulos a ON ms.id_productos = a.id_articulos
          LEFT JOIN usuarios u ON ms.id_usuarios = u.id_usuarios
          WHERE ms.esta_eliminado = 0 
          ORDER BY ms.fecha_movimiento DESC, ms.id_movimientos_stock DESC";
$resultado = mysqli_query($conexion, $query);
?>

<!-- Tarjeta de Resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Movimientos de Stock</div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_movimientos; ?> Registrados</div>
        </div>
    </div>
</div>

<!-- Encabezado de la Sección -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-boxes me-2 text-success"></i>Control de Movimientos de Stock</h3>
        <p class="text-muted small mb-0">Historial de entradas (IN), salidas (OUT) y comentarios de inventario.</p>
    </div>
    <a href="index.php?page=movimientos_stock_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nuevo Movimiento
    </a>
</div>

<!-- Tabla Principal -->
<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-stock" class="table table-hover align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th>Fecha / Hora</th>
                    <th>Artículo</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Cantidad</th>
                    <th>Usuario</th>
                    <th>Comentarios</th>
                    <th class="text-center" style="width: 12%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($ms = mysqli_fetch_assoc($resultado)): ?>
                        <?php 
                            $tipo = strtoupper($ms['tipo_movimiento']);
                            $badge_class = ($tipo === 'IN') 
                                ? 'bg-success-subtle text-success border-success-subtle' 
                                : 'bg-danger-subtle text-danger border-danger-subtle';
                        ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?php echo $ms['id_movimientos_stock']; ?></td>
                            <td class="small"><?php echo date('d/m/Y H:i', strtotime($ms['fecha_movimiento'])); ?></td>
                            <td class="fw-bold text-dark">
                                <i class="fas fa-couch me-1 text-muted small"></i>
                                <?php echo htmlspecialchars($ms['articulo_nombre'] ?? 'Artículo #'.$ms['id_productos']); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?php echo $badge_class; ?> border rounded-pill px-3">
                                    <?php echo htmlspecialchars($tipo); ?>
                                </span>
                            </td>
                            <td class="text-center fw-bold fs-6"><?php echo $ms['cantidad']; ?></td>
                            <td class="small">
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-user me-1 text-muted"></i><?php echo htmlspecialchars($ms['nombre_usuario'] ?? 'Sistema'); ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?php echo htmlspecialchars($ms['comentarios'] ?? '-'); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=movimientos_stock_editar&id=<?php echo $ms['id_movimientos_stock']; ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-borrar-estetico" 
                                       data-url="index.php?page=movimientos_stock_borrar&id=<?php echo $ms['id_movimientos_stock']; ?>" 
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay movimientos de stock registrados aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-stock').DataTable({
            "pageLength": 10,
            "ordering": true,
            "destroy": true,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "search": "Buscar:",
                "paginate": { "next": "Siguiente", "previous": "Anterior" }
            }
        });
    }
});
</script>