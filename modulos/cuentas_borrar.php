<?php
// modulos/cuentas_borrar.php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    mysqli_query($conexion, "UPDATE cuentas SET esta_eliminado = 1 WHERE id_cuenta = $id");
}
echo "<script>window.location.href = 'index.php?page=cuentas';</script>";
exit;
?>