<?php
// clientes/listar.php
session_start();
include '../php/conexion.php';

$query = "SELECT * FROM clientes ORDER BY id_clientes DESC";
$resultado = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-warm-beige: #FDFCF6;
            --text-dark-brown: #3E2723;
            --accent-green: #2E7D32;
            --border-color: #EAE1D0;
        }
        body { background-color: var(--bg-warm-beige); color: var(--text-dark-brown); font-family: 'Segoe UI', sans-serif; }
        .main-card { background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px; }
        .table th { background-color: #F4F1EA !important; color: var(--text-dark-brown); font-size: 0.8rem; text-transform: uppercase; }
        .btn-deco { background-color: var(--accent-green); color: white; }
        .btn-deco:hover { background-color: #1B5E20; color: white; }
    </style>
</head>
<body>

<?php include '../componentes/menu.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-users me-2 text-success"></i>Clientes del Estudio</h2>
            <p class="text-muted small mb-0">Base de datos de contactos, showrooms y profesionales asociados.</p>
        </div>
        <a href="crear.php" class="btn btn-deco px-4 fw-semibold"><i class="fas fa-plus me-2"></i>Nuevo Cliente</a>
    </div>

    <div class="main-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                        <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                            <tr>
                                <td class="text-muted">#<?php echo $row['id_clientes']; ?></td>
                                <td class="fw-semibold"><?php echo $row['nombre'] . " " . $row['apellido']; ?></td>
                                <td><?php echo $row['telefono']; ?></td>
                                <td class="text-muted"><?php echo $row['email']; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo $row['id_provincias']; ?></span></td>
                                <td class="text-center">
                                    <a href="editar.php?id=<?php echo $row['id_clientes']; ?>" class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-edit"></i></a>
                                    <a href="borrar.php?id=<?php echo $row['id_clientes']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar cliente?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay clientes registrados en la base de datos.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>