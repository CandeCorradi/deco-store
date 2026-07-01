<?php
// php/actualizar_inline_universal.php

// 💡 IMPORTANTE: Verificá si la conexión se incluye así o si lleva '../' 
// Si este archivo está adentro de la carpeta "php", su conexión suele estar en el mismo directorio.
include 'conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $columna = isset($_POST['columna']) ? mysqli_real_escape_string($conexion, $_POST['columna']) : '';
    $valor = isset($_POST['valor']) ? mysqli_real_escape_string($conexion, $_POST['valor']) : '';
    $tabla = isset($_POST['tabla']) ? mysqli_real_escape_string($conexion, $_POST['tabla']) : '';

    if ($id > 0 && !empty($columna) && !empty($tabla)) {
        
        // La clave primaria dinámica (id_caja, id_articulos, etc.)
        $pk = 'id_' . $tabla; 
        
        // Regla especial para los importes de la tabla caja
        if ($tabla === 'caja' && $columna === 'importe') {
            // Limpiamos cualquier signo raro, espacio o $ que pueda mandar el input
            $valor_limpio = str_replace(['$', '.', ' '], '', $valor);
            $valor_limpio = str_replace(',', '.', $valor_limpio); // Por si usás coma decimal
            $valor_absoluto = abs(floatval($valor_limpio));
            
            // Traemos el registro tal cual está HOY en la base de datos antes del update
            $q_actual = "SELECT importe FROM caja WHERE id_caja = $id";
            $r_actual = mysqli_query($conexion, $q_actual);
            
            if ($r_actual && mysqli_num_rows($r_actual) > 0) {
                $f_actual = mysqli_fetch_assoc($r_actual);
                
                // 🔴 Si el importe en la base de datos era MENOR a 0, significa que SI O SI es un egreso.
                // Forzamos a que el nuevo valor se guarde con el signo menos.
                if (floatval($f_actual['importe']) < 0) {
                    $valor = -$valor_absoluto;
                } else {
                    $valor = $valor_absoluto;
                }
            }
        }        
    }
}