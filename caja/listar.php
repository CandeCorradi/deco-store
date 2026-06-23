<?php
// caja/listar.php
session_start();
include '../php/conexion.php';

// Traemos los movimientos activos ordenados por el ID exacto de tu captura
$query = "SELECT * FROM registro_caja WHERE esta_eliminado = 0 ORDER BY id_registro_caja DESC";
$resultado = mysqli_query($conexion, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Control de Caja</title>
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
        .main-card { background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 4px 12px rgba(62, 39, 35, 0.02); }
        .table th { background-color: #F4F1EA !important; color: var(--text-dark-brown); font-size: 0.8rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
        .btn-deco { background-color: var(--accent-green); color: white; }
        .btn-deco:hover { background-color: #1B5E20; color: white; }
    </style>
</head>
<body>

<?php include '../componentes/menu.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-cash-register me-2 text-success"></i>Caja Diaria</h2>
            <p class="text-muted small mb-0">Registro de ingresos y egresos de dinero del showroom.</p>
        </div>
        <a href="crear.php" class="btn btn-deco px-4 fw-semibold"><i class="fas fa-plus me-2"></i>Nuevo Movimiento</a>
    </div>

    <div class="main-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Detalle / Concepto</th>
                        <th>Tipo</th>
                        <th class="text-end">Monto</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                            <?php 
                                // Interpretamos si es Ingreso (1) o Egreso (2) segun tu id_tipos_caja
                                $es_ingreso = ($row['id_tipos_caja'] == 1);
                                $tipo_texto = $es_ingreso ? 'Ingreso' : 'Egreso';
                            ?>
                            <tr>
                                <td class="text-muted"><?php echo date('d/m/Y H:i', strtotime($row['fecha_hora'])); ?></td>
                                <td class="fw-semibold"><?php echo $row['detalle']; ?></td>
                                <td>
                                    <span class="badge <?php echo $es_ingreso ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-3 text-uppercase">
                                        <?php echo $tipo_texto; ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold <?php echo $es_ingreso ? 'text-success' : 'text-danger'; ?>">
                                    $<?php echo number_format($row['importe'], 2, ',', '.'); ?>
                                </td>
                                <td class="text-center">
                                    <a href="borrar.php?id=<?php echo $row['id_registro_caja']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este registro de caja?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay movimientos de caja registrados hoy.</td>
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