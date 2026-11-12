<?php
// modulos/cuentas_agregar.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cuenta = mysqli_real_escape_string($conexion, trim($_POST['cuenta']));

    if (!empty($cuenta)) {
        $insert = "INSERT INTO cuentas (cuenta, esta_eliminado) VALUES ('$cuenta', 0)";
        if (mysqli_query($conexion, $insert)) {
            echo "<script>window.location.href = 'index.php?page=cuentas';</script>";
            exit;
        } else { $error = "Error al guardar: " . mysqli_error($conexion); }
    } else { $error = "El nombre de la cuenta es obligatorio."; }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-1"><i class="fas fa-plus me-2 text-success"></i>Nueva Cuenta</h3>
</div>

<?php if (isset($error)): ?><div class="alert alert-danger mb-4"><?php echo $error; ?></div><?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 600px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        <div class="mb-4">
            <label class="form-label fw-semibold text-dark">Nombre de la Cuenta / Caja (*)</label>
            <input type="text" name="cuenta" class="form-control border" placeholder="Ej: Mercado Pago, Efectivo Mostrador" required>
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=cuentas" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cuenta</button>
        </div>
    </form>
</div>