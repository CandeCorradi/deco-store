<?php
// modulos/usuarios_agregar.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = mysqli_real_escape_string($conexion, trim($_POST['nombre_usuario']));
    $contrasenia_plana = trim($_POST['contrasenia']);
    $nombre         = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $apellido       = mysqli_real_escape_string($conexion, trim($_POST['apellido']));
    $email          = mysqli_real_escape_string($conexion, trim($_POST['email']));
    $rol            = mysqli_real_escape_string($conexion, $_POST['rol']);
    $esta_activo    = isset($_POST['esta_activo']) ? 1 : 0;

    // Validamos que el nombre de usuario no exista previamente
    $q_check = "SELECT id_usuarios FROM usuarios WHERE nombre_usuario = '$nombre_usuario' AND esta_eliminado = 0";
    $r_check = mysqli_query($conexion, $q_check);

    if (mysqli_num_rows($r_check) > 0) {
        $error = "El nombre de usuario '$nombre_usuario' ya se encuentra registrado.";
    } else {
        // 🔑 Encriptamos la contraseña con algoritmo Bcrypt seguro
        $contrasenia_hash = password_hash($contrasenia_plana, PASSWORD_DEFAULT);

        $insert = "INSERT INTO usuarios (nombre_usuario, contrasenia, rol, esta_activo, esta_eliminado, nombre, apellido, email) 
                   VALUES ('$nombre_usuario', '$contrasenia_hash', '$rol', $esta_activo, 0, '$nombre', '$apellido', '$email')";

        if (mysqli_query($conexion, $insert)) {
            echo "<script>window.location.href = 'index.php?page=usuarios';</script>";
            exit;
        } else {
            $error = "Error al crear el usuario: " . mysqli_error($conexion);
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-user-plus me-2 text-success"></i>Nuevo Usuario</h3>
        <p class="text-muted small mb-0">Cargá las credenciales y datos personales del nuevo operador.</p>
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
                <input type="text" name="nombre_usuario" class="form-control border" placeholder="Ej: candela_admin" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Contraseña (*)</label>
                <input type="password" name="contrasenia" class="form-control border" placeholder="••••••••" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Nombre</label>
                <input type="text" name="nombre" class="form-control border" placeholder="Ej: Andrea">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Apellido</label>
                <input type="text" name="apellido" class="form-control border" placeholder="Ej: Tolosa">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-dark">Correo Electrónico</label>
            <input type="email" name="email" class="form-control border" placeholder="ejemplo@correo.com">
        </div>

        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark">Rol del Usuario</label>
                <select name="rol" class="form-select border" required>
                    <option value="employee">Empleado / Employee</option>
                    <option value="admin">Administrador / Admin</option>
                </select>
            </div>
            <div class="col-md-6 pt-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="esta_activo" id="checkActivo" checked>
                    <label class="form-check-input-label fw-semibold text-dark" for="checkActivo">Usuario Activo</label>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-3">
            <a href="index.php?page=usuarios" class="btn btn-light px-4 me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-success px-4 fw-semibold">Guardar Usuario</button>
        </div>
    </form>
</div>