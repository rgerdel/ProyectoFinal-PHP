<?php session_start();
$errores = []; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Net-M@rket</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-md text-center">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full mb-8">
      <i class="fa-solid fa-lock text-white text-4xl"></i>
    </div>
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Recuperar Contraseña</h1>
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <p class="mb-6 text-gray-600 text-left">Escribe la dirección de correo electrónico asociado a tu cuenta de Net-M@rket</p>
        <form action="enviar_token.php" method="POST">
          <input type="email" name="email" required class="w-full px-4 py-2 border rounded mb-4" placeholder="Correo electrónico">
          <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
          <i class="fas fa-sign-in-alt mr-2"></i>Continuar</button>
        </form>
 
    <?php 
      if (isset($_SESSION['error'])): 
      ?>
        <p class="mt-4 text-sm text-red-600"><?= $_SESSION['error'] ?></p>
      <?php 
        unset($_SESSION['error']); 
      endif; 
    ?>

    <div class="mt-6 space-y-4">
      <p class="text-center text-gray-600">
          ¿Ya tienes una cuenta?
          <a href="login.php" class="text-blue-600 hover:text-blue-700 font-semibold">Iniciar Sesión</a>
      </p>
      <p class="text-center text-gray-600">
          ¿No tienes una cuenta?
          <a href="registro.php" class="text-blue-600 hover:text-blue-700 font-semibold">Regístrate</a>
      </p>
    </div>
    </div>
</div>  
</body>
</html>