<?php
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query_delete = "DELETE FROM articulos WHERE id_articulos = $id";

    if (mysqli_query($conexion, $query_delete)) {
        echo "<script>window.location.href='index.php?page=articulos';</script>";
        exit();
    }
}
?>