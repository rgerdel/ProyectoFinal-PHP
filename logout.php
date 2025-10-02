<?php
// Inicio sesión
session_start();               
// Limpio el array de sesión
$_SESSION = [];                 
// Borro la cookie de sesión si existe
if (ini_get("session.use_cookies")) {               //Compruebo si la sesión usa cookies
    $params = session_get_cookie_params();          // Obtengo los parámetros de la cookie
    setcookie(session_name(), '', time() - 42000,   // Hago que caduque en el pasado
        $params["path"], $params["domain"],         // Mantengo los parámetros originales
        $params["secure"], $params["httponly"]      // Mantengo los parámetros originales
    );
}

session_destroy();                                  // Destruyo la sesión
header('Location: login.php');                      // Redirijo al usuario a la página de login
exit;                                               // Aseguro que no se ejecute más código
?>