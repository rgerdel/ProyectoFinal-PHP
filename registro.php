<?php

// Inicio sesion
session_start(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net-M@rket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-blue-50 min-h-screen flex items-center justify-center p-4">
    
<div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full mb-4">
            <i class="fa-solid fa-users text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Crear una cuenta </h1>
        </div>

        <?php
        if (isset($_SESSION["error"]) && $_SESSION["error"] != null) {
            echo "<div class='mb-4 p-4 border border-red-500 bg-red-100 text-red-700 rounded'>";
            echo "<p>" . $_SESSION["error"] . "</p>";
            echo "</div>";
            unset($_SESSION["error"]); // Limpia el mensaje
        } elseif (isset($_SESSION["success"]) && $_SESSION["success"] === true) {
            echo "<div class='mb-4 p-4 border border-green-500 bg-green-100 text-green-700 rounded'>";
            echo "<p>Registro exitoso. ¡Bienvenido!</p>";
            echo "</div>";
            unset($_SESSION["success"]); // Limpia el mensaje
        }
        ?>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form method="POST" action="controllers/registroController.php" class="space-y-6">
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-blue-600"></i>Nombre Completo
                    </label>
                    <input type="text" id="nombre" name="nombre" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                           placeholder="Ingrese su nombre">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-600"></i>Correo Electrónico
                    </label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                           placeholder="tu@email.com">
                </div>

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

                <div>
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

                <button type="submit"
                        class="w-full bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transform hover:scale-105 transition duration-200 shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>Crear Cuenta
                </button>

                <p class="text-center text-gray-600">
                    ¿Ya tienes una cuenta?
                    <a href="login.php" class="text-blue-600 hover:text-blue-700 font-semibold">Iniciar Sesión</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Script para mostrar/ocultar contraseña  (Ojito)-->
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