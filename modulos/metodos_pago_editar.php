<?php
// modulos/metodos_pago_editar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>window.location.href = 'index.php?page=metodos_pago';</script>";
    exit;
}

$query_m = "SELECT * FROM metodos_pago WHERE id_metodos_pago = $id AND esta_eliminado = 0";
$res_m = mysqli_query($conexion, $query_m);
$metodo = mysqli_fetch_assoc($res_m);

if (!$metodo) {
    echo "<div class='alert alert-danger m-4'>El método de pago no existe o fue eliminado.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));

    if (!empty($nombre)) {
        $update = "UPDATE metodos_pago SET nombre = '$nombre' WHERE id_metodos_pago = $id";

        if (mysqli_query($conexion, $update)) {
            echo "<script>window.location.href = 'index.php?page=metodos_pago';</script>";
            exit;
        } else {
            $error = "Error al actualizar el método de pago: " . mysqli_error($conexion);
        }
    } else {
        $error = "El nombre es obligatorio.";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Método de Pago</h3>
        <p class="text-muted small mb-0">Modificá la opción de cobro #<?php echo $id; ?>.</p>
    </div>
    <a href="index.php?page=metodos_pago" class="btn btn-outline-secondary fw-semibold">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 600px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Nombre del Método de Pago</label>
            <input type="text" name="nombre" class="form-control border" value="<?php echo htmlspecialchars($metodo['nombre']); ?>" required>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=metodos_pago" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>