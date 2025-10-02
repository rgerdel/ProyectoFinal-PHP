<?php
session_start();                 // mejor arriba, antes de cualquier salida
require_once "../connection.php";

$regex_email    = '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/';
$errores        = [];

if ($_POST) {

    $email = trim($_POST['email']  ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if (!preg_match($regex_email, $email)) {
        $errores[] = "El email no es válido.";
    }

        if ($errores) {
        $_SESSION["error"] = implode("<br>", $errores);
        header("Location: ../login.php");
        exit();
    }

    if (!$errores) {
        //Realizo consulta BD para verificar usuario
        $consulta = $conn->prepare("SELECT nombre, password_hash, rol FROM usuarios WHERE email = ?");
        $consulta->bind_param('s', $email); //Paso parametro string email a la consulta
        $consulta->execute(); //Ejecuto consulta
        $consulta->store_result(); //Almaceno resultado

        if ($consulta->num_rows === 0) { //Verifico cuantos registros trajo la consulta, si es 0 no existe el email
            $errores[] = "El email no está registrado.";
        } else { // de lo contrario, si existe el email
            $consulta->bind_result($nombre, $hash, $rol); //Vinculo variables a los 3 campos de la consulta
            $consulta->fetch(); //Obtengo los datos de la consulta

        //Verifico la contraseña hasheada usando password_verify
            if (!password_verify($contrasena, $hash)) {
                $errores[] = "Contraseña incorrecta.";
            } else {
                /* 4. Todo OK: crear sesión */
                $_SESSION["nombre"] =  $nombre;
                $_SESSION["email"] =  $email;
                $_SESSION["rol"] =  $rol;
         
                ;
                /* 5. Recordar usuario (opcional) */
                //if (!empty($_POST['recordar'])) {
                 //    setcookie('email', $email, time() + 86400 * 30, '/');
                 //}
                 header('Location: ../productos.php'); // o la página que quieras
                 exit;
            }
        }
        $consulta->close();
        $conn->close();
        
        
    }

    /* Si llegamos aquí algo falló */
    $_SESSION['error'] = implode('<br>', $errores);
    header('Location: ../login.php');
    exit;
}