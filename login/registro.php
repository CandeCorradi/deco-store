<?php
session_start();

// Incluimos la conexión subiendo un nivel a la carpeta principal
if (file_exists('../php/conexion.php')) {
    include '../php/conexion.php';
} elseif (file_exists('../conexion.php')) {
    include '../conexion.php';
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizamos los datos que vienen del formulario
    $usuario  = trim($_POST['nombre_usuario']);
    $nombre   = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email    = trim($_POST['email']);
    $mipass   = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Validaciones básicas
    if (empty($usuario) || empty($nombre) || empty($apellido) || empty($email) || empty($mipass) || empty($confirm)) {
        $error = "Por favor, completá todos los campos obligatorios.";
    } elseif ($mipass !== $confirm) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // 1. Verificamos si el usuario o email ya existen
        $check_sql = "SELECT id_usuarios FROM usuarios WHERE nombre_usuario = '$usuario' OR email = '$email'";
        $check_res = mysqli_query($conexion, $check_sql);

        if ($check_res && mysqli_num_rows($check_res) > 0) {
            $error = "El nombre de usuario o email ya se encuentra registrado.";
        } else {
            // 2. Encriptamos la contraseña según el estándar de PHP
            $hash = password_hash($mipass, PASSWORD_DEFAULT);

            // 3. Insertamos respetando la estructura exacta de tu base de datos
            $sql = "INSERT INTO usuarios (
                        nombre_usuario, 
                        contrasenia, 
                        nombre, 
                        apellido, 
                        email, 
                        cuil_cuit, 
                        rol,
                        esta_activo, 
                        esta_eliminado
                    ) VALUES (
                        '$usuario', 
                        '$hash', 
                        '$nombre', 
                        '$apellido', 
                        '$email', 
                        '0', 
                        'employee',
                        1, 
                        0
                    )";

            $resp = mysqli_query($conexion, $sql);

            if ($resp) {
                // Registro exitoso -> Redirigimos al Login de la misma carpeta
                header("Location: index.php?registrado=1");
                exit;
            } else {
                $error = "Error en la base de datos: " . mysqli_error($conexion);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Crear Cuenta</title>
    <style>
        body {
            background-color: #f7f6f2;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem 0;
            box-sizing: border-box;
        }
        .card-login {
            background: #ffffff;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logo-title {
            color: #2e7d32;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 0.3rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .subtitle {
            color: #757575;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #4a4a4a;
            margin-bottom: 0.3rem;
        }
        .form-control {
            width: 100%;
            padding: 0.65rem 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #2e7d32;
        }
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 0.5rem;
        }
        .btn-primary:hover {
            background-color: #236127;
        }
        .alert {
            padding: 0.7rem;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            text-align: left;
        }
        .alert-danger { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .link-volver {
            display: inline-block;
            margin-top: 1.2rem;
            color: #2e7d32;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .link-volver:hover {
            text-decoration: underline;
        }
        .row-2 {
            display: flex;
            gap: 0.5rem;
        }
    </style>
</head>
<body>

<div class="card-login">
    <div class="logo-title">DECOSTORE</div>
    <div class="subtitle">Crear cuenta de acceso al panel</div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="row-2">
            <div class="form-group" style="flex: 1;">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Carlos" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" class="form-control" placeholder="Ej: Perez" required>
            </div>
        </div>

        <div class="form-group">
            <label for="nombre_usuario">Nombre de Usuario</label>
            <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" placeholder="Ej: cande_p" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="correo@ejemplo.com" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirmar Contraseña</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-primary">Crear Cuenta</button>
    </form>

    <a href="index.php" class="link-volver">← Volver al Inicio</a>
</div>

</body>
</html>