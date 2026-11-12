<?php
// modulos/ventas_detalle_editar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>window.location.href = 'index.php?page=ventas_detalle';</script>";
    exit;
}

$query_vd = "SELECT * FROM ventas_detalle WHERE id_ventas_detalle = $id";
$res_vd = mysqli_query($conexion, $query_vd);
$detalle = mysqli_fetch_assoc($res_vd);

if (!$detalle) {
    echo "<div class='alert alert-danger m-4'>El registro de detalle no existe.</div>";
    exit;
}

$r_articulos = mysqli_query($conexion, "SELECT id_articulos, nombre, precio_venta FROM articulos WHERE esta_eliminado = 0 ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_articulos    = intval($_POST['id_articulos']);
    $cantidad        = intval($_POST['cantidad']);
    $precio_unitario = floatval($_POST['precio_unitario']);
    $subtotal        = $cantidad * $precio_unitario;

    $update = "UPDATE ventas_detalle SET 
                id_articulos = $id_articulos, 
                cantidad = $cantidad, 
                precio_unitario = $precio_unitario, 
                subtotal = $subtotal 
               WHERE id_ventas_detalle = $id";

    if (mysqli_query($conexion, $update)) {
        echo "<script>window.location.href = 'index.php?page=ventas_detalle&id_venta=" . $detalle['id_ventas'] . "';</script>";
        exit;
    } else {
        $error = "Error al actualizar: " . mysqli_error($conexion);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Ítem de Venta</h3>
        <p class="text-muted small mb-0">Modificá el artículo o la cantidad cargada en la Venta #<?php echo $detalle['id_ventas']; ?>.</p>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 650px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        
        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Artículo / Producto (*)</label>
            <select name="id_articulos" class="form-select border" required>
                <?php while ($art = mysqli_fetch_assoc($r_articulos)): ?>
                    <option value="<?php echo $art['id_articulos']; ?>" <?php echo ($detalle['id_articulos'] == $art['id_articulos']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($art['nombre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Cantidad (*)</label>
                <input type="number" min="1" name="cantidad" class="form-control border" value="<?php echo $detalle['cantidad']; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Precio Unitario ($) (*)</label>
                <input type="number" step="0.01" name="precio_unitario" class="form-control border" value="<?php echo $detalle['precio_unitario']; ?>" required>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=ventas_detalle&id_venta=<?php echo $detalle['id_ventas']; ?>" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>