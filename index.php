<?php
// index.php - EL RECEPTOR UNIVERSAL
session_start();
include 'php/conexion.php';

// Control de sesión provisorio (descomentar cuando quieras asegurar todo)
/* if (!isset($_SESSION['id_usuario'])) { header("Location: login/index.php"); exit(); } */

// Detectamos qué sección quiere ver el usuario (si no pide ninguna, va al panel de inicio)
$pagina = isset($_GET['page']) ? $_GET['page'] : 'inicio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Panel de Control</title>
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
        body { background-color: var(--bg-warm-beige); color: var(--text-dark-brown); font-family: 'Segoe UI', sans-serif; }
        .main-card { background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 4px 12px rgba(62, 39, 35, 0.02); }
        .editable { cursor: pointer; border-bottom: 1px dashed #CCC; }
        .editable:hover { background-color: #FFFDEB; }

        /* 🎨 ESTILOS RECUPERADOS PARA EL BIENVENIDA Y TARJETAS DEL INICIO */
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
            color: var(--accent-green);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<?php include 'componentes/menu.php'; ?>

<div class="container py-4">
    <?php
    // 🎛️ EL ENRUTADOR: Trae el pedacito de código que corresponde
    switch ($pagina) {
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
        case 'inicio':
        default:
            include 'modulos/panel_inicio.php'; // Tus tarjetas de bienvenida originales
            break;   
        case 'categorias':
            include 'modulos/categorias_listar.php';
            break;
        case 'provincias':
            include 'modulos/provincias_listar.php';
            break;
        case 'metodos_pago':
            include 'modulos/metodos_pago_listar.php';
            break;
        case 'caja':
            include 'modulos/caja_listar.php';
            break;
        case 'caja_borrar':
            include 'modulos/caja_borrar.php';
            break;
        case 'caja_agregar':
            include 'modulos/caja_agregar.php';
            break;
        case 'caja_editar':
            include 'modulos/caja_editar.php';
            break;
    }
    ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
// 🚀 INYECTAMOS EL PAGINADOR AUTOMÁTICO A CUALQUIER TABLA QUE APAREZCA
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

// 🟢 LÓGICA GLOBAL DE BORRADO ESTÉTICO CON SWEETALERT2
$(document).on('click', '.btn-borrar-estetico', function(e) {
    e.preventDefault(); // Frenamos el viaje de la URL por el momento
    const urlDestino = $(this).attr('data-url');

    Swal.fire({
        title: '¿Estás segura, Cande?',
        text: "Esta acción eliminará el registro de la base de datos de manera definitiva.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2E7D32', // Verde DecoStore
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar',
        background: '#FDFCF6', // Tu fondo cálido beige
        color: '#3E2723' // Tu color de texto marrón
    }).then((result) => {
        if (result.isConfirmed) {
            // Si confirmás, viaja por el index a ejecutar el script de borrado
            window.location.href = urlDestino;
        }
    });
});

// ✏️ LÓGICA UNIVERSAL DE EDICIÓN INLINE (DOBLE CLIC)
$(document).on('dblclick', '.editable', function() {
    if ($(this).find('input').length) return;
    
    const celda = $(this);
    const id = celda.closest('tr').data('id');
    const columna = celda.data('columna');
    const tablaDestino = celda.closest('table').data('tabla'); // Nos dice a qué tabla pegarle
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
        
        const nuevoValor = input.val().trim(); // 🛠️ CORREGIDO: Era .val(), no .value()
        if (nuevoValor === '' || nuevoValor === valorActual) { celda.html(valorActual); return; }
        
        // 🛠️ CORREGIDO: Agregado el signo $ que faltaba en $.ajax
        $.ajax({
            url: 'php/actualizar_inline_universal.php',
            method: 'POST',
            data: { id: id, columna: columna, valor: nuevoValor, tabla: tablaDestino },
            success: function() { 
                celda.html(nuevoValor); 
            },
            error: function() { // 🛠️ CORREGIDO: Función de respaldo si la base de datos falla
                alert('No se pudo guardar el cambio.');
                celda.html(valorActual);
            }
        });
    });
});
</script>
</body>
</html>