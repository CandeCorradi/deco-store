<?php
// modulos/presupuestos_agregar.php

$r_clientes = mysqli_query($conexion, "SELECT id_clientes, nombre, apellido FROM clientes WHERE esta_eliminado = 0 ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_clientes    = intval($_POST['id_clientes']);
    $fecha_limite   = mysqli_real_escape_string($conexion, $_POST['fecha_limite']);
    $cantidad_total = floatval($_POST['cantidad_total']);
    $estado         = mysqli_real_escape_string($conexion, $_POST['estado']);
    $id_usuarios    = isset($_SESSION['id_usuarios']) ? intval($_SESSION['id_usuarios']) : 1;

    if ($id_clientes > 0 && $cantidad_total >= 0) {
        $insert = "INSERT INTO presupuestos (id_clientes, id_usuarios, fecha_creacion, fecha_limite, cantidad_total, estado, esta_eliminado) 
                   VALUES ($id_clientes, $id_usuarios, NOW(), '$fecha_limite', $cantidad_total, '$estado', 0)";
        if (mysqli_query($conexion, $insert)) {
            echo "<script>window.location.href = 'index.php?page=presupuestos';</script>";
            exit;
        } else { $error = "Error al guardar: " . mysqli_error($conexion); }
    } else { $error = "Completá los campos obligatorios."; }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-1"><i class="fas fa-plus me-2 text-success"></i>Nuevo Presupuesto</h3>
</div>

<?php if (isset($error)): ?><div class="alert alert-danger mb-4"><?php echo $error; ?></div><?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 650px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Cliente (*)</label>
            <select name="id_clientes" class="form-select border" required>
                <option value="">-- Seleccioná --</option>
                <?php while ($c = mysqli_fetch_assoc($r_clientes)): ?>
                    <option value="<?php echo $c['id_clientes']; ?>"><?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Fecha Límite / Vencimiento</label>
                <input type="date" name="fecha_limite" class="form-control border" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Monto Total ($) (*)</label>
                <input type="number" step="0.01" name="cantidad_total" class="form-control border" placeholder="0.00" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Estado</label>
            <select name="estado" class="form-select border" required>
                <option value="Pending">Pending (Pendiente)</option>
                <option value="Approved">Approved (Aprobado)</option>
                <option value="Rejected">Rejected (Rechazado)</option>
            </select>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=presupuestos" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Presupuesto</button>
        </div>
    </form>
</div>