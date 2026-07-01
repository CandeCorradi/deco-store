<?php
// login/index.php - Pantalla de Inicio de Sesión
session_start();

// Si el usuario ya estaba logueado, lo mandamos directo al panel de la raíz
if (isset($_SESSION['id_usuarios'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Ingreso al Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #FDFCF6; font-family: 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-card { background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px; box-shadow: 0 4px 12px rgba(62, 39, 35, 0.05); width: 100%; max-width: 400px; padding: 2.5rem; }
        .btn-success { background-color: #2E7D32; border: none; }
        .btn-success:hover { background-color: #1B5E20; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h2 class="fw-bold" style="color: #3E2723;"><i class="fas fa-leaf text-success me-2"></i>DECOSTORE</h2>
        <p class="text-muted small">Gestión de Estudio & Showroom</p>
    </div>

    <form action="procesar_login.php" method="POST">
        <div class="mb-3">
            <label for="usuario" class="form-label text-muted small fw-bold">Usuario o Email</label>
            <input type="text" name="usuario" id="usuario" class="form-control" required placeholder="admin">
        </div>
        <div class="mb-4">
    <label for="password" class="form-label text-muted small fw-bold">Contraseña</label>
    <input type="password" name="contrasenia" id="password" class="form-control" required placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
            <i class="fas fa-sign-in-alt me-2"></i> Ingresar al Panel
        </button>
    </form>
    </button>
    </form>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center small mt-3 py-2">
            Usuario o contraseña incorrectos.
        </div>
    <?php endif; ?>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>