<?php
// modulos/movimientos_stock_agregar.php

$r_articulos = mysqli_query($conexion, "SELECT id_articulos, nombre FROM articulos WHERE esta_eliminado = 0 ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_productos    = intval($_POST['id_productos']);
    $tipo_movimiento = mysqli_real_escape_string($conexion, $_POST['tipo_movimiento']);
    $cantidad        = intval($_POST['cantidad']);
    $comentarios     = mysqli_real_escape_string($conexion, trim($_POST['comentarios']));
    $id_usuarios     = isset($_SESSION['id_usuarios']) ? intval($_SESSION['id_usuarios']) : 1;

    if ($id_productos > 0 && $cantidad > 0) {
        $insert = "INSERT INTO movimientos_stock (id_productos, id_usuarios, tipo_movimiento, cantidad, fecha_movimiento, comentarios, esta_eliminado) 
                   VALUES ($id_productos, $id_usuarios, '$tipo_movimiento', $cantidad, NOW(), '$comentarios', 0)";

        if (mysqli_query($conexion, $insert)) {
            echo "<script>window.location.href = 'index.php?page=movimientos_stock';</script>";
            exit;
        } else {
            $error = "Error al registrar movimiento: " . mysqli_error($conexion);
        }
    } else {
        $error = "Por favor seleccioná un producto y una cantidad mayor a cero.";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-plus me-2 text-success"></i>Nuevo Movimiento de Stock</h3>
        <p class="text-muted small mb-0">Registrá una entrada (IN) o salida (OUT) manual de inventario.</p>
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
                <option value="">-- Seleccioná el Artículo --</option>
                <?php while ($art = mysqli_fetch_assoc($r_articulos)): ?>
                    <option value="<?php echo $art['id_articulos']; ?>">
                        <?php echo htmlspecialchars($art['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Tipo de Movimiento (*)</label>
                <select name="tipo_movimiento" class="form-select border" required>
                    <option value="OUT">OUT (Salida / Venta)</option>
                    <option value="IN">IN (Entrada / Proveedor)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Cantidad (*)</label>
                <input type="number" min="1" name="cantidad" class="form-control border" value="1" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Comentarios / Detalle</label>
            <textarea name="comentarios" class="form-control border" rows="3" placeholder="Ej: Venta realizada en el showroom, Ingreso por reposición..."></textarea>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=movimientos_stock" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Movimiento</button>
        </div>
    </form>
</div>