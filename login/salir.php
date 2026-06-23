<?php
// login/logout.php
session_start();
session_unset();
session_destroy();

// Lo redirige al formulario de ingreso
header("Location: index.php");
exit();
?>