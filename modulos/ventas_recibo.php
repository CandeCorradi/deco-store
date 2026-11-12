<?php
// modulos/ventas_recibo.php
$id_venta = isset($_GET['id']) ? intval($_GET['id']) : 0;

$q_venta = "SELECT v.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido, c.telefono, c.email, u.nombre_usuario 
            FROM ventas v
            LEFT JOIN clientes c ON v.id_clientes = c.id_clientes
            LEFT JOIN usuarios u ON v.id_usuarios = u.id_usuarios
            WHERE v.id_ventas = $id_venta AND v.esta_eliminado = 0";
$r_venta = mysqli_query($conexion, $q_venta);
$venta   = ($r_venta) ? mysqli_fetch_assoc($r_venta) : null;

if (!$venta) {
    echo "<div class='alert alert-danger m-3'>Comprobante no encontrado.</div>";
    return;
}

$q_items = "SELECT vd.*, a.nombre AS articulo_nombre 
            FROM ventas_detalle vd 
            INNER JOIN articulos a ON vd.id_articulos = a.id_articulos 
            WHERE vd.id_ventas = $id_venta";
$r_items = mysqli_query($conexion, $q_items);

$q_pagos = "SELECT vp.*, mp.nombre AS metodo_nombre 
            FROM ventas_pagos vp 
            INNER JOIN metodos_pago mp ON vp.id_metodos_pago = mp.id_metodos_pago 
            WHERE vp.id_ventas = $id_venta AND vp.esta_eliminado = 0";
$r_pagos = mysqli_query($conexion, $q_pagos);
?>

<style>
@media print {
    body * { visibility: hidden; }
    #area-recibo, #area-recibo * { visibility: visible; }
    #area-recibo { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
.recibo-card {
    background: #fff;
    max-width: 800px;
    margin: 0 auto;
    border: 1px solid #EAE1D0;
    border-radius: 12px;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    
    <div>
        <a href="exportar_venta_excel.php?id=<?php echo $id_venta; ?>" class="btn btn-outline-success btn-sm me-2">
            <i class="fas fa-file-excel me-1"></i> Descargar Excel
        </a>
        <button onclick="window.print();" class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf me-1"></i> Imprimir / Guardar PDF
        </button>   

    <!-- Botón Enviar por Email -->
    <button type="button" class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalEnviarMail">
        <i class="fas fa-envelope me-1"></i> Enviar por Email
    </button>

    <!-- Badge con estado histórico del envío -->
    <?php
    $q_mail_log = mysqli_query($conexion, "SELECT estado, fecha, email FROM ventas_email WHERE id_ventas = $id_venta ORDER BY id_ventas_email DESC LIMIT 1");
    if ($log_m = mysqli_fetch_assoc($q_mail_log)):
        $badge_class = ($log_m['estado'] === 'ENVIADA') ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle';
    ?>
        <span class="badge border ms-2 px-2 py-1 <?php echo $badge_class; ?>" id="badgeEstadoMail">
            <i class="fas fa-paper-plane me-1"></i> <?php echo $log_m['estado']; ?> (<?php echo date('d/m H:i', strtotime($log_m['fecha'])); ?>)
        </span>
    <?php else: ?>
        <span class="badge bg-light text-muted border ms-2 px-2 py-1" id="badgeEstadoMail">
            <i class="far fa-clock me-1"></i> No enviado aún
        </span>
    <?php endif; ?>
</div>

<!-- Modal para Confirmar / Editar Destinatario -->
<div class="modal fade d-print-none" id="modalEnviarMail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid #EAE1D0;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #3E2723;">
                    <i class="fas fa-envelope-open-text text-primary me-2"></i>Enviar Comprobante
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body py-3">
                <p class="small text-muted mb-3">Se enviará el detalle de la venta a través de SMTP autenticado desde <code>candecorradi01@d26.com.ar</code>.</p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Email del Destinatario (*)</label>
                    <input type="email" id="inputCorreoDestino" class="form-control" value="<?php echo htmlspecialchars($venta['email'] ?? ''); ?>" placeholder="cliente@correo.com" required>
                </div>
                <div id="alertaEnvioMail" class="d-none alert py-2 small mb-0"></div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary btn-sm fw-semibold" id="btnConfirmarEnvioMail">
                    <i class="fas fa-paper-plane me-1"></i> Enviar Ahora
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnConfirmarEnvioMail').addEventListener('click', function() {
    const btn = this;
    const email = document.getElementById('inputCorreoDestino').value.trim();
    const alerta = document.getElementById('alertaEnvioMail');
    const idVenta = "<?php echo $id_venta; ?>";

    if (!email) {
        alerta.className = 'alert alert-danger py-2 small mb-0';
        alerta.textContent = 'Por favor ingresá un correo válido.';
        alerta.classList.remove('d-none');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Enviando...';
    alerta.classList.add('d-none');

    const formData = new FormData();
    formData.append('id_venta', idVenta);
    formData.append('email', email);

    fetch('php/enviar_factura_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Enviar Ahora';

        if (data.status === 'success') {
            alerta.className = 'alert alert-success py-2 small mb-0';
            alerta.textContent = data.mensaje;
            alerta.classList.remove('d-none');

            // Actualizar el badge visualmente
            const badge = document.getElementById('badgeEstadoMail');
            if (badge) {
                badge.className = 'badge border ms-2 px-2 py-1 bg-success-subtle text-success border-success-subtle';
                badge.innerHTML = `<i class="fas fa-paper-plane me-1"></i> ENVIADA (${data.fecha})`;
            }

            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalEnviarMail'));
                if (modal) modal.hide();
            }, 1800);
        } else {
            alerta.className = 'alert alert-danger py-2 small mb-0';
            alerta.textContent = data.mensaje;
            alerta.classList.remove('d-none');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Enviar Ahora';
        alerta.className = 'alert alert-danger py-2 small mb-0';
        alerta.textContent = 'Error de conexión con el servidor.';
        alerta.classList.remove('d-none');
    });
});
</script>
    </div>
</div>

<div class="recibo-card p-4 shadow-sm" id="area-recibo">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
        <div>
            <h3 class="fw-bold mb-0 text-success"><i class="fas fa-couch me-2"></i>DecoStore</h3>
            <p class="text-muted small mb-0">Mobiliario & Diseño de Interiores</p>
            <p class="text-muted small mb-0">San Justo, Santa Fe</p>
        </div>
        <div class="text-end">
            <h4 class="fw-bold text-dark mb-0">RECIBO DE VENTA</h4>
            <div class="badge bg-secondary fs-6">N° #<?php echo str_pad($venta['id_ventas'], 6, '0', STR_PAD_LEFT); ?></div>
            <div class="small text-muted mt-1">Fecha: <?php echo date('d/m/Y H:i', strtotime($venta['fecha_hora'])); ?></div>
        </div>
    </div>

    <!-- Cliente y Emisor -->
    <div class="row g-2 mb-3 small">
        <div class="col-6">
            <strong>Cliente:</strong> <?php echo htmlspecialchars(trim(($venta['cli_nombre'] ?? '') . ' ' . ($venta['cli_apellido'] ?? 'General'))); ?><br>
            <strong>Teléfono:</strong> <?php echo htmlspecialchars($venta['telefono'] ?? '-'); ?><br>
            <strong>Email:</strong> <?php echo htmlspecialchars($venta['email'] ?? '-'); ?>
        </div>
        <div class="col-6 text-end">
            <strong>Atendido por:</strong> <?php echo htmlspecialchars($venta['nombre_usuario'] ?? 'Sistema'); ?><br>
            <strong>Condición:</strong> Venta Mostrador
        </div>
    </div>

    <!-- Tabla de Ítems -->
    <table class="table table-sm table-bordered align-middle mb-3" style="font-size: 0.9rem;">
        <thead class="table-light">
            <tr>
                <th>Descripción del Artículo</th>
                <th class="text-center" style="width: 10%;">Cant.</th>
                <th class="text-end" style="width: 20%;">Precio Unit.</th>
                <th class="text-end" style="width: 20%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($it = mysqli_fetch_assoc($r_items)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($it['articulo_nombre']); ?></td>
                    <td class="text-center"><?php echo $it['cantidad']; ?></td>
                    <td class="text-end">$<?php echo number_format($it['precio_unitario'], 2, ',', '.'); ?></td>
                    <td class="text-end fw-semibold">$<?php echo number_format($it['subtotal'], 2, ',', '.'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">TOTAL:</th>
                <th class="text-end text-success fs-6">$<?php echo number_format($venta['total'], 2, ',', '.'); ?></th>
            </tr>
        </tfoot>
    </table>

    <!-- Desglose de Cobro (ventas_pagos) -->
    <div class="p-3 bg-light rounded-3 small">
        <strong class="d-block mb-1 text-dark">Forma de Pago / Liquidación:</strong>
        <ul class="mb-0 ps-3">
            <?php if ($r_pagos && mysqli_num_rows($r_pagos) > 0): ?>
                <?php while ($p = mysqli_fetch_assoc($r_pagos)): ?>
                    <li>
                        <?php echo htmlspecialchars($p['metodo_nombre']); ?>: 
                        <span class="fw-bold">$<?php echo number_format($p['monto'], 2, ',', '.'); ?></span>
                        <?php if ($p['id_metodos_pago'] == 7): ?>
                            <span class="text-danger fw-semibold">(Abono en Cuenta Corriente)</span>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>Contado / Facturado directo</li>
            <?php endif; ?>
        </ul>
    </div>
</div>