<?php
// modulos/usuarios_listar.php

// 1. Consulta de totales
$q_totales = "SELECT COUNT(*) AS total FROM usuarios WHERE esta_eliminado = 0";
$r_totales = mysqli_query($conexion, $q_totales);
$f_totales = mysqli_fetch_assoc($r_totales);
$total_usuarios = $f_totales['total'] ?? 0;

// 2. Consulta principal de usuarios
$query = "SELECT * FROM usuarios WHERE esta_eliminado = 0 ORDER BY nombre_usuario ASC";
$resultado = mysqli_query($conexion, $query);
?>

<!-- Tarjeta de Resumen Superior -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 border-0" style="background-color: #FFFFFF; border-left: 4px solid #2E7D32 !important; border-radius: 10px;">
            <div class="text-muted small text-uppercase fw-bold">Usuarios del Sistema</div>
            <div class="h3 fw-bold text-success mt-1"><?php echo $total_usuarios; ?> Registrados</div>
        </div>
    </div>
</div>

<!-- Encabezado de la Sección -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="fas fa-users-cog me-2 text-success"></i>Gestión de Usuarios</h3>
        <p class="text-muted small mb-0">Administrá las cuentas de acceso, roles y permisos de la plataforma.</p>
    </div>
    <a href="index.php?page=usuarios_agregar" class="btn btn-success fw-semibold">
        <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
    </a>
</div>

<!-- Tabla Principal -->
<div class="main-card p-4 shadow-sm">
    <div class="table-responsive">
        <table id="tabla-usuarios" class="table table-hover align-middle mb-0" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th>Usuario</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center" style="width: 15%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($u = mysqli_fetch_assoc($resultado)): ?>
                        <?php 
                            $nombre_completo = trim(($u['nombre'] ?? '') . ' ' . ($u['apellido'] ?? ''));
                            if (empty($nombre_completo)) $nombre_completo = '-';
                        ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?php echo $u['id_usuarios']; ?></td>
                            <td class="fw-bold text-dark">
                                <i class="fas fa-user-circle me-1 text-muted"></i>
                                <?php echo htmlspecialchars($u['nombre_usuario']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($nombre_completo); ?></td>
                            <td class="small text-muted"><?php echo htmlspecialchars($u['email'] ?? '-'); ?></td>
                            <td>
                                <span class="badge <?php echo ($u['rol'] === 'admin') ? 'bg-dark' : 'bg-secondary'; ?>">
                                    <?php echo strtoupper(htmlspecialchars($u['rol'])); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($u['esta_activo'] == 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?page=usuarios_editar&id=<?php echo $u['id_usuarios']; ?>" class="btn btn-outline-secondary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="#" 
                                       class="btn btn-outline-danger btn-borrar-estetico" 
                                       data-url="index.php?page=usuarios_borrar&id=<?php echo $u['id_usuarios']; ?>" 
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay usuarios cargados aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tabla-usuarios').DataTable({
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