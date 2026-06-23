<?php
// articulos/crear.php
session_start();
include '../php/conexion.php';

// 1. Traer las categorías desde la base de datos para llenar el combo box
// ⚠️ Asegurate de que tu tabla en phpMyAdmin se llame 'categorias' y tenga 'id_categoria' y 'nombre'
$query_categorias = "SELECT * FROM categorias ORDER BY nombre ASC";
$res_categorias = mysqli_query($conexion, $query_categorias);

// 2. Procesar el formulario cuando el usuario hace clic en "Guardar Artículo"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $id_categoria = intval($_POST['id_categorias']);
    $precio_costo = floatval($_POST['precio_costo']);
    $precio_venta = floatval($_POST['precio_venta']);
    $stock = intval($_POST['stock']);
    
    // ⚠️ Sincronizado: Insertamos en 'articulos' usando la columna 'id_categorias'
    $query_insert = "INSERT INTO articulos (nombre, id_categorias, precio_costo, precio_venta, stock, esta_eliminado) 
                     VALUES ('$nombre', $id_categoria, $precio_costo, $precio_venta, $stock, 0)";
                    
    if (mysqli_query($conexion, $query_insert)) {
        header("Location: listar.php?creado=1");
        exit();
    } else {
        $error_msg = "Error al guardar en la base de datos: " . mysqli_error($conexion);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Nuevo Artículo</title>
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
    
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error_msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="mb-4">
        <h3 class="fw-bold mb-1"><i class="fas fa-plus me-2 text-success"></i>Nuevo Artículo</h3>
        <p class="text-muted small">Cargá un nuevo mueble o accesorio al inventario del showroom.</p>
    </div>

    <div class="form-card p-4">
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Descripción / Nombre del Producto</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej: Sillón Velvet 3 cuerpos" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-semibold small">Categoría</label>
                <select name="id_categorias" class="form-select" required>
                    <option value="">Seleccione una categoría</option>
                    <?php if ($res_categorias && mysqli_num_rows($res_categorias) > 0): ?>
                        <?php while ($cat = mysqli_fetch_assoc($res_categorias)): ?>
                            <option value="<?php echo $cat['id_categorias']; ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="" disabled>⚠️ No se encontraron categorías en la base de datos</option>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small">Precio de Costo ($)</label>
                    <input type="number" step="0.01" name="precio_costo" class="form-control" placeholder="0.00" required>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small">Precio de Venta ($)</label>
                    <input type="number" step="0.01" name="precio_venta" class="form-control" placeholder="0.00" required>
                </div>
            </div>

            <div class="mb-4" style="max-width: 200px;">
                <label class="form-label fw-semibold small">Stock Inicial</label>
                <input type="number" name="stock" class="form-control" value="1" min="0" required>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="listar.php" class="btn btn-light border px-4">Cancelar</a>
                <button type="submit" class="btn btn-save px-4 fw-semibold">Guardar Artículo</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>