<?php
// modulos/ventas_editar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>window.location.href = 'index.php?page=ventas';</script>";
    exit;
}

$query_v = "SELECT * FROM ventas WHERE id_ventas = $id AND esta_eliminado = 0";
$res_v = mysqli_query($conexion, $query_v);
$venta = mysqli_fetch_assoc($res_v);

if (!$venta) {
    echo "<div class='alert alert-danger m-4'>El comprobante de venta no existe o fue eliminado.</div>";
    exit;
}

$r_clientes = mysqli_query($conexion, "SELECT id_clientes, nombre, apellido FROM clientes WHERE esta_eliminado = 0 ORDER BY nombre ASC");
$r_metodos  = mysqli_query($conexion, "SELECT id_metodos_pago, nombre FROM metodos_pago WHERE esta_eliminado = 0 ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_clientes     = intval($_POST['id_clientes']);
    $id_metodos_pago = intval($_POST['id_metodos_pago']);
    $total           = floatval($_POST['total']);

    $update = "UPDATE ventas SET 
                id_clientes = $id_clientes, 
                id_metodos_pago = $id_metodos_pago, 
                total = $total 
               WHERE id_ventas = $id";

    if (mysqli_query($conexion, $update)) {
        echo "<script>window.location.href = 'index.php?page=ventas';</script>";
        exit;
    } else {
        $error = "Error al actualizar la venta: " . mysqli_error($conexion);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Venta #<?php echo $id; ?></h3>
        <p class="text-muted small mb-0">Modificá la cabecera del comprobante.</p>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 650px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        
        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Cliente (*)</label>
            <select name="id_clientes" class="form-select border" required>
                <?php while ($c = mysqli_fetch_assoc($r_clientes)): ?>
                    <option value="<?php echo $c['id_clientes']; ?>" <?php echo ($venta['id_clientes'] == $c['id_clientes']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Método de Pago (*)</label>
                <select name="id_metodos_pago" class="form-select border" required>
                    <?php while ($mp = mysqli_fetch_assoc($r_metodos)): ?>
                        <option value="<?php echo $mp['id_metodos_pago']; ?>" <?php echo ($venta['id_metodos_pago'] == $mp['id_metodos_pago']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($mp['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Monto Total ($) (*)</label>
                <input type="number" step="0.01" name="total" class="form-control border" value="<?php echo $venta['total']; ?>" required>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=ventas" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>