<?php
// modulos/proveedores_editar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) { echo "<script>window.location.href = 'index.php?page=proveedores';</script>"; exit; }

$res = mysqli_query($conexion, "SELECT * FROM proveedores WHERE id_proveedores = $id AND esta_eliminado = 0");
$pr = mysqli_fetch_assoc($res);
if (!$pr) { echo "<div class='alert alert-danger m-4'>Proveedor no encontrado.</div>"; exit; }

$r_provs = mysqli_query($conexion, "SELECT id_provincias, nombre FROM provincias WHERE esta_eliminado = 0 ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre        = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $cuit          = mysqli_real_escape_string($conexion, trim($_POST['cuit']));
    $telefono      = mysqli_real_escape_string($conexion, trim($_POST['telefono']));
    $email         = mysqli_real_escape_string($conexion, trim($_POST['email']));
    $id_provincias = intval($_POST['id_provincias']);

    if (!empty($nombre)) {
        $update = "UPDATE proveedores SET 
                    nombre = '$nombre', 
                    cuit = '$cuit', 
                    telefono = '$telefono', 
                    email = '$email', 
                    id_provincias = $id_provincias 
                   WHERE id_proveedores = $id";
        if (mysqli_query($conexion, $update)) {
            echo "<script>window.location.href = 'index.php?page=proveedores';</script>";
            exit;
        } else { $error = "Error al actualizar: " . mysqli_error($conexion); }
    } else { $error = "El nombre es obligatorio."; }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Proveedor #<?php echo $id; ?></h3>
</div>

<?php if (isset($error)): ?><div class="alert alert-danger mb-4"><?php echo $error; ?></div><?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 650px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Nombre / Razón Social (*)</label>
            <input type="text" name="nombre" class="form-control border" value="<?php echo htmlspecialchars($pr['nombre']); ?>" required>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">CUIT</label>
                <input type="text" name="cuit" class="form-control border" value="<?php echo htmlspecialchars($pr['cuit'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Teléfono</label>
                <input type="text" name="telefono" class="form-control border" value="<?php echo htmlspecialchars($pr['telefono'] ?? ''); ?>">
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Correo Electrónico</label>
                <input type="email" name="email" class="form-control border" value="<?php echo htmlspecialchars($pr['email'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Provincia</label>
                <select name="id_provincias" class="form-select border">
                    <option value="0">-- Seleccioná --</option>
                    <?php while ($p = mysqli_fetch_assoc($r_provs)): ?>
                        <option value="<?php echo $p['id_provincias']; ?>" <?php echo ($pr['id_provincias'] == $p['id_provincias']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['nombre']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=proveedores" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>