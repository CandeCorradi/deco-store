<?php
// modulos/articulos_agregar.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $id_categoria = intval($_POST['id_categorias']); 
    $precio_costo = floatval($_POST['precio_costo']);
    $precio_venta = floatval($_POST['precio_venta']);
    $stock = intval($_POST['stock']);

    // Ejecutamos el INSERT. Controlá que tu tabla se llame 'articulos'
    $query_insert = "INSERT INTO articulos (nombre, id_categorias, precio_costo, precio_venta, stock) 
                    VALUES ('$nombre', $id_categoria, $precio_costo, $precio_venta, $stock)";

    if (mysqli_query($conexion, $query_insert)) {
        // Si todo sale bien, volvemos limpios al catálogo
        echo "<script>window.location.href='index.php?page=articulos';</script>";
        exit();
    } else {
        // 🚨 SI LA BASE DE DATOS RECHAZA ALGO, ESTO TE VA A DECIR QUÉ PASÓ:
        die("Error de MySQL: " . mysqli_error($conexion));
    }
}

// Tu consulta normal para el select de abajo
$query_categories = "SELECT * FROM categorias ORDER BY nombre ASC";
$res_categorias = mysqli_query($conexion, $query_categories);
?>

<div class="container py-4" style="max-width: 600px;">
    <div class="mb-4">
        <h3 class="fw-bold mb-1"><i class="fas fa-plus me-2 text-success"></i>Nuevo Artículo</h3>
        <p class="text-muted small">Cargá un nuevo mueble o accesorio al inventario del showroom.</p>
    </div>

    <div class="main-card p-4" style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px;">
        <!-- El formulario apunta al procesador que acabamos de crear en php/ -->

        <form action="index.php?page=articulos_agregar" method="POST">
            
            
            <!-- Nombre -->
            <div class="mb-3">
                <label class="form-label fw-semibold small">Descripción / Nombre del Producto</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej: Sillón Velvet 3 cuerpos" required>
            </div>

            <!-- Categoría (Desplegable Dinámico Relacional) -->
            <div class="mb-3">
                <label class="form-label fw-semibold small">Categoría</label>
                <!-- Le ponemos name="id_categorias" para que coincida con tu base de datos -->
                <select name="id_categorias" class="form-select" required>
                    <option value="">Seleccione una categoría</option>
                    <?php if ($res_categorias && mysqli_num_rows($res_categorias) > 0): ?>
                        <?php while ($cat = mysqli_fetch_assoc($res_categorias)): ?>
                            <option value="<?php echo $cat['id_categorias']; ?>">
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
                    <input type="number" step="0.01" name="precio_costo" class="form-control" value="0.00" required>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small">Precio de Venta ($)</label>
                    <input type="number" step="0.01" name="precio_venta" class="form-control" value="0.00" required>
                </div>
            </div>

            <!-- Stock Inicial -->
            <div class="mb-4" style="max-width: 200px;">
                <label class="form-label fw-semibold small">Stock Inicial</label>
                <input type="number" name="stock" class="form-control" value="1" min="0" required>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex gap-2 justify-content-end">
                <a href="index.php?page=articulos" class="btn btn-light border px-4">Cancelar</a>
                <button type="submit" class="btn px-4 fw-semibold text-white" style="background-color: var(--accent-green);">Guardar Artículo</button>
            </div>
        </form>
    </div>
</div>