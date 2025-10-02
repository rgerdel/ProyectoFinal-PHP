<?php
header('Content-Type: text/html; charset=UTF-8'); 
$token = $_GET['token'] ?? '';// Obtengo el token de la URL
if (!$token) { // Si no hay token, muestro error
    die("Token inválido.");
}

require_once "connection.php";  // Conexión a la base de datos
//Consulta para verificar si el token es válido y no ha expirado
$consulta = $conn->prepare("SELECT email, expira_en FROM password_resets WHERE token = ? AND usado_en = 0");
$consulta->bind_param("s", $token); // Asigno el valor del token a la variable
$consulta->execute();               // Ejecuto la consulta
$consulta->store_result();          // Almaceno el resultado

// Condición para verificar si el token existe
if ($consulta->num_rows === 0) {
    die("Token inválido o ya usado.");
}

$consulta->bind_result($email, $expira_en); // Asigno los resultados a variables
$consulta->fetch();                         // Obtengo los valores

// Verifico si el token ha expirado
if (new DateTime() > new DateTime($expira_en)) {
    die("El Token ha expirado., por favor intente restablecer su contraseña nuevamente.");
}

$consulta->close();                 // Cierro la consulta
$conn->close();                     // Cierro la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Net-M@rket</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full mb-4">
                <i class="fas fa-sign-in-alt text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Restablecer Contraseña</h1>  
        </div> 


<div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">   
    <form action="controllers/recuperarController.php" method="POST">
      <input type="hidden" name="token" value="<?= ($token) ?>">
      <input type="hidden" name="email" value="<?= ($email) ?>">
      <!-- Contraseña -->
                <div>
                    <label for="contrasena" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-blue-600"></i>Contraseña
                    </label>
                    <div class="relative">
                        <input type="password" id="contrasena" name="contrasena" required
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword('contrasena')"
                                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirmar Contraseña -->
                <div class="mt-4">
                    <label for="confirmar_contrasena" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-blue-600"></i>Confirmar Contraseña
                    </label>
                    <div class="relative">
                        <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword('confirmar_contrasena')"
                                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
      <button type="submit" class="mt-4 w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition duration-200 shadow-lg">
        <i class="fas fa-sign-in-alt mr-2"></i>Restablecer contraseña</button>
    </form>
  </div>
</div>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>