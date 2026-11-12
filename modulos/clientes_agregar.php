<?php
// modulos/clientes_agregar.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Capturamos los datos del formulario (dejamos de usar el DNI en la consulta)
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $telefono = mysqli_real_escape_string($conexion, trim($_POST['telefono']));
    $email = mysqli_real_escape_string($conexion, trim($_POST['email']));
    $id_provincia = intval($_POST['id_provincias']);

    // 2. Quitamos 'dni' de las columnas y de los VALUES para que MySQL no proteste
    $query_insert = "INSERT INTO clientes (nombre, telefono, email, id_provincias) 
                    VALUES ('$nombre', '$telefono', '$email', $id_provincia)";

    if (mysqli_query($conexion, $query_insert)) {
        echo "<script>window.location.href='index.php?page=clientes';</script>";
        exit();
    } else {
        die("Error de MySQL al guardar cliente: " . mysqli_error($conexion));
    }
}

// Abajo sigue tu consulta normal de provincias...
$query_provincias = "SELECT * FROM provincias ORDER BY nombre ASC";
$res_provincias = mysqli_query($conexion, $query_provincias);
?>

<div class="container py-4" style="max-width: 600px;">
    <div class="mb-4">
        <h3 class="fw-bold mb-1"><i class="fas fa-user-plus me-2 text-success"></i>Nuevo Cliente</h3>
        <p class="text-muted small">Cargá los datos de contacto y procedencia del nuevo cliente.</p>
    </div>

    <div class="main-card p-4" style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 12px;">
        <form action="index.php?page=clientes_agregar" method="POST">
            
            <div class="mb-3">
                <label class="form-label fw-semibold small">Nombre Completo</label>
                <input type="text" name="nombre" class="form-control" placeholder="Ej: Juan Pérez" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">DNI / Documento</label>
                <input type="text" name="dni" class="form-control" placeholder="Sin puntos ni espacios" required>
            </div>
            <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label fw-bold">Condición de IVA / Responsable</label>
            <select name="id_tiporesp" id="id_tiporesp" class="form-select" onchange="actualizarLetraSugerida()">
                <?php
                $resResp = mysqli_query($conexion, "SELECT id_tiporesp, tiporesp FROM tipos_responsable WHERE esta_eliminado = 0");
                while ($tr = mysqli_fetch_assoc($resResp)) {
                    $selected = ($tr['id_tiporesp'] == 3) ? 'selected' : ''; // 3 = Consumidor Final por defecto
                    echo "<option value='{$tr['id_tiporesp']}' $selected>{$tr['tiporesp']}</option>";
                }
                ?>
            </select>
        </div>
        
    </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Ej: 3498657896">
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small">Correo Electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="juan@gmail.com">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Provincia</label>
                <select name="id_provincias" class="form-select" required>
                    <option value="">Seleccione una provincia</option>
                    <?php if ($res_provincias && mysqli_num_rows($res_provincias) > 0): ?>
                        <?php while ($prov = mysqli_fetch_assoc($res_provincias)): ?>
                            <option value="<?php echo $prov['id_provincias']; ?>">
                                <?php echo htmlspecialchars($prov['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="index.php?page=clientes" class="btn btn-light border px-4">Cancelar</a>
                <button type="submit" class="btn px-4 fw-semibold text-white" style="background-color: var(--accent-green);">Guardar Cliente</button>
            </div>
        </form>
    </div>
</div>