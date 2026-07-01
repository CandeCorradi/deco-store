<?php
// modulos/clientes_editar.php

// 1. Validamos que llegue el ID por la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Traemos el cliente específico (revisá si tu clave primaria es id o id_clientes)
    $query = "SELECT * FROM clientes WHERE id_clientes = $id LIMIT 1";
    $resultado = mysqli_query($conexion, $query);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $cliente = mysqli_fetch_assoc($resultado);
    } else {
        echo "<script>window.location.href='index.php?page=clientes';</script>";
        exit();
    }
} else {
    echo "<script>window.location.href='index.php?page=clientes';</script>";
    exit();
}

// 2. Traemos las provincias para el desplegable relacional
$query_provincias = "SELECT * FROM provincias ORDER BY nombre ASC";
$res_provincias = mysqli_query($conexion, $query_provincias);
?>

<div class="container py-4" style="max-width: 600px;">
    <div class="mb-4">
        <h3 class="fw-bold mb-1"><i class="fas fa-user-edit me-2 text-success"></i>Editar Cliente</h3>
        <p class="text-muted small">Modificá los datos de contacto y ubicación del cliente.</p>
    </div>

    <div class="main-card p-4" style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px;">
        <form action="php/clientes_actualizar.php" method="POST">
            
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold small">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
            </div>

<!-- Teléfono y Email en una fila -->
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small">Teléfono</label>
                    <!-- Evitamos pasar un NULL asegurando un string vacío con ?? '' -->
                    <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>">
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small">Correo Electrónico</label>
                    <!-- Evitamos pasar un NULL asegurando un string vacío con ?? '' -->
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>">
                </div>
            </div>            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Provincia</label>
                <select name="id_provincias" class="form-select" required>
                    <option value="">Seleccione una provincia</option>
                    <?php if ($res_provincias && mysqli_num_rows($res_provincias) > 0): ?>
                        <?php while ($prov = mysqli_fetch_assoc($res_provincias)): ?>
                            <?php 
                            $selected = ($prov['id_provincias'] == $cliente['id_provincias']) ? 'selected' : ''; 
                            ?>
                            <option value="<?php echo $prov['id_provincias']; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($prov['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="index.php?page=clientes" class="btn btn-light border px-4">Cancelar</a>
                <button type="submit" class="btn px-4 fw-semibold" style="background-color: var(--accent-green); color: white;">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>