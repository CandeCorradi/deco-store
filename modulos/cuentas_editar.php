<?php
// modulos/cuentas_editar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { echo "<script>window.location.href = 'index.php?page=cuentas';</script>"; exit; }

$res = mysqli_query($conexion, "SELECT * FROM cuentas WHERE id_cuenta = $id AND esta_eliminado = 0");
$c = mysqli_fetch_assoc($res);
if (!$c) { echo "<div class='alert alert-danger m-4'>La cuenta no existe.</div>"; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cuenta = mysqli_real_escape_string($conexion, trim($_POST['cuenta']));
    if (!empty($cuenta)) {
        if (mysqli_query($conexion, "UPDATE cuentas SET cuenta = '$cuenta' WHERE id_cuenta = $id")) {
            echo "<script>window.location.href = 'index.php?page=cuentas';</script>";
            exit;
        } else { $error = "Error al actualizar: " . mysqli_error($conexion); }
    } else { $error = "El nombre es obligatorio."; }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Cuenta</h3>
</div>

<?php if (isset($error)): ?><div class="alert alert-danger mb-4"><?php echo $error; ?></div><?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 600px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Nombre de la Cuenta (*)</label>
            <input type="text" name="cuenta" class="form-control border" value="<?php echo htmlspecialchars($c['cuenta']); ?>" required>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=cuentas" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>