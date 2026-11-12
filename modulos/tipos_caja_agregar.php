<?php
// modulos/tipos_caja_agregar.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));

    if (!empty($nombre)) {
        $insert = "INSERT INTO tipos_caja (nombre, esta_eliminado) VALUES ('$nombre', 0)";

        if (mysqli_query($conexion, $insert)) {
            echo "<script>window.location.href = 'index.php?page=tipos_caja';</script>";
            exit;
        } else {
            $error = "Error al crear el tipo de caja: " . mysqli_error($conexion);
        }
    } else {
        $error = "El nombre es obligatorio.";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-plus me-2 text-success"></i>Nuevo Tipo de Caja</h3>
        <p class="text-muted small mb-0">Cargá una nueva clasificación para el flujo de caja.</p>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 600px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Nombre del Tipo de Movimiento</label>
            <input type="text" name="nombre" class="form-control border" placeholder="Ej: Ingreso, Egreso, Ajuste de Caja" required>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=tipos_caja" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Tipo</button>
        </div>
    </form>
</div>