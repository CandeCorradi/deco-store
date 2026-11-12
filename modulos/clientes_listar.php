<?php
// modulos/clientes_listar.php

// 1. Consulta relacional para traer los clientes y el nombre real de su provincia
$query = "SELECT c.*, p.nombre AS nombre_provincia 
          FROM clientes c 
          INNER JOIN provincias p ON c.id_provincias = p.id_provincias 
          WHERE c.esta_eliminado = 0
          ORDER BY c.id_clientes DESC";

$resultado = mysqli_query($conexion, $query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-users me-2 text-success"></i>Control de Clientes</h3>
        <p class="text-muted small mb-0">Gestión de clientes, sincronización con provincias y cuentas corrientes.</p>
    </div>
    <a href="index.php?page=clientes_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-user-plus me-1"></i> Nuevo Cliente
    </a>
</div>

<div class="main-card p-4" style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle tabla-paginada" data-tabla="clientes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre y Apellido</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Provincia</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr data-id="<?php echo $fila['id_clientes']; ?>">
                            <td>#<?php echo $fila['id_clientes']; ?></td>
                            
                            <td class="editable" data-columna="nombre">
                                <?php echo htmlspecialchars($fila['nombre'] ?? ''); ?>
                            </td>
                            
                            <td class="editable" data-columna="telefono">
                                <?php echo htmlspecialchars($fila['telefono'] ?? ''); ?>
                            </td>
                            
                            <td class="editable" data-columna="email">
                                <?php echo htmlspecialchars($fila['email'] ?? ''); ?>
                            </td>
                            
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?php echo htmlspecialchars($fila['nombre_provincia'] ?? 'Sin asignar'); ?>
                                </span>
                            </td>
                            
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <!-- Botón Estado de Cuenta Corriente / Saldo -->
                                    <a href="index.php?page=clientes_cuenta_corriente&id=<?php echo $fila['id_clientes']; ?>" 
                                       class="btn btn-outline-warning" 
                                       title="Ver Cuenta Corriente / Saldo">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a>

                                    <!-- Botón Editar -->
                                    <a href="index.php?page=clientes_editar&id=<?php echo $fila['id_clientes']; ?>" 
                                       class="btn btn-outline-secondary" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Botón Eliminar -->
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-borrar-estetico" 
                                       data-url="index.php?page=clientes_borrar&id=<?php echo $fila['id_clientes']; ?>" 
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay clientes registrados aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>