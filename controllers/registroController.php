<?php
if (session_status() === PHP_SESSION_NONE) {    // Verifica que la sesión no ha sido iniciada
    session_start();                            // Inicio de sesión
}

// Validaciones con expresiones regulares
// Nombre: letras y espacios, entre 2 y 40 caracteres
$regex_nombre = "/^[a-zA-ZÀ-ÿ\s]{2,40}$/i"; 
// Email por defecto acepta letras, números, puntos, guiones y guion bajo
$regex_email = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/"; // Formato de email estándar
// Mínimo ocho caracteres, al menos una letra mayúscula, una letra minúscula, un número y un carácter especial
$regex_password = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\$@$!%*?&.])[A-Za-z\d\$@$!%*?&.]{8,}$/"; 

$errores = []; // Limpiar errores

if ($_POST) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    // Validaciones preg_match devuelve true si el nombre cumple con el patrón
    if (!preg_match($regex_nombre, $nombre)) {
        $errores[] = "El nombre no es válido. Por favor, use solo letras y espacios.";
    }
    // Validaciones preg_match devuelve true si el email cumple con el patrón
    if (!preg_match($regex_email, $email)) {
        $errores[] = "El email no es válido. Por favor, ingrese un email con el formato correcto";
    }
    // Validaciones preg_match devuelve true si la contraseña cumple con el patrón
    if (!preg_match($regex_password, $contrasena)) {
        $errores[] = "La contraseña no es válida. Debe tener al menos 8 caracteres, una letra mayúscula, una letra minúscula, un número y un carácter especial.";
    }
    // Valida que ambas contraseñas coincidan
    if ($contrasena !== $confirmar_contrasena) {
        $errores[] = "Las contraseñas no coinciden. Por favor, inténtelo de nuevo.";
    }

    // Si hay errores, redirigir con mensajes de error
    if ($errores) {
        $_SESSION["error"] = implode("<br>", $errores);
        header("Location: ../registro.php");
        exit();
    }

    // Si no hay errores, insertar en la base de datos
    
    require_once "../connection.php"; // Conecta a la base de datos

   $consulta = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
   $consulta->bind_param("s", $email);
   $consulta->execute();
   $consulta->store_result();

   // Verifica si el email ya está registrado en la base de datos
    if ($consulta->num_rows > 0) { // Si encuentra un registro
        $_SESSION["error"] = "El email ya se encuentra registrado en nuestra base de datos.";
        header("Location: ../registro.php");
        exit();
    }
    $consulta->close();         // Cierro la consulta

    $hash = password_hash($contrasena, PASSWORD_DEFAULT); // Convierto la contraseña en hash
    // Realizo el insert en la base de datos
    $insertar = $conn->prepare("INSERT INTO usuarios (nombre, email, password_hash) VALUES (?, ?, ?)");
    $insertar->bind_param("sss", $nombre, $email, $hash);

    if ($insertar->execute()) { // Si se ejecuta correctamente
        $_SESSION["success"] = "Registro Exitoso"; 
        $_SESSION["email"] =  $email;
        $_SESSION["nombre"] =  $nombre;
        header("Location: ../login.php");
        exit();
    } else {
        $_SESSION["error"] = "Error al registrar el usuario.";
        header("Location: ../registro.php");
        exit();
    }

    $insertar->close();         // Cierro la consulta
    $conn->close();             // Cierro la conexión
    exit();                     // Asegura que no se ejecute más código después de la redirección
}