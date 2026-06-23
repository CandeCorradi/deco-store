<?php
// articulos/listar.php
session_start();
include '../php/conexion.php';

// Consulta con INNER JOIN para traer el nombre real de la categoría
$query = "SELECT a.*, c.nombre AS nombre_categoria 
          FROM articulos a 
          LEFT JOIN categorias c ON a.id_categorias = c.id_categorias 
          WHERE a.esta_eliminado = 0 
          ORDER BY a.id_articulos DESC"; 
$resultado = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Catálogo de Artículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-warm-beige: #FDFCF6;
            --text-dark-brown: #3E2723;
            --accent-green: #2E7D32;
            --border-color: #EAE1D0;
        }
        body { background-color: var(--bg-warm-beige); color: var(--text-dark-brown); font-family: 'Segoe UI', sans-serif; }
        .main-card { background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 4px 12px rgba(62, 39, 35, 0.02); }
        .table th { background-color: #F4F1EA !important; color: var(--text-dark-brown); font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
        .btn-deco { background-color: var(--accent-green); color: white; }
        .btn-deco:hover { background-color: #1B5E20; color: white; }
        .editable { cursor: pointer; border-bottom: 1px dashed #CCC; }
        .editable:hover { background-color: #FFFDEB; }
        
        /* Ajustes estéticos para que el buscador y paginador de la librería queden lindos */
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0px !important; margin-left: 0px !important; border: none !important; }
        .dataTables_filter input { border: 1px solid var(--border-color) !important; border-radius: 8px !important; padding: 5px 10px !important; }
        .dataTables_length select { border: 1px solid var(--border-color) !important; border-radius: 8px !important; }
    </style>
</head>
<body>

<?php include '../componentes/menu.php'; ?>

<div class="container py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-couch me-2 text-success"></i>Catálogo de Artículos</h2>
            <p class="text-muted small mb-0">Hacé <b>doble clic</b> sobre el nombre o los precios para editarlos rápido.</p>
        </div>
        <a href="crear.php" class="btn btn-deco px-4 fw-semibold"><i class="fas fa-plus me-2"></i> Nuevo Artículo</a>
    </div>

    <div class="main-card p-4">
        <div class="table-responsive">
            <table id="tabla-articulos" class="table table-hover align-middle mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción / Nombre</th>
                        <th>Categoría</th>
                        <th class="text-end">P. Costo</th>
                        <th class="text-end">P. Venta</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                            <tr data-id="<?php echo $row['id_articulos']; ?>">
                                <td class="fw-bold text-muted">#<?php echo $row['id_articulos']; ?></td>
                                <td class="editable fw-semibold" data-columna="nombre"><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo isset($row['nombre_categoria']) ? htmlspecialchars($row['nombre_categoria']) : 'Sin Categoría'; ?></span></td>
                                <td class="editable text-end" data-columna="precio_costo" data-puro="<?php echo $row['precio_costo']; ?>">$<?php echo number_format($row['precio_costo'], 2, ',', '.'); ?></td>
                                <td class="editable text-end fw-bold text-success" data-columna="precio_venta" data-puro="<?php echo $row['precio_venta']; ?>">$<?php echo number_format($row['precio_venta'], 2, ',', '.'); ?></td>
                                <td class="editable text-center" data-columna="stock">
                                    <span class="badge <?php echo $row['stock'] > 3 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-3">
                                        <?php echo $row['stock']; ?> un.
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="editar.php?id=<?php echo $row['id_articulos']; ?>" class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-edit"></i></a>
                                    <a href="borrar.php?id=<?php echo $row['id_articulos']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro querés eliminar este producto?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // 🚀 ACTIVAMOS EL PAGINADOR EN TU TABLA
    $('#tabla-articulos').DataTable({
        "pageLength": 10, // Cuántos datos muestra por página
        "ordering": true, // Permite ordenar haciendo clic en los títulos
        "language": {     // Traducimos la librería al español
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
});

// Tu lógica de Doble Clic intacta (sigue funcionando igual sobre las celdas)
document.querySelectorAll('.editable').forEach(celda => {
    celda.addEventListener('dblclick', function() {
        if (this.querySelector('input')) return;
        const columna = this.getAttribute('data-columna');
        const idArticulo = this.closest('tr').getAttribute('data-id');
        const valorActual = this.hasAttribute('data-puro') ? this.getAttribute('data-puro') : this.innerText.replace(' un.', '').trim();
        
        const input = document.createElement('input');
        input.type = (columna === 'nombre') ? 'text' : 'number';
        if(input.type === 'number') input.step = '0.01';
        input.className = 'form-control form-control-sm';
        input.value = valorActual;

        this.innerHTML = '';
        this.appendChild(input);
        input.focus();

        const guardarCambio = () => {
            const nuevoValor = input.value.trim();
            if (nuevoValor === '' || nuevoValor === valorActual) { location.reload(); return; }

            fetch('actualizar_inline.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: idArticulo, columna: columna, valor: nuevoValor })
            })
            .then(res => res.json())
            .then(data => { location.reload(); });
        };

        input.addEventListener('blur', guardarCambio);
        input.addEventListener('keydown', function(e) { if (e.key === 'Enter') guardarCambio(); if (e.key === 'Escape') location.reload(); });
    });
});
</script>
</body>
</html>