<?php
// modulos/articulos_listar.php

// 1. Consulta compuesta con INNER JOIN para traer el nombre de la categoría
$query = "SELECT a.*, c.nombre AS nombre_categoria 
          FROM articulos a 
          INNER JOIN categorias c ON a.id_categorias = c.id_categorias 
          ORDER BY a.id_articulos DESC";

$resultado = mysqli_query($conexion, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-couch me-2 text-success"></i>Catálogo de Artículos</h3>
        <p class="text-muted small mb-0">Hacé doble clic sobre el nombre o los precios para editarlos rápido.</p>
    </div>
    <a href="index.php?page=articulos_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-plus me-1"></i> Nuevo Artículo
    </a>
</div>

<div class="main-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle tabla-paginada" data-tabla="articulos">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción / Nombre</th>
                    <th>Categoría</th>
                    <th>P. Costo</th>
                    <th>P. Venta</th>
                    <th>Stock</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr data-id="<?php echo $fila['id_articulos']; ?>">
                            <td>#<?php echo $fila['id_articulos']; ?></td>
                            
                            <td class="editable" data-columna="nombre">
                                <?php echo htmlspecialchars($fila['nombre']); ?>
                            </td>
                            
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?php echo htmlspecialchars($fila['nombre_categoria']); ?>
                                </span>
                            </td>
                            
                            <td class="editable" data-columna="precio_costo">
                                $<?php echo number_format($fila['precio_costo'], 2, ',', '.'); ?>
                            </td>
                            
                            <td class="editable" data-columna="precio_venta">
                                <span class="text-success fw-semibold">
                                    $<?php echo number_format($fila['precio_venta'], 2, ',', '.'); ?>
                                </span>
                            </td>
                            
                            <td class="editable" data-columna="stock">
                                <span class="badge bg-opacity-10 text-dark border">
                                    <?php echo $fila['stock']; ?> un.
                                </span>
                            </td>
                            
                            <td class="text-center">
                                <a href="index.php?page=articulos_editar&id=<?php echo $fila['id_articulos']; ?>" class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <a href="#" 
                                   class="btn btn-sm btn-outline-danger btn-borrar-estetico" 
                                   data-url="index.php?page=articulos_borrar&id=<?php echo $fila['id_articulos']; ?>" 
                                   title="Borrar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay artículos registrados aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>