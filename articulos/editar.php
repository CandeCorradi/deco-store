<?php
// articulos/editar.php
session_start();
include '../php/conexion.php';

// 1. Verificar que nos llegue el ID por la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Traer los datos del producto actual
    $query = "SELECT * FROM articulos WHERE id_articulos = $id LIMIT 1";
    $resultado = mysqli_query($conexion, $query);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $producto = mysqli_fetch_assoc($resultado);
    } else {
        header("Location: listar.php");
        exit();
    }
} else {
    header("Location: listar.php");
    exit();
}

// 2. Traer TODAS las categorías para armar el desplegable (Select)
$query_categorias = "SELECT * FROM categorias ORDER BY nombre ASC";
$res_categorias = mysqli_query($conexion, $query_categorias);

// 3. Procesar el formulario cuando se le da a "Guardar Cambios"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $id_categoria = intval($_POST['id_categorias']);
    $precio_costo = floatval($_POST['precio_costo']);
    $precio_venta = floatval($_POST['precio_venta']);
    $stock = intval($_POST['stock']);
    
    // ⚠️ ACTUALIZADO: Ahora también guardamos la categoría elegida
    $query_update = "UPDATE articulos SET 
                    nombre = '$nombre', 
                    id_categorias = $id_categoria,
                    precio_costo = $precio_costo, 
                    precio_venta = $precio_venta, 
                    stock = $stock 
                    WHERE id_articulos = $id";
                    
    if (mysqli_query($conexion, $query_update)) {
        header("Location: listar.php?editado=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Editar Artículo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #FDFCF6; color: #3E2723; font-family: 'Segoe UI', sans-serif; }
        .form-card { background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px; box-shadow: 0 4px 12px rgba(62, 39, 35, 0.02); }
        .btn-save { background-color: #2E7D32; color: white; }
        .btn-save:hover { background-color: #1B5E20; color: white; }
    </style>
</head>
<body>

<?php include '../componentes/menu.php'; ?>

<div class="container py-4" style="max-width: 600px;">
    <div class="mb-4">
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Artículo</h3>
        <p class="text-muted small">Modificá los detalles y la clasificación del artículo seleccionado.</p>
    </div>

    <div class="form-card p-4">
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Descripción / Nombre del Producto</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-semibold small">Categoría</label>
                <select name="id_categorias" class="form-select" required>
                    <option value="">Seleccione una categoría</option>
                    <?php if ($res_categorias && mysqli_num_rows($res_categorias) > 0): ?>
                        <?php while ($cat = mysqli_fetch_assoc($res_categorias)): ?>
                            <?php 
                                // Comparamos si esta categoría es la que ya tiene asignada el producto
                                $selected = ($cat['id_categorias'] == $producto['id_categorias']) ? 'selected' : ''; 
                            ?>
                            <option value="<?php echo $cat['id_categorias']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            
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

            <div class="mb-4" style="max-width: 200px;">
                <label class="form-label fw-semibold small">Stock Actual</label>
                <input type="number" name="stock" class="form-control" value="<?php echo $producto['stock']; ?>" min="0" required>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="listar.php" class="btn btn-light border px-4">Cancelar</a>
                <button type="submit" class="btn btn-save px-4 fw-semibold">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>