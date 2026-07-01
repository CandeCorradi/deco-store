<?php
// php/clientes_actualizar.php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturamos los datos del formulario
    $id = intval($_POST['id']);
    $nombre = mysqli_real_escape_string($conexion, trim($_POST['nombre']));
    $telefono = mysqli_real_escape_string($conexion, trim($_POST['telefono']));
    $email = mysqli_real_escape_string($conexion, trim($_POST['email']));
    $id_provincia = intval($_POST['id_provincias']);

    // Ejecutamos el UPDATE (Asegurate de que los nombres de las columnas coincidan con tu tabla 'clientes')
    $query_update = "UPDATE clientes SET 
                    nombre = '$nombre', 
                    telefono = '$telefono', 
                    email = '$email', 
                    id_provincias = $id_provincia 
                    WHERE id_clientes = $id";

    if (mysqli_query($conexion, $query_update)) {
        // Redirección limpia al index de la raíz pasándole la página de clientes
        header("Location: ../index.php?page=clientes&editado=1");
        exit();
    } else {
        echo "Error al actualizar el cliente: " . mysqli_error($conexion);
    }
} else {
    header("Location: ../index.php?page=clientes");
    exit();
}
?>