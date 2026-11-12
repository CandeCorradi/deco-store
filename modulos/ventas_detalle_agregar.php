<?php
// modulos/ventas_detalle_agregar.php

$id_venta_previa = isset($_GET['id_venta']) ? intval($_GET['id_venta']) : 0;

// Traemos las ventas disponibles y los artículos activos
$r_ventas = mysqli_query($conexion, "SELECT v.id_ventas, v.fecha_hora, c.nombre, c.apellido 
                                     FROM ventas v 
                                     LEFT JOIN clientes c ON v.id_clientes = c.id_clientes 
                                     WHERE v.esta_eliminado = 0 ORDER BY v.id_ventas DESC");

$r_articulos = mysqli_query($conexion, "SELECT id_articulos, nombre, precio_venta FROM articulos WHERE esta_eliminado = 0 ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ventas       = intval($_POST['id_ventas']);
    $id_articulos    = intval($_POST['id_articulos']);
    $cantidad        = intval($_POST['cantidad']);
    $precio_unitario = floatval($_POST['precio_unitario']);
    $subtotal        = $cantidad * $precio_unitario;

    if ($id_ventas > 0 && $id_articulos > 0 && $cantidad > 0 && $precio_unitario > 0) {
        $insert = "INSERT INTO ventas_detalle (id_ventas, id_articulos, cantidad, precio_unitario, subtotal) 
                   VALUES ($id_ventas, $id_articulos, $cantidad, $precio_unitario, $subtotal)";

        if (mysqli_query($conexion, $insert)) {
            echo "<script>window.location.href = 'index.php?page=ventas_detalle&id_venta=$id_ventas';</script>";
            exit;
        } else {
            $error = "Error al insertar el detalle: " . mysqli_error($conexion);
        }
    } else {
        $error = "Por favor completá los datos del artículo con valores mayores a cero.";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-plus me-2 text-success"></i>Agregar Ítem a Venta</h3>
        <p class="text-muted small mb-0">Vinculá un artículo y su cantidad a una venta existente.</p>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 650px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        
        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Comprobante de Venta (*)</label>
            <select name="id_ventas" class="form-select border" required>
                <option value="">-- Seleccioná la Venta --</option>
                <?php while ($v = mysqli_fetch_assoc($r_ventas)): ?>
                    <?php 
                        $cli = trim(($v['nombre'] ?? '') . ' ' . ($v['apellido'] ?? ''));
                        $label = "Venta #" . $v['id_ventas'] . " (" . date('d/m/Y', strtotime($v['fecha_hora'])) . " - " . ($cli ?: 'Gral') . ")";
                        $selected = ($id_venta_previa == $v['id_ventas']) ? 'selected' : '';
                    ?>
                    <option value="<?php echo $v['id_ventas']; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Artículo / Producto (*)</label>
            <select name="id_articulos" id="select_articulo" class="form-select border" required>
                <option value="" data-precio="0">-- Seleccioná el Artículo --</option>
                <?php while ($art = mysqli_fetch_assoc($r_articulos)): ?>
                    <option value="<?php echo $art['id_articulos']; ?>" data-precio="<?php echo $art['precio_venta']; ?>">
                        <?php echo htmlspecialchars($art['nombre']); ?> ($<?php echo number_format($art['precio_venta'], 2, ',', '.'); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Cantidad (*)</label>
                <input type="number" min="1" name="cantidad" id="input_cantidad" class="form-control border" value="1" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Precio Unitario ($) (*)</label>
                <input type="number" step="0.01" name="precio_unitario" id="input_precio" class="form-control border" placeholder="0.00" required>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=ventas_detalle" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Ítem</button>
        </div>
    </form>
</div>

<script>
// Autocompleta el precio unitario cuando el usuario selecciona un artículo
document.getElementById('select_articulo').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const precio = selectedOption.getAttribute('data-precio');
    if (precio) {
        document.getElementById('input_precio').value = precio;
    }
});
</script>