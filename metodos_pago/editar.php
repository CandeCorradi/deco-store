<?php
// metodos_pago/editar.php
session_start();
include '../php/conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM metodos_pago WHERE id_metodos_pago = $id LIMIT 1";
    $resultado = mysqli_query($conexion, $query);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $metodo = mysqli_fetch_assoc($resultado);
    } else {
        header("Location: listar.php");
        exit();
    }
} else {
    header("Location: listar.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));

    if (!empty($nombre)) {
        $query_update = "UPDATE metodos_pago SET nombre = '$nombre' WHERE id_metodos_pago = $id";
        if (mysqli_query($conexion, $query_update)) {
            header("Location: listar.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DecoStore - Editar Método de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #FDFCF6; color: #3E2723; font-family: 'Segoe UI', sans-serif; }
        .form-card { background: #FFFFFF; border: 1px solid #EAE1D0; border-radius: 12px; }
        .btn-save { background-color: #2E7D32; color: white; }
    </style>
</head>
<body>

<?php include '../componentes/menu.php'; ?>

<div class="container py-4" style="max-width: 500px;">
    <div class="mb-4">
        <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2 text-success"></i>Editar Método de Pago</h3>
        <p class="text-muted small">Modificá el nombre de la forma de cobro seleccionada.</p>
    </div>

    <div class="form-card p-4 shadow-sm">
        <form action="" method="POST">
            <div class="mb-4">
                <label class="form-label fw-semibold small">Nombre del Método de Pago</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($metodo['nombre']); ?>" required autofocus>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="listar.php" class="btn btn-light border px-4">Cancelar</a>
                <button type="submit" class="btn btn-save px-4 fw-semibold">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>