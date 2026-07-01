<?php
// componentes/menu.php

$script_name = $_SERVER['SCRIPT_NAME'];

// Detectamos si estamos adentro de CUALQUIER subcarpeta del sistema
$en_subcarpeta = (
    strpos($script_name, '/articulos/') !== false || 
    strpos($script_name, '/clientes/') !== false || 
    strpos($script_name, '/caja/') !== false ||
    strpos($script_name, '/categorias/') !== false ||
    strpos($script_name, '/provincias/') !== false ||
    strpos($script_name, '/metodos_pago/') !== false
);

// Definimos las rutas dinámicas según dónde estemos parados
$ruta_inicio = $en_subcarpeta ? '../index.php' : 'index.php';
$ruta_catalogo = $en_subcarpeta ? '../articulos/listar.php' : 'articulos/listar.php';
$ruta_clientes = $en_subcarpeta ? '../clientes/listar.php' : 'clientes/listar.php';
$ruta_caja = $en_subcarpeta ? '../caja/listar.php' : 'caja/listar.php';
$ruta_categorias = $en_subcarpeta ? '../categorias/listar.php' : 'categorias/listar.php'; 
$ruta_provincias = $en_subcarpeta ? '../provincias/listar.php' : 'provincias/listar.php'; 
$ruta_metodos_pago = $en_subcarpeta ? '../metodos_pago/listar.php' : 'metodos_pago/listar.php'; 
$ruta_salir = $en_subcarpeta ? '../login/salir.php' : 'login/salir.php'; 
?>

<nav class="navbar navbar-expand-lg py-3 mb-2" style="background-color: #FDFCF6; border-bottom: 1px solid #EAE1D0;">
    <div class="container">
        
        <div class="d-flex align-items-center gap-3">
            <?php if ($en_subcarpeta): ?>
                <a href="<?php echo $ruta_inicio; ?>" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center" 
                   style="width: 35px; height: 35px; background-color: #F4F1EA; color: #2E7D32; border: 1px solid #EAE1D0; transition: all 0.2s;"
                   title="Volver al Inicio"
                   onmouseover="this.style.backgroundColor='#2E7D32'; this.style.color='#FFFFFF';"
                   onmouseout="this.style.backgroundColor='#F4F1EA'; this.style.color='#2E7D32';">
                    <i class="fas fa-arrow-left"></i>
                </a>
            <?php endif; ?>

            <a class="navbar-brand d-flex align-items-center fw-bold text-uppercase m-0" href="<?php echo $ruta_inicio; ?>" style="letter-spacing: 1px; color: #3E2723;">
                <span style="color: #2E7D32; margin-right: 6px;"><i class="fas fa-leaf"></i></span>
                DECO<span class="fw-light" style="color: #5D4037;">STORE</span>
            </a>
        </div>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-2 mt-3 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold small" style="color: #3E2723;" href="<?php echo $ruta_catalogo; ?>">Catálogo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold small" style="color: #3E2723;" href="<?php echo $ruta_clientes; ?>">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold small" style="color: #3E2723;" href="<?php echo $ruta_caja; ?>">Caja</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold small" style="color: #3E2723;" href="<?php echo $ruta_categorias; ?>">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold small" style="color: #3E2723;" href="<?php echo $ruta_provincias; ?>">Provincias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-3 fw-semibold small" style="color: #3E2723;" href="<?php echo $ruta_metodos_pago; ?>">Métodos de Pago</a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-sm btn-outline-danger px-3 fw-semibold" href="<?php echo $ruta_salir; ?>">Salir</a>
                </li>
            </ul>
        </div>

    </div>
</nav>