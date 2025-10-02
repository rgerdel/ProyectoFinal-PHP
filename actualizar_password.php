<?php
session_start(); 
require_once "connection.php";

$token = $_POST['token'] ?? '';
$password = $_POST['contrasena'] ?? '';

if (!$token || !$password) {
    die("Datos incompletos.");
}

// Verificar token válido y no usado
$stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND usado_en = 0");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Token inválido o expirado.");
}

$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

// Hashear nueva contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

// Actualizar contraseña
$stmt = $conn->prepare("UPDATE usuarios SET password_hash = ? WHERE email = ?");
$stmt->bind_param("ss", $hash, $email);
$stmt->execute();

// Marcar token como usado
$stmt = $conn->prepare("UPDATE password_resets SET usado_en = now() WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();

echo "Contraseña restablecida con éxito. <a href='../login.php'>Iniciar sesión</a>";

$stmt->close();
$conn->close();

// Limpiar sesión
session_unset();
session_destroy();
exit;
?>

