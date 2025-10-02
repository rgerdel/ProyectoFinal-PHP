<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$regex_password = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\$@$!%*?&.])[A-Za-z\d\$@$!%*?&.]{8,}$/';
$errores = [];

if ($_POST) {  // Si el formulario se ha enviado
    // Recoje los datos del formulario y lo almacena en variables
    $contrasena           = $_POST['contrasena']           ?? ''; // Nueva contraseña
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? ''; // Confirmación de la nueva contraseña
    $email                = $_POST['email']                ?? ''; // Email del usuario

    // Validar los datos del formulario: contraseña
    if (!preg_match($regex_password, $contrasena)) {
        $errores[] = "La contraseña no es válida. Debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula, un número y un carácter especial.";
    }

    //Validar que ambas contraseñas coincidan
    if ($contrasena !== $confirmar_contrasena) {
        $errores[] = "Las contraseñas no coinciden. Por favor, inténtelo de nuevo.";
    }

    // Validar los datos del formulario: email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo electrónico no válido.";
    }

    // Si hay errores, redirigir con mensajes de error
    if ($errores) {
        $_SESSION['error'] = implode('<br>', $errores);
        header('Location: ../restablecer.php');
        exit();
    }

    // Si no hay errores, actualizar la contraseña en la base de datos
    require_once '../connection.php'; // Conexión a la base de datos
    $hash = password_hash($contrasena, PASSWORD_DEFAULT); // Hashear la nueva contraseña
    // Preparar y ejecutar la consulta para actualizar la contraseña
    $update = $conn->prepare('UPDATE usuarios SET password_hash = ? WHERE email = ?'); 
    //bind_param vincula las variables a los parámetros de la consulta preparada
    $update->bind_param('ss', $hash, $email); // 'ss' indica que ambos parámetros son strings

    // Ejecutar la consulta y verificar si se actualizó una fila
    if ($update->execute() && $update->affected_rows === 1) {
        $_SESSION['success'] = 'Contraseña actualizada con éxito. Ahora puedes iniciar sesión.';
        header('Location: ../login.php'); //Redirecciono a login
    } else {
        // Si no se actualizó ninguna fila, mostrar un mensaje de error
        $_SESSION['error'] = 'Error al actualizar la contraseña. Por favor, inténtelo de nuevo.';
        header('Location: ../login.php'); //Redirecciono a login
    }

    $update->close();       // Cierra la consulta 
    $conn->close();         // Cierra la conexión
    exit();                 // Asegura que no se ejecute más código después de la redirección   
}
?>