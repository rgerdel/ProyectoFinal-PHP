<?php
session_start();

require_once "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "connection.php";
$email = $_POST['email'] ?? '';
if (!$email) {
    $_SESSION['error'] = "El correo es obligatorio.";
    header("Location: solicitarContrasena.php");
    exit;
}
// Verificar si el email existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) {
    $_SESSION['error'] = "No existe un usuario registrado con ese correo electrónico, por favor verifica e intenta de nuevo.";
    header("Location: solicitarContrasena.php");
    exit;
}   
$stmt->close();
// Generar token y expiración
$token = bin2hex(random_bytes(16));
$expira_en = date('Y-m-d H:i:s', strtotime('+1 hour'));
// Guardar token en BD
$stmt = $conn->prepare("INSERT INTO password_resets (email, token, expira_en, usado_en) VALUES (?, ?, ?, 0)");
$stmt->bind_param("sss", $email, $token, $expira_en);
$stmt->execute();
$stmt->close();
$conn->close();

// Enviar email

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8'; 
$mail->SMTPDebug = 0;
$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'rafaelgerdel@gmail.com';
$mail->Password   = 'dhdw jiam bywv bmok';
$mail->SMTPSecure = 'tls';
$mail->Port       = 587;

$mail->setFrom('no-relpy@gmail.com', 'Sistema');
$mail->addAddress($email);
$mail->isHTML(true);
$mail->Subject = 'Net-M@rket: Recupera tu Contraseña';
$mail->Body    = "
    <h2>Restablecer contraseña</h2>
    <p>Haz clic <a href='https://localhost/php/proyectofinal/restablecer.php?token=$token'>aquí</a> para restablecer tu contraseña.</p>
    <p>Expira en 1 hora.</p>
";

try {
    $mail->send();
    $_SESSION['success'] = "Revisa la bandeja de entrada o la carpeta spam de tu correo electrónico.";
    header("Location: login.php");
    exit;
} catch (Exception $e) { 
    $_SESSION['error'] = "Error al enviar el correo: " . $e->getMessage();
        header("Location: login.php");
    exit;
}


?>