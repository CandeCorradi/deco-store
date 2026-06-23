<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Ingreso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-warm-beige: #FDFCF6;
            --text-dark-brown: #3E2723;
            --accent-green: #2E7D32;
            --accent-green-hover: #1B5E20;
            --border-color: #EAE1D0;
        }

        body {
            background-color: var(--bg-warm-beige);
            color: var(--text-dark-brown);
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(62, 39, 35, 0.04);
            padding: 2.5rem;
            max-width: 400px;
            width: 100%;
        }

        .btn-deco {
            background-color: var(--accent-green);
            color: white;
            border: none;
            transition: background 0.3s ease;
        }

        .btn-deco:hover {
            background-color: var(--accent-green-hover);
            color: white;
        }

        .form-control:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h2 class="text-uppercase fw-bold" style="color: var(--accent-green); letter-spacing: 1px;">
            <i class="fas fa-leaf me-2"></i>Deco<span style="font-weight: 300; color: #5D4037;">Store</span>
        </h2>
        <p class="text-muted small">Gestión de Interiores & Mobiliario</p>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger py-2 text-center small" role="alert">
            Usuario o contraseña incorrectos.
        </div>
    <?php endif; ?>

    <form action="procesar_login.php" method="POST">
        <div class="mb-3">
            <label for="usuario" class="form-label small fw-semibold">Usuario</label>
            <input type="text" class="form-control" id="usuario" name="usuario" required autocomplete="off">
        </div>
        
        <div class="mb-4">
            <label for="contrasenia" class="form-label small fw-semibold">Contraseña</label>
            <input type="password" class="form-control" id="contrasenia" name="contrasenia" required>
        </div>

        <button type="submit" class="btn btn-deco w-100 py-2 fw-semibold">Ingresar al Sistema</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>