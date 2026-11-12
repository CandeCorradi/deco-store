<?php
// modulos/provincias_listar.php

// 1. Consulta de totales
$q_totales = "SELECT COUNT(*) AS total FROM provincias WHERE esta_eliminado = 0";
$r_totales = mysqli_query($conexion, $q_totales);
$f_totales = mysqli_fetch_assoc($r_totales);
$total_provincias = $f_totales['total'] ?? 0;

// 2. Consulta principal ordenando por 'nombre'
$query = "SELECT * FROM provincias WHERE esta_eliminado = 0 ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $query);
?>

<!-- Tarjeta de Resumen Superior -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Total Provincias</div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_provincias; ?> Cargadas</div>
        </div>
    </div>
</div>

<!-- Encabezado de la Sección -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-map-marker-alt me-2 text-success"></i>Gestión de Provincias</h3>
        <p class="text-muted small mb-0">Listado de provincias registradas para direcciones e envíos.</p>
    </div>
    <a href="index.php?page=provincias_crear" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nueva Provincia
    </a>
</div>

<!-- Tabla Principal -->
<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-provincias" class="table table-hover align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 15%;">Código</th>
                    <th>Nombre de la Provincia</th>
                    <th class="text-center" style="width: 20%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?php echo $fila['id_provincias']; ?></td>
                            <td class="fw-semibold">
                                <?php echo htmlspecialchars($fila['nombre']); ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=provincias_editar&id=<?php echo $fila['id_provincias']; ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-borrar-estetico" 
                                       data-url="index.php?page=provincias_borrar&id=<?php echo $fila['id_provincias']; ?>" 
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No hay provincias cargadas aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-provincias').DataTable({
            "pageLength": 10,
            "ordering": true,
            "destroy": true,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "search": "Buscar:",
                "paginate": { "next": "Siguiente", "previous": "Anterior" }
            }
        });
    }
});
</script>