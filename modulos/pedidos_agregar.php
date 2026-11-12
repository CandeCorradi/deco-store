<?php
// modulos/pedidos_agregar.php

// Traemos datos para los selects
$r_clientes = mysqli_query($conexion, "SELECT id_clientes, nombre, apellido FROM clientes WHERE esta_eliminado = 0 ORDER BY nombre ASC");
$r_metodos  = mysqli_query($conexion, "SELECT id_metodos_pago, nombre FROM metodos_pago WHERE esta_eliminado = 0 ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_clientes     = intval($_POST['id_clientes']);
    $id_metodos_pago = intval($_POST['id_metodos_pago']);
    $cantidad_total  = floatval($_POST['cantidad_total']);
    $estado_entrega  = mysqli_real_escape_string($conexion, $_POST['estado_entrega']);
    $id_usuarios     = isset($_SESSION['id_usuarios']) ? intval($_SESSION['id_usuarios']) : 1;

    if ($id_clientes > 0 && $id_metodos_pago > 0 && $cantidad_total > 0) {
        $insert = "INSERT INTO pedidos (id_clientes, id_usuarios, id_metodos_pago, fecha_pedido, cantidad_total, esta_eliminado, estado_entrega) 
                   VALUES ($id_clientes, $id_usuarios, $id_metodos_pago, NOW(), $cantidad_total, 0, '$estado_entrega')";

        if (mysqli_query($conexion, $insert)) {
            echo "<script>window.location.href = 'index.php?page=pedidos';</script>";
            exit;
        } else {
            $error = "Error al crear el pedido: " . mysqli_error($conexion);
        }
    } else {
        $error = "Por favor completá los campos obligatorios.";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-plus me-2 text-success"></i>Nuevo Pedido</h3>
        <p class="text-muted small mb-0">Cargá una nueva orden de pedido para el showroom.</p>
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
                <option value="">-- Seleccioná un Cliente --</option>
                <?php while ($c = mysqli_fetch_assoc($r_clientes)): ?>
                    <option value="<?php echo $c['id_clientes']; ?>">
                        <?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Método de Pago (*)</label>
                <select name="id_metodos_pago" class="form-select border" required>
                    <option value="">-- Seleccioná --</option>
                    <?php while ($mp = mysqli_fetch_assoc($r_metodos)): ?>
                        <option value="<?php echo $mp['id_metodos_pago']; ?>">
                            <?php echo htmlspecialchars($mp['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Monto Total ($) (*)</label>
                <input type="number" step="0.01" name="cantidad_total" class="form-control border" placeholder="35000.00" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Estado de Entrega</label>
            <select name="estado_entrega" class="form-select border" required>
                <option value="Pendiente">Pendiente</option>
                <option value="Entregado">Entregado</option>
                <option value="Cancelado">Cancelado</option>
            </select>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=pedidos" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Pedido</button>
        </div>
    </form>
</div>