<?php
// modulos/caja_editar.php

// 1. Validamos que llegue un ID válido por la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>window.location.href = 'index.php?page=caja';</script>";
    exit;
}

// 2. Traemos los datos actuales de este movimiento para precargar el formulario
$query_mov = "SELECT * FROM caja WHERE id_caja = $id AND esta_eliminado = 0";
$res_mov = mysqli_query($conexion, $query_mov);
$movimiento = mysqli_fetch_assoc($res_mov);

if (!$movimiento) {
    echo "<div class='alert alert-danger m-4'>El registro no existe o fue eliminado.</div>";
    exit;
}

// Separamos la fecha del datetime para el input de tipo date
$fecha_input = date('Y-m-d', strtotime($movimiento['fecha']));

// Detectamos si originalmente es un ingreso o egreso y calculamos el valor absoluto
$tipo_original = ($movimiento['importe'] < 0) ? 'egreso' : 'ingreso';
$importe_absoluto = abs($movimiento['importe']);

// 3. Traemos las cuentas para el selector
$q_cuentas = "SELECT * FROM cuentas WHERE esta_eliminado = 0 ORDER BY cuenta ASC";
$r_cuentas = mysqli_query($conexion, $q_cuentas);

// 4. Procesamos el formulario cuando la usuaria guarda los cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'] . ' ' . date('H:i:s'); // Nueva fecha + hora actual
    $id_cuenta = intval($_POST['id_cuenta']);
    $detalle = mysqli_real_escape_string($conexion, $_POST['detalle']);
    $monto_absoluto = floatval($_POST['importe']);
    $tipo = $_POST['tipo'];

    // 💡 Aplicamos la misma regla: si es egreso, va con signo menos a la base de datos
    $importe_final = ($tipo === 'egreso') ? -$monto_absoluto : $monto_absoluto;

    $update = "UPDATE caja SET 
                fecha = '$fecha', 
                id_cuenta = $id_cuenta, 
                detalle = '$detalle', 
                importe = $importe_final 
               WHERE id_caja = $id";

    if (mysqli_query($conexion, $update)) {
        echo "<script>window.location.href = 'index.php?page=caja';</script>";
        exit;
    } else {
        $error = "Error al actualizar el registro: " . mysqli_error($conexion);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Movimiento</h3>
        <p class="text-muted small mb-0">Modificá los detalles del registro de caja #<?php echo $id; ?>.</p>
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
                <option value="ingreso" class="text-success fw-bold" <?php echo $tipo_original === 'ingreso' ? 'selected' : ''; ?>>🟢 Ingreso / Entrada de dinero</option>
                <option value="egreso" class="text-danger fw-bold" <?php echo $tipo_original === 'egreso' ? 'selected' : ''; ?>>🔴 Egreso / Gasto / Pago</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Cuenta asociada</label>
            <select name="id_cuenta" class="form-select border" required>
                <option value="">-- Seleccionar Cuenta --</option>
                <?php while ($c = mysqli_fetch_assoc($r_cuentas)): ?>
                    <option value="<?php echo $c['id_cuenta']; ?>" <?php echo $c['id_cuenta'] == $movimiento['id_cuenta'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['cuenta']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Fecha</label>
            <input type="date" name="fecha" class="form-control border" value="<?php echo $fecha_input; ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Detalle o Motivo</label>
            <input type="text" name="detalle" class="form-control border" value="<?php echo htmlspecialchars($movimiento['detalle']); ?>" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Importe ($)</label>
            <div class="input-group">
                <span class="input-group-text bg-light fw-bold">$</span>
                <input type="number" name="importe" step="0.01" min="0.01" class="form-control form-control-lg border fw-bold" value="<?php echo $importe_absoluto; ?>" required>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=caja" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>