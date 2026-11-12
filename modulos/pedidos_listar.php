<?php
// modulos/pedidos_listar.php

// 1. Totales
$q_totales = "SELECT COUNT(*) AS total FROM pedidos WHERE esta_eliminado = 0";
$r_totales = mysqli_query($conexion, $q_totales);
$f_totales = mysqli_fetch_assoc($r_totales);
$total_pedidos = $f_totales['total'] ?? 0;

// 2. Consulta principal con JOINs para mostrar nombres en lugar de IDs
$query = "SELECT p.*, 
                 c.nombre AS cli_nombre, c.apellido AS cli_apellido, 
                 u.nombre_usuario, 
                 mp.nombre AS metodo_pago
          FROM pedidos p
          LEFT JOIN clientes c ON p.id_clientes = c.id_clientes
          LEFT JOIN usuarios u ON p.id_usuarios = u.id_usuarios
          LEFT JOIN metodos_pago mp ON p.id_metodos_pago = mp.id_metodos_pago
          WHERE p.esta_eliminado = 0 
          ORDER BY p.fecha_pedido DESC";
$resultado = mysqli_query($conexion, $query);
?>

<!-- Tarjeta de Resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Pedidos Registrados</div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_pedidos; ?> Activos</div>
        </div>
    </div>
</div>

<!-- Encabezado de la Sección -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-truck-loading me-2 text-success"></i>Gestión de Pedidos</h3>
        <p class="text-muted small mb-0">Administrá las órdenes de compra, clientes y estados de entrega.</p>
    </div>
    <a href="index.php?page=pedidos_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nuevo Pedido
    </a>
</div>

<!-- Tabla Principal -->
<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-pedidos" class="table table-hover align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Atendido Por</th>
                    <th>Método Pago</th>
                    <th>Monto Total</th>
                    <th class="text-center">Estado Entrega</th>
                    <th class="text-center" style="width: 15%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($resultado)): ?>
                        <?php 
                            $cliente_nombre = trim(($p['cli_nombre'] ?? '') . ' ' . ($p['cli_apellido'] ?? ''));
                            if (empty($cliente_nombre)) $cliente_nombre = 'Cliente General';

                            $estado = $p['estado_entrega'] ?? 'Pendiente';
                            $badge_class = 'bg-warning-subtle text-warning border-warning-subtle';
                            if ($estado === 'Entregado') $badge_class = 'bg-success-subtle text-success border-success-subtle';
                            if ($estado === 'Cancelado') $badge_class = 'bg-danger-subtle text-danger border-danger-subtle';
                        ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?php echo $p['id_pedidos']; ?></td>
                            <td class="small"><?php echo date('d/m/Y H:i', strtotime($p['fecha_pedido'])); ?></td>
                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($cliente_nombre); ?></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-user me-1 text-muted"></i><?php echo htmlspecialchars($p['nombre_usuario'] ?? 'Sistema'); ?>
                                </span>
                            </td>
                            <td class="small"><?php echo htmlspecialchars($p['metodo_pago'] ?? '-'); ?></td>
                            <td class="fw-bold text-success">$<?php echo number_format($p['cantidad_total'], 2, ',', '.'); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo $badge_class; ?> border rounded-pill px-3">
                                    <?php echo htmlspecialchars($estado); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=pedidos_editar&id=<?php echo $p['id_pedidos']; ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-borrar-estetico" 
                                       data-url="index.php?page=pedidos_borrar&id=<?php echo $p['id_pedidos']; ?>" 
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay pedidos registrados aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-pedidos').DataTable({
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