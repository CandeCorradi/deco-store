<?php
// login/procesar_login.php
session_start();

include '../php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_input = mysqli_real_escape_string($conexion, trim($_POST['usuario'])); 
    $pass_input = trim($_POST['contrasenia']); 

    // Buscamos el usuario por su nombre_usuario o por su email
    $query = "SELECT * FROM usuarios WHERE (nombre_usuario = '$user_input' OR email = '$user_input') AND esta_activo = 1 AND esta_eliminado = 0 LIMIT 1";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);
        
        $login_valido = false;

        // 1. Verificación segura de Hash
        if (password_verify($pass_input, $usuario['contrasenia'])) {
            $login_valido = true;
        } 
        // 2. Compatibilidad con contraseñas viejas en texto plano
        else if ($pass_input === $usuario['contrasenia']) {
            $login_valido = true;
            
            // Migración automática a Hash en la BD
            $id_u = intval($usuario['id_usuarios']);
            $nuevo_hash = password_hash($pass_input, PASSWORD_DEFAULT);
            mysqli_query($conexion, "UPDATE usuarios SET contrasenia = '$nuevo_hash' WHERE id_usuarios = $id_u");
        }

        if ($login_valido) {
            // Guardamos las variables de sesión exactas
            $_SESSION['id_usuarios']     = $usuario['id_usuarios']; 
            $_SESSION['nombre_usuario']  = $usuario['nombre_usuario'];
            $_SESSION['nombre_completo'] = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''));
            $_SESSION['rol']             = $usuario['rol'];
            
            header("Location: ../index.php");
            exit();
        }
    }
    
    // Si falló usuario, clave o estado inactivo
    header("Location: index.php?error=1");
    exit();
}
?>