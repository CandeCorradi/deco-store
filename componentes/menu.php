<?php
// componentes/menu.php

// Ruta de salida centralizada
$ruta_salir = 'login/salir.php'; 
?>

<nav class="navbar navbar-expand-lg py-2 mb-3 shadow-sm" style="background-color: #FDFCF6; border-bottom: 1px solid #EAE1D0;">
    <div class="container d-flex align-items-center justify-content-between">
        
        <!-- Marca / Logo -->
        <a class="navbar-brand d-flex align-items-center fw-bold text-uppercase m-0" href="index.php?page=panel_inicio" style="letter-spacing: 1px; color: #3E2723;">
            <span style="color: #2E7D32; margin-right: 8px; font-size: 1.2rem;"><i class="fas fa-leaf"></i></span>
            DECO<span class="fw-light" style="color: #5D4037;">STORE</span>
        </a>

        <!-- Zona Derecha: Botón Hamburguesa + Botón Salir Fijo -->
        <div class="d-flex align-items-center gap-3">
            
            <!-- Botón Salir (Siempre Visible) -->
            <a class="btn btn-sm btn-outline-danger px-3 fw-semibold rounded-pill" href="<?php echo $ruta_salir; ?>">
                <i class="fas fa-sign-out-alt me-1"></i> Salir
            </a>

            <!-- Botón Hamburguesa -->
            <button class="btn border-0 p-2 text-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuOffcanvas" aria-controls="menuOffcanvas">
                <i class="fas fa-bars fs-4" style="color: #3E2723;"></i>
            </button>

        </div>

    </div>
</nav>

<!-- 🎨 PANEL DESPLEGABLE LATERAL (OFFCANVAS) -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="menuOffcanvas" aria-labelledby="menuOffcanvasLabel" style="background-color: #FDFCF6; width: 320px;">
    
    <div class="offcanvas-header border-bottom" style="border-color: #EAE1D0 !important;">
        <h5 class="offcanvas-title fw-bold text-uppercase" id="menuOffcanvasLabel" style="color: #3E2723; font-size: 1.1rem; letter-spacing: 1px;">
            <span style="color: #2E7D32;"><i class="fas fa-leaf me-2"></i></span>Navegación
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div class="list-group list-group-flush">
            
            <!-- INICIO -->
            <a href="index.php?page=panel_inicio" class="list-group-item list-group-item-action py-3 px-4 border-0 text-dark fw-semibold" style="background: transparent;">
                <i class="fas fa-home me-3 text-success"></i> Inicio / Panel
            </a>

            <div class="px-4 py-2 text-uppercase fw-bold text-muted small mt-2" style="font-size: 0.75rem; letter-spacing: 1px; background-color: #F4F1EA;">
                Gestión Principal
            </div>

            <a href="index.php?page=ventas" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-cash-register me-3 text-success" style="width: 20px;"></i> Ventas
            </a>
            <a href="index.php?page=articulos" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-couch me-3 text-success" style="width: 20px;"></i> Catálogo de Artículos
            </a>
            <a href="index.php?page=clientes" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-users me-3 text-success" style="width: 20px;"></i> Clientes
            </a>
            <a href="index.php?page=caja" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-wallet me-3 text-success" style="width: 20px;"></i> Caja Diaria
            </a>
            
            <a href="index.php?page=movimientos_stock" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-boxes me-3 text-success" style="width: 20px;"></i> Movimientos de Stock
            </a>

            <div class="px-4 py-2 text-uppercase fw-bold text-muted small mt-2" style="font-size: 0.75rem; letter-spacing: 1px; background-color: #F4F1EA;">
                Tablas Auxiliares
            </div>

            <a href="index.php?page=categorias" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-tags me-3 text-success" style="width: 20px;"></i> Categorías
            </a>
            <a href="index.php?page=provincias" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-map-marker-alt me-3 text-success" style="width: 20px;"></i> Provincias
            </a>
            <a href="index.php?page=metodos_pago" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-credit-card me-3 text-success" style="width: 20px;"></i> Métodos de Pago
            </a>
            <a href="index.php?page=documentos_tipo" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-id-card me-3 text-success" style="width: 20px;"></i> Tipos de Documento
            </a>
            <a href="index.php?page=tipos_caja" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-exchange-alt me-3 text-success" style="width: 20px;"></i> Tipos de Caja
            </a>
            <a href="index.php?page=cuentas" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-wallet me-3 text-success" style="width: 20px;"></i> Cuentas / Cajas
            </a>
            <a href="index.php?page=presupuestos" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-file-invoice-dollar me-3 text-success" style="width: 20px;"></i> Presupuestos
            </a>
            <a href="index.php?page=proveedores" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-truck me-3 text-success" style="width: 20px;"></i> Proveedores
            </a>

            <div class="px-4 py-2 text-uppercase fw-bold text-muted small mt-2" style="font-size: 0.75rem; letter-spacing: 1px; background-color: #F4F1EA;">
                Sistema
            </div>

            <a href="index.php?page=usuarios" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-users-cog me-3 text-success" style="width: 20px;"></i> Usuarios
            </a>
            
            <a href="modulos/manual_uso.php" class="list-group-item list-group-item-action py-2 px-4 border-0 text-dark" style="background: transparent;">
                <i class="fas fa-book-open me-3 text-success" style="width: 20px;"></i> Manual de Uso
            </a>
            
            
            
        </div>
    </div>

    <!-- Pie del Offcanvas -->
    <div class="offcanvas-footer p-3 border-top text-center" style="border-color: #EAE1D0 !important; background-color: #F4F1EA;">
        <span class="small text-muted">DecoStore</span>
    </div>

</div>