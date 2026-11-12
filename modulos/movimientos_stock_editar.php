<?php
// modulos/movimientos_stock_editar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>window.location.href = 'index.php?page=movimientos_stock';</script>";
    exit;
}

$query_ms = "SELECT * FROM movimientos_stock WHERE id_movimientos_stock = $id AND esta_eliminado = 0";
$res_ms = mysqli_query($conexion, $query_ms);
$ms = mysqli_fetch_assoc($res_ms);

if (!$ms) {
    echo "<div class='alert alert-danger m-4'>El movimiento de stock no existe o fue eliminado.</div>";
    exit;
}

$r_articulos = mysqli_query($conexion, "SELECT id_articulos, nombre FROM articulos WHERE esta_eliminado = 0 ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_productos    = intval($_POST['id_productos']);
    $tipo_movimiento = mysqli_real_escape_string($conexion, $_POST['tipo_movimiento']);
    $cantidad        = intval($_POST['cantidad']);
    $comentarios     = mysqli_real_escape_string($conexion, trim($_POST['comentarios']));

    $update = "UPDATE movimientos_stock SET 
                id_productos = $id_productos, 
                tipo_movimiento = '$tipo_movimiento', 
                cantidad = $cantidad, 
                comentarios = '$comentarios' 
               WHERE id_movimientos_stock = $id";

    if (mysqli_query($conexion, $update)) {
        echo "<script>window.location.href = 'index.php?page=movimientos_stock';</script>";
        exit;
    } else {
        $error = "Error al actualizar el movimiento: " . mysqli_error($conexion);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Movimiento #<?php echo $id; ?></h3>
        <p class="text-muted small mb-0">Modificá los detalles del ajuste de stock.</p>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 650px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        
        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Artículo / Producto (*)</label>
            <select name="id_productos" class="form-select border" required>
                <?php while ($art = mysqli_fetch_assoc($r_articulos)): ?>
                    <option value="<?php echo $art['id_articulos']; ?>" <?php echo ($ms['id_productos'] == $art['id_articulos']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($art['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Tipo de Movimiento (*)</label>
                <select name="tipo_movimiento" class="form-select border" required>
                    <option value="OUT" <?php echo ($ms['tipo_movimiento'] === 'OUT') ? 'selected' : ''; ?>>OUT (Salida / Venta)</option>
                    <option value="IN" <?php echo ($ms['tipo_movimiento'] === 'IN') ? 'selected' : ''; ?>>IN (Entrada / Proveedor)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Cantidad (*)</label>
                <input type="number" min="1" name="cantidad" class="form-control border" value="<?php echo $ms['cantidad']; ?>" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Comentarios / Detalle</label>
            <textarea name="comentarios" class="form-control border" rows="3"><?php echo htmlspecialchars($ms['comentarios'] ?? ''); ?></textarea>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=movimientos_stock" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>