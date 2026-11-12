<?php
// modulos/panel_inicio.php

// 1. Datos del usuario para el banner de bienvenida
$nombre_usuario = !empty($_SESSION['nombre_completo']) 
                    ? $_SESSION['nombre_completo'] 
                    : (!empty($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : 'Administrador');

$rol_usuario = !empty($_SESSION['rol']) ? $_SESSION['rol'] : 'ADMIN';

// 2. Consulta de productos activos en catálogo cruzados con categoría
$q_articulos = "SELECT a.id_articulos, a.nombre, a.precio_venta, a.stock, a.imagen, c.nombre AS nombre_categoria 
                FROM articulos a 
                LEFT JOIN categorias c ON a.id_categorias = c.id_categorias 
                WHERE a.esta_eliminado = 0 
                ORDER BY a.id_articulos ASC";

$r_articulos = mysqli_query($conexion, $q_articulos);
?>

<!-- Banner Superior de Bienvenida -->
<div class="welcome-banner d-flex align-items-center justify-content-between flex-wrap gap-3 p-4 mb-4" 
     style="background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px; box-shadow: 0 4px 12px rgba(62, 39, 35, 0.03);">
    <div>
        <span class="badge mb-2 px-3 py-2 text-uppercase" style="background-color: #F4F1EA; color: #5D4037; font-weight: 600; font-size: 0.75rem;">
            <i class="fas fa-user-shield me-1 text-success"></i> <?php echo htmlspecialchars($rol_usuario); ?>
        </span>
        <h2 class="fw-bold mb-1" style="color: #3E2723;">Bienvenido/a, <?php echo htmlspecialchars($nombre_usuario); ?></h2>
        <p class="text-muted mb-0 small">Panel general de gestión de estudio y showroom.</p>
    </div>
    <div class="text-end">
        <div class="small text-muted mb-1"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y'); ?></div>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">CONEXIÓN SEGURA</span>
    </div>
</div>

<!-- Buscador Global DecoStore -->
<div class="position-relative my-4" style="max-width: 650px; margin-left: auto; margin-right: auto;">
    <div class="input-group shadow-sm" style="border-radius: 30px; overflow: hidden; border: 1px solid #EAE1D0; background: #fff;">
        <span class="input-group-text bg-white border-0 ps-3">
            <i class="fas fa-search text-muted"></i>
        </span>
        <input type="text" 
               id="inputBuscadorGlobal" 
               class="form-control border-0 py-2 shadow-none" 
               placeholder="Buscar decoración (ej. jarra), cliente (ej. pepe) o venta..." 
               autocomplete="off">
        <button class="btn btn-white bg-white border-0 text-muted d-none" type="button" id="btnLimpiarBuscador">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Desplegable con resultados dinámicos -->
    <div id="resultadosBuscador" 
         class="position-absolute w-100 mt-2 shadow-lg bg-white rounded-3 p-2 d-none" 
         style="z-index: 1050; max-height: 420px; overflow-y: auto; border: 1px solid #EAE1D0;">
    </div>
</div>

<style>
.hover-bg {
    transition: background-color 0.15s ease;
}
.hover-bg:hover {
    background-color: #F8F5EE !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('inputBuscadorGlobal');
    const cajaResultados = document.getElementById('resultadosBuscador');
    const btnLimpiar = document.getElementById('btnLimpiarBuscador');
    let temporizador = null;

    if (!input || !cajaResultados) return;

    input.addEventListener('input', function() {
        clearTimeout(temporizador);
        const query = this.value.trim();

        if (query.length > 0) {
            btnLimpiar.classList.remove('d-none');
        } else {
            btnLimpiar.classList.add('d-none');
        }

        if (query.length < 2) {
            cajaResultados.classList.add('d-none');
            cajaResultados.innerHTML = '';
            return;
        }

        temporizador = setTimeout(() => {
            fetch('php/buscador_global.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    let html = '';
                    let total = (data.articulos?.length || 0) + (data.clientes?.length || 0) + (data.ventas?.length || 0);

                    if (total === 0) {
                        html = '<div class="p-3 text-center text-muted small">No se encontraron resultados para "<b>' + escapeHtml(query) + '</b>"</div>';
                    } else {
                        // Artículos / Decoración
                        if (data.articulos && data.articulos.length > 0) {
                            html += '<div class="text-uppercase small fw-bold text-muted px-2 pt-1 pb-1 border-bottom"><i class="fas fa-couch me-1 text-success"></i> Artículos / Catálogo</div>';
                            data.articulos.forEach(art => {
                                html += `<a href="${art.url}" class="d-flex justify-content-between align-items-center text-decoration-none text-dark p-2 rounded hover-bg">
                                            <div><strong>${art.titulo}</strong></div>
                                            <span class="small text-muted">${art.sub}</span>
                                         </a>`;
                            });
                        }

                        // Clientes
                        if (data.clientes && data.clientes.length > 0) {
                            html += '<div class="text-uppercase small fw-bold text-muted px-2 pt-2 pb-1 border-bottom mt-1"><i class="fas fa-users me-1 text-primary"></i> Clientes</div>';
                            data.clientes.forEach(cli => {
                                html += `<a href="${cli.url}" class="d-flex justify-content-between align-items-center text-decoration-none text-dark p-2 rounded hover-bg">
                                            <div><strong>${cli.titulo}</strong></div>
                                            <span class="small text-muted">${cli.sub}</span>
                                         </a>`;
                            });
                        }

                        // Ventas
                        if (data.ventas && data.ventas.length > 0) {
                            html += '<div class="text-uppercase small fw-bold text-muted px-2 pt-2 pb-1 border-bottom mt-1"><i class="fas fa-receipt me-1 text-warning"></i> Ventas</div>';
                            data.ventas.forEach(ven => {
                                html += `<a href="${ven.url}" class="d-flex justify-content-between align-items-center text-decoration-none text-dark p-2 rounded hover-bg">
                                            <div><strong>${ven.titulo}</strong></div>
                                            <span class="small text-muted">${ven.sub}</span>
                                         </a>`;
                            });
                        }
                    }

                    cajaResultados.innerHTML = html;
                    cajaResultados.classList.remove('d-none');
                })
                .catch(err => console.error('Error buscador:', err));
        }, 250);
    });

    btnLimpiar.addEventListener('click', function() {
        input.value = '';
        btnLimpiar.classList.add('d-none');
        cajaResultados.classList.add('d-none');
        input.focus();
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !cajaResultados.contains(e.target)) {
            cajaResultados.classList.add('d-none');
        }
    });

    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && cajaResultados.innerHTML.trim() !== '') {
            cajaResultados.classList.remove('d-none');
        }
    });

    function escapeHtml(text) {
        let div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>

<!-- Encabezado de la Colección -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0" style="color: #3E2723;">
            <i class="fas fa-couch me-2 text-success"></i>Showroom & Colección
        </h4>
        <p class="text-muted small mb-0">Catálogo de mobiliario y objetos de diseño disponibles.</p>
    </div>
    <a href="index.php?page=articulos" class="btn btn-outline-success btn-sm fw-semibold px-3" style="border-radius: 8px;">
        Ver tabla completa <i class="fas fa-arrow-right ms-1"></i>
    </a>
</div>

<!-- Grilla de Tarjetas con Imágenes (Modo Vidriera) -->
<div class="row g-4 mb-4">
    <?php if ($r_articulos && mysqli_num_rows($r_articulos) > 0): ?>
        <?php while ($art = mysqli_fetch_assoc($r_articulos)): ?>
            <?php 
                $foto_nombre = !empty($art['imagen']) ? $art['imagen'] : 'chair.jpg';
                $ruta_foto   = 'img/articulos/' . $foto_nombre;
                
                if (!file_exists($ruta_foto)) {
                    $ruta_foto = 'img/articulos/chair.jpg';
                }
                
                $precio = number_format($art['precio_venta'] ?? 0, 2, ',', '.');
            ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm overflow-hidden" 
                     style="border-radius: 14px; background: #FFFFFF; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    
                    <div style="height: 185px; overflow: hidden; position: relative; background-color: #F4F1EA; cursor: pointer;" 
                         onclick="Swal.fire({
                             title: '<?php echo htmlspecialchars($art['nombre']); ?>',
                             text: 'Categoría: <?php echo htmlspecialchars($art['nombre_categoria'] ?? 'Mobiliario'); ?> - Precio: $<?php echo $precio; ?>',
                             imageUrl: '<?php echo $ruta_foto; ?>',
                             imageWidth: '80%',
                             imageHeight: '350px',
                             imageAlt: '<?php echo htmlspecialchars($art['nombre']); ?>',
                             confirmButtonText: 'Cerrar',
                             confirmButtonColor: '#198754'
                         });">
                        
                        <img src="<?php echo $ruta_foto; ?>" 
                             alt="<?php echo htmlspecialchars($art['nombre']); ?>" 
                             class="w-100 h-100" 
                             style="object-fit: cover;" 
                             onerror="this.src='img/articulos/chair.jpg';">
                    </div>

                    <!-- Datos y Detalle -->
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                <?php echo htmlspecialchars($art['nombre_categoria'] ?? 'Mobiliario'); ?>
                            </span>
                            <h6 class="fw-bold mt-1 mb-2 text-dark text-truncate" title="<?php echo htmlspecialchars($art['nombre']); ?>">
                                <?php echo htmlspecialchars($art['nombre']); ?>
                            </h6>
                        </div>

                        <div class="pt-2 border-top mt-2">
                            <span class="text-muted d-block" style="font-size: 0.68rem;">Precio</span>
                            <span class="fw-bold fs-6 text-success">$<?php echo $precio; ?></span>
                        </div>
                        
                    </div>

                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <i class="fas fa-couch text-muted fa-3x mb-3"></i>
            <p class="text-muted">No se encontraron artículos registrados en el catálogo.</p>
        </div>
    <?php endif; ?>
</div>