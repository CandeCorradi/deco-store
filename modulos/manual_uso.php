<?php
// Si tus módulos validan sesión o incluyen el header común de la aplicación, podés incluirlo acá.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual de Uso - DecoStore</title>
    <!-- Bootstrap 5 y FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card-manual {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
        .card-manual .card-header {
            background-color: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            font-weight: bold;
            color: #198754; /* Verde institucional */
        }
        .hero-manual {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .badge-step {
            background-color: #198754;
            color: white;
            font-size: 0.9rem;
            padding: 5px 10px;
            border-radius: 50px;
        }
        /* Estilos para impresión limpia */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
            }
            .card-manual, .hero-manual {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="p-4">

<div class="container-fluid">
    <!-- Encabezado y Botones de Acción -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="fw-bold text-dark"><i class="fas fa-book-open text-success me-2"></i> Manual de Uso - DecoStore</h3>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary me-2"><i class="fas fa-print me-1"></i> Imprimir / PDF</button>
            <a href="../index.php" class="btn btn-success"><i class="fas fa-arrow-left me-1"></i> Volver al Dashboard</a>
        </div>
    </div>

    <!-- Banner Principal -->
    <div class="hero-manual">
        <span class="badge bg-success mb-2">GUÍA RÁPIDA DE OPERACIÓN</span>
        <h1 class="h4 fw-bold text-dark">TODO LO NECESARIO PARA OPERAR DECOSTORE</h1>
    </div>

    <!-- Sección 1: Ingreso y Autenticación -->
    <div class="card card-manual">
        <div class="card-header"><span class="badge-step me-2">1</span> Ingreso y Autenticación al Sistema</div>
        <div class="card-body">
            <p>Para ingresar al sistema, abrí la aplicación en el navegador e introducé tus credenciales de <strong>usuario</strong> y <strong>contraseña</strong>.</p>
            <ul class="mb-0">
                <li><strong>Cierre de sesión:</strong> Podés salir de forma segura desde el botón superior derecho.</li>
                <li><strong>Seguridad:</strong> El sistema valida roles y permisos para restringir accesos a áreas sensibles.</li>
            </ul>
        </div>
    </div>

    <!-- Sección 2: Dashboard General -->
    <div class="card card-manual">
        <div class="card-header"><span class="badge-step me-2">2</span> Dashboard y Métricas</div>
        <div class="card-body">
            <p>Es el panel principal de control del showroom:</p>
            <ul class="mb-0">
                <li><strong>Indicadores Clave:</strong> Visualización de ventas totales del período, ticket promedio y clientes.</li>
                <li><strong>Accesos Rápidos:</strong> Botones directos para iniciar ventas o consultar la caja.</li>
            </ul>
        </div>
    </div>

    <!-- Sección 3: Artículos y Stock -->
    <div class="card card-manual">
        <div class="card-header"><span class="badge-step me-2">3</span> Gestión de Artículos y Stock</div>
        <div class="card-body">
            <p>Control del inventario de muebles y objetos de decoración:</p>
            <ul class="mb-0">
                <li>Permite registrar productos, modificar precios y actualizar el stock en tiempo real al concretar ventas.</li>
            </ul>
        </div>
    </div>

    <!-- Sección 4: Ventas y Facturación -->
    <div class="card card-manual">
        <div class="card-header"><span class="badge-step me-2">4</span> Ventas y Facturación B (Monotributo)</div>
        <div class="card-body">
            <p>Núcleo comercial del sistema:</p>
            <ul class="mb-0">
                <li><strong>Comprobante Fijo:</strong> Emisión estandarizada bajo <strong>Factura B</strong>.</li>
                <li><strong>Concepto Global:</strong> Registro limpio del monto total de la operación sin fricciones.</li>
                <li><strong>Ordenamiento cronológico:</strong> Las ventas se listan automáticamente de forma descendente (`ORDER BY fecha DESC`), mostrando la última operación arriba.</li>
            </ul>
        </div>
    </div>

    <!-- Sección 5: Control de Caja y Horarios -->
    <div class="card card-manual">
        <div class="card-header"><span class="badge-step me-2">5</span> Control de Caja y Sincronización Horaria</div>
        <div class="card-body">
            <p>Administración de flujos de dinero:</p>
            <ul class="mb-0">
                <li>Configurado en la zona horaria de Argentina (`America/Argentina/Buenos_Aires` y `SET time_zone = '-03:00'`) para registrar la hora local exacta en cada operación.</li>
            </ul>
        </div>
    </div>

    <!-- Sección 6: Clientes -->
    <div class="card card-manual">
        <div class="card-header"><span class="badge-step me-2">6</span> Gestión de Clientes</div>
        <div class="card-body">
            <p>Directorio de compradores:</p>
            <ul class="mb-0">
                <li>Permite dar de alta clientes, editar datos y seleccionarlos rápidamente al emitir comprobantes.</li>
            </ul>
        </div>
    </div>

</div>

</body>
</html>