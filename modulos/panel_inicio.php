<?php
// Mapeamos lo que viene de la sesión para que el banner lo entienda
$nombre_usuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Cande';
$rol_usuario = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'ADMIN';
?>

<div class="welcome-banner d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <span class="badge mb-2 px-3 py-2 text-uppercase" style="background-color: #F4F1EA; color: var(--soft-brown); font-weight: 600;">
                        <i class="fas fa-user-shield me-1"></i> <?php echo $rol_usuario; ?>
                    </span>
                    <h1 class="fw-bold mb-1">Bienvenido, <?php echo $nombre_usuario; ?></h1>
                    <p class="text-muted mb-0 small">Bienvenida de nuevo a la gestión de tu estudio y showroom.</p>
                </div>
                <div class="text-end d-none d-sm-block">
                    <p class="text-muted small mb-0"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y'); ?></p>
                    <p class="fw-semibold small text-uppercase" style="color: var(--accent-green); font-size: 0.75rem;">Conexión Segura</p>
                </div>
            </div>

            <h5 class="text-uppercase fw-bold mb-3 small text-muted" style="letter-spacing: 1.5px;">Gestión Comercial</h5>
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <a href="index.php?page=articulos" class="card-menu p-4">
                        <div class="icon-box"><i class="fas fa-couch"></i></div>
                        <h4 class="fw-bold h5 mb-2">Catálogo de Artículos</h4>
                        <p class="text-muted small mb-0">Gestioná tus muebles, stock, categorías de diseño y precios.</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="index.php?page=clientes" class="card-menu p-4">
                        <div class="icon-box"><i class="fas fa-users"></i></div>
                        <h4 class="fw-bold h5 mb-2">Clientes del Estudio</h4>
                        <p class="text-muted small mb-0">Administrá la base de datos de clientes, teléfonos, e-mails y provincias.</p>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="index.php?page=caja" class="card-menu p-4">
                        <div class="icon-box"><i class="fas fa-cash-register"></i></div>
                        <h4 class="fw-bold h5 mb-2">Caja Diaria</h4>
                        <p class="text-muted small mb-0">Controlá los ingresos de dinero, egresos por gastos y cierres de caja.</p>
                    </a>
                </div>
                <div class="col-md-4">
                        <a href="categorias/listar.php" class="card-menu p-4">
                            <div class="icon-box"><i class="fas fa-tags text-success"></i></div>
                            <h4 class="fw-bold h5 mb-2">Categorías de Artículos</h4>
                            <p class="text-muted small mb-0">Gestioná las familias y grupos de productos para organizar tu stock y catálogo.</p>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="provincias/listar.php" class="card-menu p-4">
                            <div class="icon-box"><i class="fas fa-map-marker-alt text-success"></i></div>
                            <h4 class="fw-bold h5 mb-2">Gestión de Provincias</h4>
                            <p class="text-muted small mb-0">Administrá los estados y provincias para la procedencia y logística de tus clientes.</p>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="metodos_pago/listar.php" class="card-menu p-4">
                            <div class="icon-box"><i class="fas fa-credit-card text-success"></i></div>
                            <h4 class="fw-bold h5 mb-2">Métodos de Pago</h4>
                            <p class="text-muted small mb-0">Configurá las opciones de cobro disponibles como efectivo, transferencia o tarjetas.</p>
                        </a>
                    </div>
                
            </div>