<?php
// modulos/pedidos_editar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>window.location.href = 'index.php?page=pedidos';</script>";
    exit;
}

$query_p = "SELECT * FROM pedidos WHERE id_pedidos = $id AND esta_eliminado = 0";
$res_p = mysqli_query($conexion, $query_p);
$pedido = mysqli_fetch_assoc($res_p);

if (!$pedido) {
    echo "<div class='alert alert-danger m-4'>El pedido no existe o fue eliminado.</div>";
    exit;
}

$r_clientes = mysqli_query($conexion, "SELECT id_clientes, nombre, apellido FROM clientes WHERE esta_eliminado = 0 ORDER BY nombre ASC");
$r_metodos  = mysqli_query($conexion, "SELECT id_metodos_pago, nombre FROM metodos_pago WHERE esta_eliminado = 0 ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_clientes     = intval($_POST['id_clientes']);
    $id_metodos_pago = intval($_POST['id_metodos_pago']);
    $cantidad_total  = floatval($_POST['cantidad_total']);
    $estado_entrega  = mysqli_real_escape_string($conexion, $_POST['estado_entrega']);

    $update = "UPDATE pedidos SET 
                id_clientes = $id_clientes, 
                id_metodos_pago = $id_metodos_pago, 
                cantidad_total = $cantidad_total, 
                estado_entrega = '$estado_entrega' 
               WHERE id_pedidos = $id";

    if (mysqli_query($conexion, $update)) {
        echo "<script>window.location.href = 'index.php?page=pedidos';</script>";
        exit;
    } else {
        $error = "Error al actualizar: " . mysqli_error($conexion);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Pedido</h3>
        <p class="text-muted small mb-0">Modificá los datos de la orden #<?php echo $id; ?>.</p>
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
                    <option value="<?php echo $c['id_clientes']; ?>" <?php echo ($pedido['id_clientes'] == $c['id_clientes']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Método de Pago (*)</label>
                <select name="id_metodos_pago" class="form-select border" required>
                    <?php while ($mp = mysqli_fetch_assoc($r_metodos)): ?>
                        <option value="<?php echo $mp['id_metodos_pago']; ?>" <?php echo ($pedido['id_metodos_pago'] == $mp['id_metodos_pago']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($mp['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Monto Total ($) (*)</label>
                <input type="number" step="0.01" name="cantidad_total" class="form-control border" value="<?php echo $pedido['cantidad_total']; ?>" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Estado de Entrega</label>
            <select name="estado_entrega" class="form-select border" required>
                <option value="Pendiente" <?php echo ($pedido['estado_entrega'] === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                <option value="Entregado" <?php echo ($pedido['estado_entrega'] === 'Entregado') ? 'selected' : ''; ?>>Entregado</option>
                <option value="Cancelado" <?php echo ($pedido['estado_entrega'] === 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
            </select>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=pedidos" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>