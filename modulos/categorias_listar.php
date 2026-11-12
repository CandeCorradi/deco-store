<?php
// modulos/categorias_listar.php

// 1. Consulta de estadísticas rápidas para las tarjetas
$q_totales = "SELECT COUNT(*) AS total_cat FROM categorias WHERE esta_eliminado = 0";
$r_totales = mysqli_query($conexion, $q_totales);
$f_totales = mysqli_fetch_assoc($r_totales);
$total_categorias = $f_totales['total_cat'] ?? 0;

// 2. Consulta principal de categorías
$query = "SELECT * FROM categorias WHERE esta_eliminado = 0 ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $query);
?>

<!-- Tarjeta de Resumen Superior -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Total Categorías</div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_categorias; ?> Registradas</div>
        </div>
    </div>
</div>

<!-- Encabezado de la Sección -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-tags me-2 text-success"></i>Categorías de Artículos</h3>
        <p class="text-muted small mb-0">Gestión y clasificación de productos del catálogo.</p>
    </div>
    <a href="index.php?page=categorias_crear" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nueva Categoría
    </a>
</div>

<!-- Tabla Principal de Datos -->
<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-categorias" class="table table-hover align-middle mb-0" style="width:100%" data-tabla="categorias">
            <thead>
                <tr>
                    <th style="width: 15%;">Código</th>
                    <th>Nombre de la Categoría</th>
                    <th class="text-center" style="width: 20%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr data-id="<?php echo $fila['id_categorias']; ?>">
                            <td class="text-muted fw-bold">#<?php echo $fila['id_categorias']; ?></td>
                            
                            <!-- Celda editable con doble clic -->
                            <td class="editable fw-semibold" data-columna="nombre">
                                <?php echo htmlspecialchars($fila['nombre']); ?>
                            </td>

                            <!-- Botones de Acción -->
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=categorias_editar&id=<?php echo $fila['id_categorias']; ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-borrar-estetico" 
                                       data-url="index.php?page=categorias_borrar&id=<?php echo $fila['id_categorias']; ?>" 
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No hay categorías cargadas aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Inicialización de DataTables idéntica a Caja -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-categorias').DataTable({
            "pageLength": 10,
            "ordering": true,
            "destroy": true,
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "search": "Buscar:",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });
    }
});
</script>