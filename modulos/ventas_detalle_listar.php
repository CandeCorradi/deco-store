<?php
// modulos/ventas_detalle_listar.php

// Capturamos si viene un filtro por venta específica desde la URL
$id_venta_filtro = isset($_GET['id_venta']) ? intval($_GET['id_venta']) : 0;

$where = "WHERE 1=1";
if ($id_venta_filtro > 0) {
    $where .= " AND vd.id_ventas = $id_venta_filtro";
}

// Consulta principal trayendo datos del artículo y de la venta
$query = "SELECT vd.*, 
                 a.nombre AS articulo_nombre, 
                 v.fecha_hora, 
                 c.nombre AS cli_nombre, c.apellido AS cli_apellido
          FROM ventas_detalle vd
          INNER JOIN articulos a ON vd.id_articulos = a.id_articulos
          INNER JOIN ventas v ON vd.id_ventas = v.id_ventas
          LEFT JOIN clientes c ON v.id_clientes = c.id_clientes
          $where
          ORDER BY vd.id_ventas DESC, vd.id_ventas_detalle ASC";

$resultado = mysqli_query($conexion, $query);

// Consulta de totales
$q_totales = "SELECT SUM(subtotal) AS total_acumulado, COUNT(*) AS total_items FROM ventas_detalle vd $where";
$r_totales = mysqli_query($conexion, $q_totales);
$f_totales = mysqli_fetch_assoc($r_totales);
$total_acumulado = $f_totales['total_acumulado'] ?? 0;
$total_items     = $f_totales['total_items'] ?? 0;
?>

<!-- Tarjeta de Resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">
                <?php echo ($id_venta_filtro > 0) ? "Detalles de la Venta #$id_venta_filtro" : "Total de Ítems Vendidos"; ?>
            </div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_items; ?> Ítems</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #8D7B68 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Monto Subtotal Acumulado</div>
            <div class="h3 fw-bold text-dark mt-1">$<?php echo number_format($total_acumulado, 2, ',', '.'); ?></div>
        </div>
    </div>
</div>

<!-- Encabezado de la Sección -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-list-ol me-2 text-success"></i>Detalle de Ventas</h3>
        <p class="text-muted small mb-0">Desglose de artículos, cantidades y importes asignados a los comprobantes.</p>
    </div>
    <div>
        <?php if ($id_venta_filtro > 0): ?>
            <a href="index.php?page=ventas_detalle" class="btn btn-outline-secondary btn-sm me-2">Ver Todos los Detalles</a>
        <?php endif; ?>
        <a href="index.php?page=ventas_detalle_agregar<?php echo ($id_venta_filtro > 0) ? '&id_venta='.$id_venta_filtro : ''; ?>" class="btn btn-success fw-semibold">
            <i class="fas fa-plus me-1"></i> Agregar Ítem
        </a>
    </div>
</div>

<!-- Tabla Principal -->
<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-ventas-detalle" class="table table-hover align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 10%;">Comp. #</th>
                    <th>Artículo / Producto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Precio Unit.</th>
                    <th class="text-end">Subtotal</th>
                    <th>Cliente</th>
                    <th class="text-center" style="width: 15%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($d = mysqli_fetch_assoc($resultado)): ?>
                        <?php 
                            $cliente = trim(($d['cli_nombre'] ?? '') . ' ' . ($d['cli_apellido'] ?? ''));
                            if (empty($cliente)) $cliente = 'Cliente General';
                        ?>
                        <tr>
                            <td class="fw-bold text-dark">
                                <a href="index.php?page=ventas_detalle&id_venta=<?php echo $d['id_ventas']; ?>" class="text-decoration-none text-success">
                                    #<?php echo $d['id_ventas']; ?>
                                </a>
                            </td>
                            <td class="fw-semibold text-dark">
                                <i class="fas fa-couch me-2 text-muted small"></i><?php echo htmlspecialchars($d['articulo_nombre']); ?>
                            </td>
                            <td class="text-center fw-bold"><?php echo $d['cantidad']; ?></td>
                            <td class="text-end">$<?php echo number_format($d['precio_unitario'], 2, ',', '.'); ?></td>
                            <td class="text-end fw-bold text-success">$<?php echo number_format($d['subtotal'], 2, ',', '.'); ?></td>
                            <td class="small text-muted"><?php echo htmlspecialchars($cliente); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=ventas_detalle_editar&id=<?php echo $d['id_ventas_detalle']; ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-borrar-estetico" 
                                       data-url="index.php?page=ventas_detalle_borrar&id=<?php echo $d['id_ventas_detalle']; ?>" 
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay ítems registrados en el detalle de venta.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-ventas-detalle').DataTable({
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