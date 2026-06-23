<?php
// login/procesar_login.php
session_start();

include '../php/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_input = mysqli_real_escape_string($conexion, trim($_POST['usuario'])); 
    $pass_input = $_POST['contrasenia']; 

    $query = "SELECT * FROM usuarios WHERE nombre_usuario = '$user_input' LIMIT 1";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $usuario = mysqli_fetch_assoc($resultado);
        
        if ($pass_input == $usuario['contrasenia']) {
            
            // ⚠️ CORREGIDO: Usamos los nombres exactos de tu tabla local
            $_SESSION['id_usuario'] = $usuario['id']; // Usa 'id'
            $_SESSION['nombre']     = $usuario['nombre_completo']; // Usa 'nombre_completo'
            $_SESSION['rol']        = $usuario['rol'];
            
            header("Location: ../inicio.php");
            exit();
        }
    }
    
    header("Location: index.php?error=1");
    exit();
}
?>