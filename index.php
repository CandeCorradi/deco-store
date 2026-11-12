<?php
// Configuración y depuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Control de Acceso: Si NO hay usuario autenticado, redirige al login
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: login/index.php");
    exit();
}

// Incluir conexión a la base de datos
include 'php/conexion.php';

// 1. Detectamos qué sección quiere ver la usuaria
$pagina = isset($_GET['page']) ? $_GET['page'] : 'panel_inicio';

// 2. Definimos páginas donde NO debe aparecer el botón de volver
$paginas_sin_volver = ['panel_inicio', 'login', 'inicio'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Panel de Control</title>
    <!-- CSS de Bootstrap, FontAwesome, DataTables y SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { 
            --bg-warm-beige: #FDFCF6; 
            --text-dark-brown: #3E2723; 
            --accent-green: #2E7D32; 
            --border-color: #EAE1D0; 
            --soft-brown: #8D7B68;
        }
        body { 
            background-color: var(--bg-warm-beige); 
            color: var(--text-dark-brown); 
            font-family: 'Segoe UI', sans-serif; 
        }
        .main-card { 
            background: #FFFFFF; 
            border: 1px solid var(--border-color); 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(62, 39, 35, 0.02); 
        }
        .editable { cursor: pointer; border-bottom: 1px dashed #CCC; }
        .editable:hover { background-color: #FFFDEB; }

        /* Estilos del Banner e Inicio */
        .welcome-banner {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 12px rgba(62, 39, 35, 0.02);
        }
        .card-menu {
            display: block;
            text-decoration: none;
            color: var(--text-dark-brown);
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .card-menu:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(62, 39, 35, 0.06);
            border-color: #C0B299;
            color: var(--text-dark-brown);
        }
        .icon-box {
            font-size: 2rem;
            color: #2E7D32 !important;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<?php include 'componentes/menu.php'; ?>

<div class="container py-4">

    <?php if (!in_array($pagina, $paginas_sin_volver)): ?>
        <!-- 🔙 BOTÓN VOLVER UNIVERSAL CIRCULAR -->
        <div class="mb-3">
            <a href="javascript:history.back()" 
               class="btn btn-light rounded-circle border d-flex align-items-center justify-content-center shadow-sm" 
               style="width: 38px; height: 38px; background-color: #FFFFFF;" 
               title="Volver a la pantalla anterior">
                <i class="fas fa-arrow-left text-muted small"></i>
            </a>
        </div>
    <?php endif; ?>

    <?php
    // 🎛️ EL ENRUTADOR PRINCIPAL
    switch ($pagina) {
        // CLIENTES
        case 'clientes':
            include 'modulos/clientes_listar.php';
            break;
        case 'clientes_editar':
            include 'modulos/clientes_editar.php';
            break;
        case 'clientes_agregar':
            include 'modulos/clientes_agregar.php';
            break;
        case 'clientes_borrar':
            include 'modulos/clientes_borrar.php';
            break;
        // CATÁLOGO / ARTÍCULOS
        case 'articulos':
            include 'modulos/articulos_listar.php';
            break;
        case 'articulos_editar':
            include 'modulos/articulos_editar.php';
            break;
        case 'articulos_agregar':
            include 'modulos/articulos_agregar.php';
            break;
        case 'articulos_borrar':
            include 'modulos/articulos_borrar.php';
            break;
        // CATEGORÍAS
        case 'categorias':
            include 'modulos/categorias_listar.php';
            break;
        case 'categorias_crear':
            include 'modulos/categorias_crear.php';
            break;
        case 'categorias_editar':
            include 'modulos/categorias_editar.php';
            break;
        case 'categorias_borrar':
            include 'modulos/categorias_borrar.php';
            break;
        // PROVINCIAS
        case 'provincias':
            include 'modulos/provincias_listar.php';
            break;
        case 'provincias_crear':
            include 'modulos/provincias_crear.php';
            break;
        case 'provincias_editar':
            include 'modulos/provincias_editar.php';
            break;
        case 'provincias_borrar':
            include 'modulos/provincias_borrar.php';
            break;
        // MÉTODOS DE PAGO
        case 'metodos_pago':
            include 'modulos/metodos_pago_listar.php';
            break;
        case 'metodos_pago_crear':
            include 'modulos/metodos_pago_crear.php';
            break;
        case 'metodos_pago_editar':
            include 'modulos/metodos_pago_editar.php';
            break;
        case 'metodos_pago_borrar':
            include 'modulos/metodos_pago_borrar.php';
            break;
        // CAJA DIARIA
        case 'caja':
            include 'modulos/caja_listar.php';
            break;
        case 'caja_agregar':
            include 'modulos/caja_agregar.php';
            break;
        case 'caja_editar':
            include 'modulos/caja_editar.php';
            break;
        case 'caja_borrar':
            include 'modulos/caja_borrar.php';
            break;
        // INICIO / DEFAULT
        case 'panel_inicio':
        case 'inicio':
        default:
            include 'modulos/panel_inicio.php';
            break;
        // TIPOS DE DOCUMENTO
        case 'documentos_tipo':
            include 'modulos/documentos_tipo_listar.php';
            break;
        case 'documentos_tipo_agregar':
            include 'modulos/documentos_tipo_agregar.php';
            break;
        case 'documentos_tipo_editar':
            include 'modulos/documentos_tipo_editar.php';
            break;
        case 'documentos_tipo_borrar':
            include 'modulos/documentos_tipo_borrar.php';
            break;
        // TIPOS DE CAJA
        case 'tipos_caja':
            include 'modulos/tipos_caja_listar.php';
            break;
        case 'tipos_caja_agregar':
            include 'modulos/tipos_caja_agregar.php';
            break;
        case 'tipos_caja_editar':
            include 'modulos/tipos_caja_editar.php';
            break;
        case 'tipos_caja_borrar':
            include 'modulos/tipos_caja_borrar.php';
            break;
        // USUARIOS
        case 'usuarios':
            include 'modulos/usuarios_listar.php';
            break;
        case 'usuarios_agregar':
            include 'modulos/usuarios_agregar.php';
            break;
        case 'usuarios_editar':
            include 'modulos/usuarios_editar.php';
            break;
        case 'usuarios_borrar':
            include 'modulos/usuarios_borrar.php';
            break;
        // PEDIDOS
        case 'pedidos':
            include 'modulos/pedidos_listar.php';
            break;
        case 'pedidos_agregar':
            include 'modulos/pedidos_agregar.php';
            break;
        case 'pedidos_editar':
            include 'modulos/pedidos_editar.php';
            break;
        case 'pedidos_borrar':
            include 'modulos/pedidos_borrar.php';
            break;
        // VENTAS DETALLE
        case 'ventas_detalle':
            include 'modulos/ventas_detalle_listar.php';
            break;
        case 'ventas_detalle_agregar':
            include 'modulos/ventas_detalle_agregar.php';
            break;
        case 'ventas_detalle_editar':
            include 'modulos/ventas_detalle_editar.php';
            break;
        case 'ventas_detalle_borrar':
            include 'modulos/ventas_detalle_borrar.php';
            break;
        // VENTAS
        case 'ventas':
            include 'modulos/ventas_listar.php';
            break;
        case 'ventas_agregar':
            include 'modulos/ventas_agregar.php';
            break;
        case 'ventas_editar':
            include 'modulos/ventas_editar.php';
            break;
        case 'ventas_borrar':
            include 'modulos/ventas_borrar.php';
            break;
        // CUENTAS
        case 'cuentas': 
            include 'modulos/cuentas_listar.php'; 
            break;
        case 'cuentas_agregar': 
            include 'modulos/cuentas_agregar.php'; 
            break;
        case 'cuentas_editar': 
            include 'modulos/cuentas_editar.php'; 
            break;
        case 'cuentas_borrar': 
            include 'modulos/cuentas_borrar.php'; 
            break;
        // PRESUPUESTOS
        case 'presupuestos': 
            include 'modulos/presupuestos_listar.php'; 
            break;
        case 'presupuestos_agregar': 
            include 'modulos/presupuestos_agregar.php'; 
            break;
        case 'presupuestos_editar': 
            include 'modulos/presupuestos_editar.php'; 
            break;
        case 'presupuestos_borrar': 
            include 'modulos/presupuestos_borrar.php'; 
            break;
        // PROVEEDORES
        case 'proveedores': 
            include 'modulos/proveedores_listar.php'; 
            break;
        case 'proveedores_agregar': 
            include 'modulos/proveedores_agregar.php'; 
            break;
        case 'proveedores_editar': 
            include 'modulos/proveedores_editar.php'; 
            break;
        case 'proveedores_borrar': 
            include 'modulos/proveedores_borrar.php'; 
            break;
        // MOVIMIENTOS DE STOCK
        case 'movimientos_stock':
            include 'modulos/movimientos_stock_listar.php';
            break;
        case 'movimientos_stock_agregar':
            include 'modulos/movimientos_stock_agregar.php';
            break;
        case 'movimientos_stock_editar':
            include 'modulos/movimientos_stock_editar.php';
            break;
        case 'movimientos_stock_borrar':
            include 'modulos/movimientos_stock_borrar.php';
            break;
        case 'clientes_cuenta_corriente':
            include 'modulos/clientes_cuenta_corriente.php';
            break;
        case 'ventas_devolucion':
            include 'modulos/ventas_devolucion.php';
            break;
        case 'ventas_recibo':
            include 'modulos/ventas_recibo.php';
            break;
        }
    ?>
</div>

<!-- Scripts JS -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
// Paginador automático
$(document).ready(function() {
    $('.tabla-paginada').DataTable({
        "pageLength": 10,
        "ordering": true,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "search": "Buscar:",
            "paginate": { "next": "Siguiente", "previous": "Anterior" }
        }
    });
});

// Borrado estético con SweetAlert2
$(document).on('click', '.btn-borrar-estetico', function(e) {
    e.preventDefault();
    const urlDestino = $(this).attr('data-url');

    Swal.fire({
        title: '¿Estás segura?',
        text: "Esta acción eliminará el registro de la base de datos.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2E7D32',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar',
        background: '#FDFCF6',
        color: '#3E2723'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = urlDestino;
        }
    });
});

// Edición inline por doble clic
$(document).on('dblclick', '.editable', function() {
    if ($(this).find('input').length) return;
    
    const celda = $(this);
    const id = celda.closest('tr').data('id');
    const columna = celda.data('columna');
    const tablaDestino = celda.closest('table').data('tabla');
    const valorActual = celda.text().trim();
    
    const input = $('<input>', {
        type: 'text',
        class: 'form-control form-control-sm',
        value: valorActual
    });
    
    celda.html(input);
    input.focus();
    
    input.on('blur keydown', function(e) {
        if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== 'Escape') return;
        if (e.key === 'Escape') { celda.html(valorActual); return; }
        
        const nuevoValor = input.val().trim();
        if (nuevoValor === '' || nuevoValor === valorActual) { celda.html(valorActual); return; }
        
        $.ajax({
            url: 'php/actualizar_inline_universal.php',
            method: 'POST',
            data: { id: id, columna: columna, valor: nuevoValor, tabla: tablaDestino },
            success: function() { 
                celda.html(nuevoValor); 
            },
            error: function() { 
                alert('No se pudo guardar el cambio.');
                celda.html(valorActual);
            }
        });
    });
});
</script>
</body>
</html>