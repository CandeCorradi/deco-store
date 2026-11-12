<?php
// modulos/usuarios_editar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "<script>window.location.href = 'index.php?page=usuarios';</script>";
    exit;
}

$query_u = "SELECT * FROM usuarios WHERE id_usuarios = $id AND esta_eliminado = 0";
$res_u = mysqli_query($conexion, $query_u);
$usuario = mysqli_fetch_assoc($res_u);

if (!$usuario) {
    echo "<div class='alert alert-danger m-4'>El usuario no existe o fue eliminado.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = mysqli_real_escape_string($conexion, trim($_POST['nombre_usuario']));
    $contrasenia    = mysqli_real_escape_string($conexion, trim($_POST['contrasenia']));
    $nombre         = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $apellido       = mysqli_real_escape_string($conexion, trim($_POST['apellido']));
    $email          = mysqli_real_escape_string($conexion, trim($_POST['email']));
    $rol            = mysqli_real_escape_string($conexion, $_POST['rol']);
    $esta_activo    = isset($_POST['esta_activo']) ? 1 : 0;

    // Si dejó la contraseña en blanco, conservamos la que ya tenía
    $pass_final = !empty($contrasenia) ? $contrasenia : $usuario['contrasenia'];

    $update = "UPDATE usuarios SET 
                nombre_usuario = '$nombre_usuario', 
                contrasenia = '$pass_final', 
                nombre = '$nombre', 
                apellido = '$apellido', 
                email = '$email', 
                rol = '$rol', 
                esta_activo = $esta_activo 
               WHERE id_usuarios = $id";

    if (mysqli_query($conexion, $update)) {
        echo "<script>window.location.href = 'index.php?page=usuarios';</script>";
        exit;
    } else {
        $error = "Error al actualizar el usuario: " . mysqli_error($conexion);
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-user-edit me-2 text-success"></i>Editar Usuario</h3>
        <p class="text-muted small mb-0">Modificá los datos del usuario #<?php echo $id; ?>.</p>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger shadow-sm mb-4"><?php echo $error; ?></div>
<?php endif; ?>

<div class="main-card p-4 mx-auto" style="max-width: 650px; background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px;">
    <form action="" method="POST" autocomplete="off">
        
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Nombre de Usuario (*)</label>
                <input type="text" name="nombre_usuario" class="form-control border" value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Contraseña</label>
                <input type="password" name="contrasenia" class="form-control border" placeholder="Dejar en blanco para no cambiar">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Nombre</label>
                <input type="text" name="nombre" class="form-control border" value="<?php echo htmlspecialchars($usuario['nombre'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Apellido</label>
                <input type="text" name="apellido" class="form-control border" value="<?php echo htmlspecialchars($usuario['apellido'] ?? ''); ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Correo Electrónico</label>
            <input type="email" name="email" class="form-control border" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>">
        </div>

        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Rol del Usuario</label>
                <select name="rol" class="form-select border" required>
                    <option value="employee" <?php echo ($usuario['rol'] === 'employee') ? 'selected' : ''; ?>>Empleado / Employee</option>
                    <option value="admin" <?php echo ($usuario['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador / Admin</option>
                </select>
            </div>
            <div class="col-md-6 pt-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="esta_activo" id="checkActivo" <?php echo ($usuario['esta_activo'] == 1) ? 'checked' : ''; ?>>
                    <label class="form-check-input-label fw-semibold text-dark" for="checkActivo">Usuario Activo</label>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=usuarios" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Cambios</button>
        </div>
    </form>
</div>