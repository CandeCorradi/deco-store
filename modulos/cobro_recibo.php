<?php
// modulos/cobro_recibo.php
$id_cobro = isset($_GET['id']) ? intval($_GET['id']) : 0;

$q = "SELECT cc.*, c.nombre AS cli_nombre, c.apellido AS cli_apellido, c.telefono, c.email, 
             mp.nombre AS metodo_nombre, u.nombre_usuario 
      FROM cobros_ctacte cc
      LEFT JOIN clientes c ON cc.id_clientes = c.id_clientes
      LEFT JOIN metodos_pago mp ON cc.id_metodos_pago = mp.id_metodos_pago
      LEFT JOIN usuarios u ON cc.id_usuarios = u.id_usuarios
      WHERE cc.id_cobros_ctacte = $id_cobro AND cc.esta_eliminado = 0";

$r = mysqli_query($conexion, $q);
$cobro = ($r) ? mysqli_fetch_assoc($r) : null;

if (!$cobro) {
    echo "<div class='alert alert-danger'>Recibo de cobro no encontrado.</div>";
    return;
}

$num_recibo = 'REC-' . str_pad($cobro['id_cobros_ctacte'], 5, '0', STR_PAD_LEFT);
?>

<div class="d-print-none d-flex justify-content-between align-items-center mb-3">
    <a href="index.php?page=clientes_cuenta_corriente&id=<?php echo $cobro['id_clientes']; ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Volver a Cuenta Corriente
    </a>
    <button type="button" onclick="window.print();" class="btn btn-danger btn-sm fw-semibold">
        <i class="fas fa-print me-1"></i> Imprimir Recibo de Cobranza
    </button>
</div>

<!-- Recibo Oficial de Cobranza (Recibo X) -->
<div class="card p-4 mx-auto shadow-sm" style="max-width: 800px; border-radius: 12px; border: 1px solid #EAE1D0; background: #FFFFFF;">
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
        <div>
            <h3 class="fw-bold mb-0 text-success"><i class="fas fa-couch me-2"></i>DecoStore</h3>
            <span class="text-muted small">Mobiliario & Diseño de Interiores - San Justo, Santa Fe</span>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-0 text-dark">RECIBO DE COBRANZA</h5>
            <span class="badge bg-secondary-subtle text-dark border mt-1 fs-6">N° <?php echo $num_recibo; ?></span>
            <div class="small text-muted mt-1">Fecha: <?php echo date('d/m/Y H:i', strtotime($cobro['fecha_pago'])); ?></div>
        </div>
    </div>

    <div class="row g-3 mb-4 small">
        <div class="col-6">
            <span class="text-muted d-block fw-semibold text-uppercase" style="font-size: 0.72rem;">Recibimos de:</span>
            <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars(trim($cobro['cli_nombre'] . ' ' . $cobro['cli_apellido'])); ?></span><br>
            <span>Teléfono: <?php echo htmlspecialchars($cobro['telefono'] ?? 'Sin registrar'); ?></span><br>
            <span>Email: <?php echo htmlspecialchars($cobro['email'] ?? 'Sin registrar'); ?></span>
        </div>
        <div class="col-6 text-end">
            <span class="text-muted d-block fw-semibold text-uppercase" style="font-size: 0.72rem;">Operador / Caja:</span>
            <span class="fw-bold text-dark"><?php echo htmlspecialchars($cobro['nombre_usuario'] ?? 'Administrador'); ?></span><br>
            <span>Forma de Pago: <strong><?php echo htmlspecialchars($cobro['metodo_nombre'] ?? 'Efectivo'); ?></strong></span>
        </div>
    </div>

    <!-- Cuadro de Detalle del Pago -->
    <div class="card p-3 mb-4" style="background: #FDFCF6; border: 1px solid #EAE1D0; border-radius: 8px;">
        <span class="text-muted text-uppercase fw-bold small" style="font-size: 0.72rem;">En Concepto De:</span>
        <p class="mb-3 text-dark"><?php echo htmlspecialchars($cobro['concepto']); ?></p>

        <div class="d-flex justify-content-between align-items-center border-top pt-2">
            <span class="fw-bold fs-5 text-dark">TOTAL RECIBIDO:</span>
            <span class="fw-bold fs-4 text-success">$ <?php echo number_format($cobro['monto'], 2, ',', '.'); ?></span>
        </div>
    </div>

    <div class="row mt-5 pt-4 text-center text-muted small">
        <div class="col-6">
            <div class="border-top mx-4 pt-2">Firma y Aclaración Cliente</div>
        </div>
        <div class="col-6">
            <div class="border-top mx-4 pt-2">Sello / Firma DecoStore</div>
        </div>
    </div>
</div>

<style>
@media print {
    .d-print-none, .navbar, footer {
        display: none !important;
    }
    body {
        background: #FFFFFF !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>