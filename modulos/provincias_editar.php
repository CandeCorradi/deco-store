<?php
// modulos/provincias_editar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>window.location.href = 'index.php?page=provincias';</script>";
    exit;
}

$query_p = "SELECT * FROM provincias WHERE id_provincias = $id AND esta_eliminado = 0";
$res_p = mysqli_query($conexion, $query_p);
$provincia_data = mysqli_fetch_assoc($res_p);

if (!$provincia_data) {
    echo "<div class='alert alert-danger m-4'>La provincia no existe o fue eliminada.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));

    if (!empty($nombre)) {
        $update = "UPDATE provincias SET nombre = '$nombre' WHERE id_provincias = $id";

        if (mysqli_query($conexion, $update)) {
            echo "<script>window.location.href = 'index.php?page=provincias';</script>";
            exit;
        } else {
            $error = "Error al actualizar la provincia: " . mysqli_error($conexion);
        }
    } else {
        $error = "El nombre de la provincia es obligatorio.";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Provincia</h3>
        <p class="text-muted small mb-0">Modificá el nombre de la provincia #<?php echo $id; ?>.</p>
    </div>
    <a href="index.php?page=provincias" class="btn btn-outline-secondary fw-semibold">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 600px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Nombre de la Provincia</label>
            <input type="text" name="nombre" class="form-control border" value="<?php echo htmlspecialchars($provincia_data['nombre']); ?>" required>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=provincias" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>