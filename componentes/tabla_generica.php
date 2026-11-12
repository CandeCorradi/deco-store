<?php
// componentes/tabla_generica.php

// Este archivo NO se abre directo. Es un molde que va a ser incluido (include)
// desde el listar.php de cada carpeta simple, pasándole las variables configuradas.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - <?php echo $titulo_modulo; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #FDFCF6; color: #3E2723; font-family: 'Segoe UI', sans-serif; }
        .main-card { background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px; box-shadow: 0 4px 12px rgba(62, 39, 35, 0.02); }
        .table th { background-color: #F4F1EA !important; color: #3E2723; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; }
        .btn-deco { background-color: #2E7D32; color: white; }
        .btn-deco:hover { background-color: #1B5E20; color: white; }
        .editable { cursor: pointer; border-bottom: 1px dashed #CCC; }
        .editable:hover { background-color: #FFFDEB; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0px !important; }
    </style>
</head>
<body>

<?php 
// Como este molde se incluye desde una subcarpeta, el menú está un nivel arriba
include '../componentes/menu.php'; 
?>

<div class="container py-4" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="../index.php?page=articulos" class="btn btn-light rounded-circle border d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Volver al Catálogo">
                <i class="fas fa-arrow-left text-muted small"></i>
            </a>
            <div>
                <h2 class="fw-bold mb-1"><i class="<?php echo $icono_modulo; ?> me-2 text-success"></i><?php echo $titulo_modulo; ?></h2>
                <p class="text-muted small mb-0">Hacé <b>doble clic</b> sobre el nombre para editar rápido.</p>
            </div>
        </div>
        <a href="crear.php" class="btn btn-deco px-4 fw-semibold"><i class="fas fa-plus me-2"></i> Nuevo Registro</a>
    </div>

    <div class="main-card p-4 shadow-sm">
        <div class="table-responsive">
            <table id="tabla-maestra" class="table table-hover align-middle mb-0" style="width:100%" data-tabla="<?php echo $tabla; ?>">
                <thead>
                    <tr>
                        <th style="width: 20%;">ID</th>
                        <th>Nombre / Descripción</th>
                        <th class="text-center" style="width: 25%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                            <tr data-id="<?php echo $row[$campo_id]; ?>">
                                <td class="text-muted fw-bold">#<?php echo $row[$campo_id]; ?></td>
                                <td class="editable fw-semibold" data-columna="<?php echo $campo_nombre; ?>">
                                    <?php echo htmlspecialchars($row[$campo_nombre]); ?>
                                </td>
                                <td class="text-center">
                                    <a href="editar.php?id=<?php echo $row[$campo_id]; ?>" class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" data-url="borrar.php?id=<?php echo $row[$campo_id]; ?>" class="btn btn-sm btn-outline-danger btn-borrar-estetico" title="Borrar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabla-maestra').DataTable({
        "pageLength": 10,
        "ordering": true,
        "language": {
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron registros",
            "search": "Buscar:",
            "paginate": { "next": "Siguiente", "previous": "Anterior" }
        }
    });
});

// Lógica de borrado con SweetAlert2
$(document).on('click', '.btn-borrar-estetico', function(e) {
    e.preventDefault();
    const urlDestino = $(this).attr('data-url');

    Swal.fire({
        title: '¿Estás segura?',
        text: "Se eliminará este registro permanentemente.",
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
</script>

</body>
</html>