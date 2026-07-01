<?php
// modulos/caja_agregar.php

// 1. Traemos las cuentas activas de la tabla simple para llenar el selector
$q_cuentas = "SELECT * FROM cuentas WHERE esta_eliminado = 0 ORDER BY cuenta ASC";
$r_cuentas = mysqli_query($conexion, $q_cuentas);

// 2. Procesamos el formulario cuando la usuaria hace clic en Guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'] . ' ' . date('H:i:s'); // Toma la fecha del input y le suma la hora actual
    $id_cuenta = intval($_POST['id_cuenta']);
    $detalle = mysqli_real_escape_string($conexion, $_POST['detalle']);
    $monto_absoluto = floatval($_POST['importe']);
    $tipo = $_POST['tipo']; // 'ingreso' o 'egreso'

    // 💡 Regla de negocio: si es egreso, lo guardamos con signo negativo
    $importe_final = ($tipo === 'egreso') ? -$monto_absoluto : $monto_absoluto;

    $insert = "INSERT INTO caja (fecha, id_cuenta, detalle, importe, esta_eliminado) 
               VALUES ('$fecha', $id_cuenta, '$detalle', $importe_final, 0)";

    if (mysqli_query($conexion, $insert)) {
        echo "<script>
            window.location.href = 'index.php?page=caja';
        </script>";
        exit;
    } else {
        $error = "Error al registrar el movimiento: " . mysqli_error($conexion);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-plus me-2 text-success"></i>Nuevo Movimiento de Caja</h3>
        <p class="text-muted small mb-0">Cargá un ingreso o egreso manual al flujo diario.</p>
    </div>
    <a href="index.php?page=caja" class="btn btn-outline-secondary fw-semibold">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 600px; background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        
        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Tipo de Movimiento</label>
            <select name="tipo" class="form-select form-select-lg border" required>
                <option value="ingreso" class="text-success fw-bold">🟢 Ingreso / Entrada de dinero</option>
                <option value="egreso" class="text-danger fw-bold">🔴 Egreso / Gasto / Pago</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Cuenta asociada</label>
            <select name="id_cuenta" class="form-select border" required>
                <option value="">-- Seleccionar Cuenta --</option>
                <?php while ($c = mysqli_fetch_assoc($r_cuentas)): ?>
                    <option value="<?php echo $c['id_cuenta']; ?>">
                        <?php echo htmlspecialchars($c['cuenta']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Fecha</label>
            <input type="date" name="fecha" class="form-control border" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Detalle o Motivo</label>
            <input type="text" name="detalle" class="form-control border" placeholder="Ej: Pago de Luz local / Venta menor de accesorios" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Importe ($)</label>
            <div class="input-group">
                <span class="input-group-text bg-light fw-bold">$</span>
                <input type="number" name="importe" step="0.01" min="0.01" class="form-control form-control-lg border fw-bold" placeholder="0.00" required>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=caja" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Registro</button>
        </div>
    </form>
</div>