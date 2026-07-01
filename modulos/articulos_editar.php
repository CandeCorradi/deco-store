<?php
// modulos/articulos_editar.php

// 1. Validamos que llegue el ID por la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Traemos el artículo específico que queremos editar
    $query = "SELECT * FROM articulos WHERE id_articulos = $id LIMIT 1";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $producto = mysqli_fetch_assoc($resultado);
    } else {
        // Si no existe el ID, volvemos al listado de forma segura
        echo "<script>window.location.href='index.php?page=articulos';</script>";
        exit();
    }
} else {
    echo "<script>window.location.href='index.php?page=articulos';</script>";
    exit();
}

// 2. Traemos las categorías para armar el desplegable relacional (Select)
$query_categories = "SELECT * FROM categorias ORDER BY nombre ASC";
$res_categorias = mysqli_query($conexion, $query_categories);
?>

<div class="container py-4" style="max-width: 600px;">
    <div class="mb-4">
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Artículo</h3>
        <p class="text-muted small">Modificá los detalles y la clasificación del artículo seleccionado.</p>
    </div>

    <div class="main-card p-4" style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px;">
        <!-- El formulario procesa en caliente en tu archivo oculto de lógica -->
        <form action="php/articulos_actualizar.php" method="POST">
            
            <!-- ID oculto para saber cuál actualizar en la base de datos -->
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <!-- Nombre -->
            <div class="mb-3">
                <label class="form-label fw-semibold small">Descripción / Nombre del Producto</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>

            <!-- Categoría (Desplegable Dinámico Relacional) -->
            <div class="mb-3">
                <label class="form-label fw-semibold small">Categoría</label>
                <select name="id_categorias" class="form-select" required>
                    <option value="">Seleccione una categoría</option>
                    <?php if ($res_categorias && mysqli_num_rows($res_categorias) > 0): ?>
                        <?php while ($cat = mysqli_fetch_assoc($res_categorias)): ?>
                            <?php 
                            // Si el ID de la categoría coincide con el del producto, lo dejamos seleccionado
                            $selected = ($cat['id_categorias'] == $producto['id_categorias']) ? 'selected' : ''; 
                            ?>
                            <option value="<?php echo $cat['id_categorias']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Precios -->
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small">Precio de Costo ($)</label>
                    <input type="number" step="0.01" name="precio_costo" class="form-control" value="<?php echo $producto['precio_costo']; ?>" required>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small">Precio de Venta ($)</label>
                    <input type="number" step="0.01" name="precio_venta" class="form-control" value="<?php echo $producto['precio_venta']; ?>" required>
                </div>
            </div>

            <!-- Stock -->
            <div class="mb-4" style="max-width: 200px;">
                <label class="form-label fw-semibold small">Stock Actual</label>
                <input type="number" name="stock" class="form-control" value="<?php echo $producto['stock']; ?>" required>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex gap-2 justify-content-end">
                <a href="index.php?page=articulos" class="btn btn-light border px-4">Cancelar</a>
                <button type="submit" class="btn px-4 fw-semibold" style="background-color: var(--accent-green); color: white;">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>