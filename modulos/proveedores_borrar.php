<?php
// modulos/proveedores_borrar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    mysqli_query($conexion, "UPDATE proveedores SET esta_eliminado = 1 WHERE id_proveedores = $id");
}
echo "<script>window.location.href = 'index.php?page=proveedores';</script>";
exit;
?>