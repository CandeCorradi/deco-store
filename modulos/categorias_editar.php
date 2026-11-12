<?php
// modulos/categorias_editar.php

// 1. Validamos que llegue un ID válido por la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>window.location.href = 'index.php?page=categorias';</script>";
    exit;
}

// 2. Traemos los datos actuales de la categoría
$query_cat = "SELECT * FROM categorias WHERE id_categorias = $id AND esta_eliminado = 0";
$res_cat = mysqli_query($conexion, $query_cat);
$categoria = mysqli_fetch_assoc($res_cat);

if (!$categoria) {
    echo "<div class='alert alert-danger m-4'>La categoría no existe o fue eliminada.</div>";
    exit;
}

// 3. Procesamos el formulario cuando la usuaria guarda los cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $descripcion = mysqli_real_escape_string($conexion, trim($_POST['descripcion']));

    if (!empty($nombre)) {
        $update = "UPDATE categorias SET 
                    nombre = '$nombre', 
                    descripcion = '$descripcion' 
                   WHERE id_categorias = $id";

        if (mysqli_query($conexion, $update)) {
            echo "<script>window.location.href = 'index.php?page=categorias';</script>";
            exit;
        } else {
            $error = "Error al actualizar la categoría: " . mysqli_error($conexion);
        }
    } else {
        $error = "El nombre de la categoría es obligatorio.";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Categoría</h3>
        <p class="text-muted small mb-0">Modificá los datos de la categoría #<?php echo $id; ?>.</p>
    </div>
    <a href="index.php?page=categorias" class="btn btn-outline-secondary fw-semibold">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 600px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        
        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Nombre de la Categoría</label>
            <input type="text" name="nombre" class="form-control border" value="<?php echo htmlspecialchars($categoria['nombre']); ?>" required>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Descripción <span class="text-muted fw-normal">(Opcional)</span></label>
            <textarea name="descripcion" class="form-control border" rows="3"><?php echo htmlspecialchars($categoria['descripcion'] ?? ''); ?></textarea>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=categorias" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>