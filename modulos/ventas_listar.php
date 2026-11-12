<?php
// modulos/ventas_listar.php

// 1. Totales generales
$q_totales = "SELECT COUNT(*) AS total_ventas, SUM(total) AS monto_total FROM ventas WHERE esta_eliminado = 0";
$r_totales = mysqli_query($conexion, $q_totales);
$f_totales = mysqli_fetch_assoc($r_totales);
$total_ventas = $f_totales['total_ventas'] ?? 0;
$monto_total  = $f_totales['monto_total'] ?? 0;

// 2. Consulta principal con JOINs
$query = "SELECT v.*, 
                 c.nombre AS cli_nombre, c.apellido AS cli_apellido, 
                 u.nombre_usuario, 
                 mp.nombre AS metodo_pago
          FROM ventas v
          LEFT JOIN clientes c ON v.id_clientes = c.id_clientes
          LEFT JOIN usuarios u ON v.id_usuarios = u.id_usuarios
          LEFT JOIN metodos_pago mp ON v.id_metodos_pago = mp.id_metodos_pago
          WHERE v.esta_eliminado = 0 
          ORDER BY v.fecha_hora DESC, v.id_ventas DESC";
$resultado = mysqli_query($conexion, $query);
?>

<!-- Tarjeta de Resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Ventas Realizadas</div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_ventas; ?> Comprobantes</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #8D7B68 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Facturación Total</div>
            <div class="h3 fw-bold text-dark mt-1">$<?php echo number_format($monto_total, 2, ',', '.'); ?></div>
        </div>
    </div>
</div>

<!-- Encabezado de la Sección -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-shopping-bag me-2 text-success"></i>Comprobantes de Venta</h3>
        <p class="text-muted small mb-0">Registro general de transacciones, clientes y cobros del showroom.</p>
    </div>
    <a href="index.php?page=ventas_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nueva Venta
    </a>
</div>

<!-- Tabla Principal -->
<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-ventas" class="table table-hover align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 10%;">N° Venta</th>
                    <th>Fecha / Hora</th>
                    <th>Cliente</th>
                    <th>Atendido Por</th>
                    <th>Método de Pago</th>
                    <th class="text-end">Total</th>
                    <th class="text-center" style="width: 18%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($v = mysqli_fetch_assoc($resultado)): ?>
                        <?php 
                            $cliente = trim(($v['cli_nombre'] ?? '') . ' ' . ($v['cli_apellido'] ?? ''));
                            if (empty($cliente)) $cliente = 'Cliente General';
                        ?>
                        <tr>
                            <td class="fw-bold text-dark">#<?php echo $v['id_ventas']; ?></td>
                            <td class="small"><?php echo date('d/m/Y H:i', strtotime($v['fecha_hora'])); ?></td>
                            <td class="fw-semibold text-dark"><?php echo htmlspecialchars($cliente); ?></td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-user me-1 text-muted"></i><?php echo htmlspecialchars($v['nombre_usuario'] ?? 'Sistema'); ?>
                                </span>
                            </td>
                            <td class="small"><?php echo htmlspecialchars($v['metodo_pago'] ?? '-'); ?></td>
                            <td class="text-end fw-bold text-success">$<?php echo number_format($v['total'], 2, ',', '.'); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <!-- Ver Detalle de la Venta -->
                                    <a href="index.php?page=ventas_detalle&id_venta=<?php echo $v['id_ventas']; ?>" class="btn btn-outline-success" title="Ver Detalle de Ítems">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- Editar Venta -->
                                    <a href="index.php?page=ventas_editar&id=<?php echo $v['id_ventas']; ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Borrar Venta -->
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-borrar-estetico" 
                                       data-url="index.php?page=ventas_borrar&id=<?php echo $v['id_ventas']; ?>" 
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <!-- Cancelación de venta -->
                                    <a href="index.php?page=ventas_devolucion&id=<?php echo $v['id_ventas']; ?>" 
                                        class="btn btn-outline-danger btn-sm" 
                                        title="Registrar Devolución / Recibo Negativo">
                                            <i class="fas fa-undo-alt"></i>
                                        </a>
                                    <a href="index.php?page=ventas_recibo&id=<?php echo $v['id_ventas']; ?>" class="btn btn-outline-primary" title="Ver Recibo / Imprimir PDF">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <!-- Facturación AFIP -->
                                    <button type="button" 
                                            class="btn btn-outline-primary" 
                                            title="Emitir Factura Electrónica AFIP" 
                                            onclick="emitirFacturaAfip(<?php echo $v['id_ventas']; ?>, this)">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay ventas registradas aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-ventas').DataTable({
            "pageLength": 10,
            "ordering": true,
            "order": [[0, "desc"]], // Columna 0 (N° Venta) ordenada descendente
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

window.emitirFacturaAfip = function(idVenta, btn) {
    Swal.fire({
        title: '¿Emitir Factura Electrónica?',
        text: 'Se solicitará el CAE en AFIP/ARCA para la venta #' + idVenta,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, emitir factura',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        const htmlOriginal = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        const formData = new FormData();
        formData.append('id_venta', idVenta);

        fetch('php/facturar_venta.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Modal de éxito con opción directa de imprimir/guardar PDF
                Swal.fire({
                    icon: 'success',
                    title: '¡Factura Autorizada!',
                    html: `<b>Comprobante:</b> Factura B N° ${data.factura.cbte_nro}<br>
                           <b>CAE:</b> ${data.factura.cae}<br>
                           <b>Vencimiento:</b> ${data.factura.cae_vto}`,
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-print me-1"></i> Imprimir Factura',
                    cancelButtonText: 'Cerrar'
                }).then((resPrint) => {
                    if (resPrint.isConfirmed) {
                        // Abre la factura con QR en una pestaña nueva lista para guardar como PDF
                        window.open('factura_imprimir.php?id=' + idVenta, '_blank');
                    }
                });

                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');
                btn.title = 'Facturada (CAE: ' + data.factura.cae + ')';
                btn.innerHTML = '<i class="fas fa-check"></i>';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al emitir comprobante',
                    text: data.mensaje,
                    confirmButtonColor: '#dc3545'
                });
                btn.disabled = false;
                btn.innerHTML = htmlOriginal;
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo comunicar con el servidor.',
                confirmButtonColor: '#dc3545'
            });
            btn.disabled = false;
            btn.innerHTML = htmlOriginal;
        });
    });
};

</script>