<?php
// modulos/presupuestos_borrar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    mysqli_query($conexion, "UPDATE presupuestos SET esta_eliminado = 1 WHERE id_presupuestos = $id");
}
echo "<script>window.location.href = 'index.php?page=presupuestos';</script>";
exit;
?>