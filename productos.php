<?php
//Iniciar sesión
session_start();

//Conecto con la base de datos
require_once "connection.php";

//Obtengo datos de sesión y la paso a variables
$email  = $_SESSION["email"] ?? null;
$nombre = $_SESSION["nombre"] ?? '';
$rol    = $_SESSION["rol"]    ?? '';
$estatus = $_GET["estatus"] ?? null;
$id = $_GET["id"] ?? null;

// Paginación
$limite = 4; // productos por página
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $limite;
$totalPaginas = 1; // valor por defecto, se sobrescribe después

//Condiciono si existe el email para obtener nombre y rol del usuario
if ($email) {
    $consulta = $conn->prepare('SELECT nombre, rol FROM usuarios WHERE email = ?');
    $consulta->bind_param('s', $email);
    $consulta->execute();
    $consulta->bind_result($nombre, $rol);
    $consulta->fetch();
    $consulta->close();
    // Almaceno en sesión nombre y rol
    $_SESSION['nombre'] = $nombre;
    $_SESSION['rol']    = strtoupper($rol);  // strtoupper Convierto mayúsculas
} 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net-M@rket</title>
    <script src="https://cdn.tailwindcss.com "></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css ">
</head>
<body class="bg-gradient-to-br from-blue-50 to-teal-50 min-h-screen p-4">
<div class="max-w-4xl mx-auto">
    <a href="logout.php"
    class="inline-flex items-center px-1 py-1 bg-red-600 text-white rounded hover:bg-red-700">
    <i class="fas fa-sign-out-alt mr-1"></i>Cerrar sesión
    </a>
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-teal-600 rounded-full mb-4">
            <i class="fas fa-box text-white text-2xl"></i>
        </div>
        <?php if ($rol === 'admin'): ?>
        <h1 class="text-3xl font-bold text-gray-800">Registro de Productos</h1>
        <?php else: ?>
        <h1 class="text-3xl font-bold text-gray-800">Catálogo de Productos</h1>
        <?php endif; ?>
        <?php if ($email): ?>
            <p class="text-blue-700 mt-2 font-semibold text-sm">
                Bienvenido, <?= strtoupper($nombre) ?> (<?= strtoupper($rol) ?>).
            </p>
        <?php else: ?>
            <p class="text-red-600 mt-2">Sesión no iniciada.</p>
        <?php endif; ?>
    </div>

    <?php
    if (isset($_SESSION["error"]) && $_SESSION["error"] != null) {
        echo "<div class='mb-4 p-4 border border-red-500 bg-red-100 text-red-700 rounded'>";
        echo "<p>" . $_SESSION["error"] . "</p>";
        echo "</div>";
        unset($_SESSION["error"]);
    } elseif (isset($_SESSION["success"])) {
        echo "<div class='mb-4 p-4 border border-blue-500 bg-blue-100 text-blue-700 rounded'>";
        echo "<p>" . $_SESSION["success"] . "<i class='fa-solid fa-thumbs-up text-blue-600 text-3xl'></i></p>";
        echo "</div>";
        unset($_SESSION["success"]);
    }
    ?>

    <?php if ($rol === 'admin'): ?>
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
        <form action="controllers/productosController.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-tag mr-2 text-blue-600"></i>Nombre del Producto</label>
                    <input type="text" name="nombre" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Ingresa el nombre del producto" value="<?= $_SESSION["producto"] ?? '' ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-align-left mr-2 text-blue-600"></i>Descripción</label>
                    <textarea name="descripcion" rows="4" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Características principales..."><?= htmlspecialchars($_SESSION['descripcion'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-dollar-sign mr-2 text-blue-600"></i>Precio</label>
                    <div class="relative"><span class="absolute left-3 top-3 text-gray-500">$</span><input type="number" name="precio"  min="0" required class="w-full pl-8 pr-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0.00" value="<?= $_SESSION["precio"] ?? '' ?>"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-cubes mr-2 text-blue-600"></i>Stock</label>
                    <input type="number" name="stock" min="0" required class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0" value="<?= $_SESSION["stock"] ?? '' ?>">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-image mr-2 text-blue-600"></i>Imagen del Producto</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500">
                    <?php if (isset($_SESSION['imagen_url']) && $_SESSION['imagen_url']): ?>
                        <p class="text-sm text-gray-600 mb-2">Imagen actual:</p>
                        <img src="<?= htmlspecialchars($_SESSION['imagen_url']) ?>" alt="Imagen del producto" class="mx-auto max-h-48">
                    <?php endif; ?>
                    <input type="file" name="imagen" accept="image/*" <?= isset($_SESSION['imagen_url']) ? '' : 'required' ?> class="hidden" id="imgInp" onchange="previewImage(event)">
                    <label for="imgInp" class="cursor-pointer"><i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i><p class="text-gray-600">Haz clic para subir una imagen</p><p class="text-xs text-gray-500">PNG, JPG, GIF hasta 10 MB</p></label>
                    <div id="imagePreview" class="mt-4 hidden"><img id="preview" class="mx-auto max-h-48 rounded-lg shadow-md"></div>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2"><i class="fas fa-toggle-on mr-2 text-blue-600"></i>Estado</label>
                <div class="flex space-x-6">
                    <label class="flex items-center"><input type="radio" name="estado" value="activo" checked class="mr-2" <?= ($_SESSION['estado'] ?? 'activo') === 'activo' ? 'checked' : '' ?>>Activo</label>
                    <label class="flex items-center"><input type="radio" name="estado" value="inactivo" class="mr-2" <?= ($_SESSION['estado'] ?? 'inactivo') === 'inactivo' ? 'checked' : '' ?>>Inactivo</label>
                </div>
            </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-teal-600 text-white font-semibold py-3 rounded-lg hover:from-blue-700 hover:to-teal-700 transition">Guardar Producto</button>
                <button type="reset"  class="flex-1 bg-gray-200 text-gray-700 font-semibold py-3 rounded-lg hover:bg-gray-300 transition">Limpiar Formulario</button>
            </div>
            <input type="hidden" name="estatus" value="<?= $estatus ?? '' ?>">
            <input type="hidden" name="id" value="<?= $id ?? '' ?>">
        </form>
    </div>  

    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-list mr-2 text-blue-600"></i>Listado de Productos</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b"><th class="text-left py-2">Nombre</th><th class="text-left py-2">Precio</th><th class="text-left py-2">Stock</th><th class="text-left py-2">Imagen</th></tr></thead>
                <tbody>
                <?php
                $consulta = $conn->prepare('SELECT id, nombre, descripcion, precio, stock, imagen_url FROM productos LIMIT ? OFFSET ?');
                $consulta->bind_param('ii', $limite, $offset);
                $consulta->execute();
                $consulta->bind_result($id, $nombre, $descripcion, $precio, $stock, $imagen_url);
                while ($consulta->fetch()) {
                    echo "<tr class='border-b hover:bg-gray-50 cursor-pointer' onclick='abrirModal({$id})'>
                        <td class='py-2'>{$nombre}</td>
                        <td class='py-2'>\${$precio}</td>
                        <td class='py-2'>{$stock}</td>
                        <td class='py-2'><img src='{$imagen_url}' class='w-20 h-20 object-cover rounded'></td>
                        <td><a href='controllers/actualizarController.php?id={$id}&estatus=actualizar' class='text-purple-600'><i class='fa-solid fa-pen mr-1'></i></a></td>
                        <td><a href='eliminar.php?id={$id}' onclick=\"return confirm('¿Está seguro de eliminar este producto?');\" class='text-red-600'><i class='fa-solid fa-trash mr-1'></i></a></td>
                    </tr>";
                }
                $consulta->close();

                $totalConsulta = $conn->prepare('SELECT COUNT(*) FROM productos');
                $totalConsulta->execute();
                $totalConsulta->bind_result($totalProductos);
                $totalConsulta->fetch();
                $totalConsulta->close();
                $totalPaginas = ceil($totalProductos / $limite);
                ?>
                </tbody>
            </table>

            <?php if ($totalPaginas > 1): ?>
            <div class="flex justify-center mt-6 space-x-2">
                <?php if ($pagina > 1): ?>
                    <a href="?pagina=<?= $pagina - 1 ?>" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>" class="px-3 py-2 rounded <?= $i == $pagina ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a href="?pagina=<?= $pagina + 1 ?>" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Siguiente</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    <!-- USUARIOS NORMALES -->
    <div class="bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-list mr-2 text-blue-600"></i>Listado de Productos</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b"><th class="text-left py-2">Nombre</th><th class="text-left py-2">Descripción</th><th class="text-left py-2">Precio</th><th class="text-left py-2">Imagen</th></tr></thead>
                <tbody>
                <?php
                    $consulta = $conn->prepare('SELECT id, nombre, descripcion, precio, stock, imagen_url FROM productos WHERE estado = "activo" LIMIT ? OFFSET ?');
                    $consulta->bind_param('ii', $limite, $offset);
                    $consulta->execute();
                    $consulta->bind_result($id, $nombre, $descripcion, $precio, $stock, $imagen_url);
                    while ($consulta->fetch()) {
                        echo "<tr class='border-b hover:bg-gray-50 cursor-pointer' onclick='abrirModal({$id})'>
                            <td class='py-2'>{$nombre}</td>
                            <td class='py-2'>" . htmlspecialchars(substr($descripcion, 0, 30)) . "...</td>
                            <td class='py-2'>{$precio}</td>
                            <td class='py-2'><img src='{$imagen_url}' class='w-20 h-20 object-cover rounded'></td>
                        </tr>";
                    }
                    $consulta->close();

                    $totalConsulta = $conn->prepare('SELECT COUNT(*) FROM productos WHERE estado = "activo"');
                    $totalConsulta->execute();
                    $totalConsulta->bind_result($totalProductos);
                    $totalConsulta->fetch();
                    $totalConsulta->close();
                    $totalPaginas = ceil($totalProductos / $limite);
                ?>
                </tbody>
            </table>

            <?php if ($totalPaginas > 1): ?>
            <div class="flex justify-center mt-6 space-x-2">
                <?php if ($pagina > 1): ?>
                    <a href="?pagina=<?= $pagina - 1 ?>" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>" class="px-3 py-2 rounded <?= $i == $pagina ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagina < $totalPaginas): ?>
                    <a href="?pagina=<?= $pagina + 1 ?>" class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">Siguiente</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- JavaScript para vista previa de imagen seleccionada-->
<script>
function previewImage(e){
    const file = e.target.files[0], preview = document.getElementById('preview'), box = document.getElementById('imagePreview');
    if(file){
        const reader = new FileReader();
        reader.onload = ev => { preview.src = ev.target.result; box.classList.remove('hidden'); };
        reader.readAsDataURL(file);
    }
}
</script>

<!-- Modal de detalle -->
<div id="modalDetalle" class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 relative">
    <button onclick="cerrarModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
      <i class="fas fa-times text-xl"></i>
    </button>
    <div id="modalContenido" class="space-y-4">
      <!-- Aquí se cargará el detalle del producto -->
    </div>
  </div>
</div>

<!-- JavaScript para manejar ventana el modal de detalle del producto.-->
<script>
function abrirModal(id) {
  fetch('detalle_producto.php?id=' + id)
    .then(res => res.text())
    .then(html => {
      document.getElementById('modalContenido').innerHTML = html;
      document.getElementById('modalDetalle').classList.remove('hidden');
    });
}

function cerrarModal() {
  document.getElementById('modalDetalle').classList.add('hidden');
}
</script>

</body>
</html>