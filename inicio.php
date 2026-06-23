<?php
// inicio.php
session_start();

// Control de seguridad real: Si no hay un ID de usuario en la sesión, te manda al login
/*if (!isset($_SESSION['id_usuario'])) {
    header("Location: login/index.php");
    exit();
}*/

// Traemos la conexión a la base de datos
include 'php/conexion.php';

// Variables de la sesión
$nombre_usuario = $_SESSION['nombre'];
$rol_usuario = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-warm-beige: #FDFCF6;
            --text-dark-brown: #3E2723;
            --accent-green: #2E7D32;
            --soft-brown: #5D4037;
            --border-color: #EAE1D0;
        }

        body {
            background-color: var(--bg-warm-beige);
            color: var(--text-dark-brown);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .welcome-banner {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 12px rgba(62, 39, 35, 0.02);
        }

        .card-menu {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-dark-brown);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-menu:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(46, 125, 50, 0.08);
            color: var(--text-dark-brown);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background-color: #F4F1EA;
            color: var(--accent-green);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.2rem;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .card-menu:hover .icon-box {
            background-color: var(--accent-green);
            color: #FFFFFF;
        }
    </style>
</head>
<body>

<?php include 'componentes/menu.php'; ?>

<div class="container py-4">
    
    <div class="welcome-banner d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <span class="badge mb-2 px-3 py-2 text-uppercase" style="background-color: #F4F1EA; color: var(--soft-brown); font-weight: 600; letter-spacing: 0.5px;">
                <i class="fas fa-user-shield me-1"></i> <?php echo $rol_usuario; ?>
            </span>
            <h1 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Bienvenido, <?php echo $nombre_usuario; ?></h1>
            <p class="text-muted mb-0 small">Bienvenida de nuevo a la gestión de tu estudio y showroom.</p>
        </div>
        <div class="text-end d-none d-sm-block">
            <p class="text-muted small mb-0"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y'); ?></p>
            <p class="fw-semibold small text-uppercase" style="color: var(--accent-green); font-size: 0.75rem; letter-spacing: 1px;">Conexión Segura</p>
        </div>
    </div>

    <h5 class="text-uppercase fw-bold mb-3 small text-muted" style="letter-spacing: 1.5px;">Gestión Comercial</h5>
    <div class="row g-4 mb-5">
        
        <div class="col-md-4 col-sm-6">
            <a href="articulos/listar.php" class="card-menu p-4">
                <div class="icon-box">
                    <i class="fas fa-couch"></i>
                </div>
                <h4 class="fw-bold h5 mb-2">Catálogo de Artículos</h4>
                <p class="text-muted small mb-0">Gestioná tus muebles, stock, categorías de diseño y precios de venta y costo.</p>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="clientes/listar.php" class="card-menu p-4">
                <div class="icon-box">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="fw-bold h5 mb-2">Clientes del Estudio</h4>
                <p class="text-muted small mb-0">Administrá la base de datos de clientes, teléfonos, emails y provincias asociadas.</p>
            </a>
        </div>

        <div class="col-md-4 col-sm-12">
            <a href="caja/listar.php" class="card-menu p-4">
                <div class="icon-box">
                    <i class="fas fa-cash-register"></i>
                </div>
                <h4 class="fw-bold h5 mb-2">Caja Diaria</h4>
                <p class="text-muted small mb-0">Controlá los ingresos de dinero, egresos por gastos y cierres de caja con precisión decimal.</p>
            </a>
        </div>
    </div>

    <h5 class="text-uppercase fw-bold mb-3 small text-muted" style="letter-spacing: 1.5px;">Tablas Maestras</h5>
    <div class="row g-4">

        <div class="col-md-4 col-sm-6">
            <a href="categorias/listar.php" class="card-menu p-4">
                <div class="icon-box">
                    <i class="fas fa-tags"></i>
                </div>
                <h4 class="fw-bold h5 mb-2">Categorías</h4>
                <p class="text-muted small mb-0">Clasificaciones del mobiliario y rubros de decoración del showroom.</p>
            </a>
        </div>

        <div class="col-md-4 col-sm-6">
            <a href="provincias/listar.php" class="card-menu p-4">
                <div class="icon-box">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4 class="fw-bold h5 mb-2">Provincias</h4>
                <p class="text-muted small mb-0">Regiones de residencia para optimizar la base de datos de clientes.</p>
            </a>
        </div>

        <div class="col-md-4 col-sm-12">
            <a href="metodos_pago/listar.php" class="card-menu p-4">
                <div class="icon-box">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h4 class="fw-bold h5 mb-2">Métodos de Pago</h4>
                <p class="text-muted small mb-0">Formas de cobro habilitadas en las transacciones (Efectivo, Transferencia, etc.).</p>
            </a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>