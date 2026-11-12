<?php
// exportar_caja_excel.php
require_once __DIR__ . '/php/conexion.php';

$query = "SELECT c.*, cu.cuenta AS nombre_cuenta 
          FROM caja c 
          INNER JOIN cuentas cu ON c.id_cuenta = cu.id_cuenta 
          WHERE c.esta_eliminado = 0 
          ORDER BY c.id_caja DESC";
$resultado = mysqli_query($conexion, $query);

$nombre_archivo = "DecoStore_CajaDiaria_" . date('Ymd_His') . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$nombre_archivo\"");
header("Pragma: no-cache");
header("Expires: 0");
echo "\xEF\xBB\xBF";
?>
<table border="1">
    <tr>
        <th colspan="5" style="background-color: #2E7D32; color: #FFFFFF; font-size: 16px;">DECOSTORE - MOVIMIENTOS DE CAJA DIARIA</th>
    </tr>
    <tr style="background-color: #EEEEEE;">
        <th>Código</th>
        <th>Fecha / Hora</th>
        <th>Cuenta</th>
        <th>Detalle / Motivo</th>
        <th>Importe</th>
    </tr>
    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
    <tr>
        <td align="center">#<?php echo $fila['id_caja']; ?></td>
        <td><?php echo date('d/m/Y H:i', strtotime($fila['fecha'])); ?></td>
        <td><?php echo htmlspecialchars($fila['nombre_cuenta']); ?></td>
        <td><?php echo htmlspecialchars($fila['detalle']); ?></td>
        <td align="right" style="color: <?php echo $fila['importe'] >= 0 ? '#2E7D32' : '#C1392B'; ?>;">
            <?php echo ($fila['importe'] < 0 ? '-' : '') . '$' . number_format(abs($fila['importe']), 2, ',', '.'); ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>