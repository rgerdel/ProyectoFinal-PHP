<?php
// Conecto a la Base de datos
require_once "connection.php";

$id = intval($_GET['id'] ?? 0); 
$stmt = $conn->prepare("SELECT nombre, descripcion, precio, stock, imagen_url FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre, $descripcion, $precio, $stock, $imagen_url);
$stmt->fetch();
$stmt->close();
?>
<div>
  <h2 class="text-3xl font-bold mb-2 text-center"><?= htmlspecialchars($nombre) ?></h2>
  <img src="<?= htmlspecialchars($imagen_url) ?>" class="w-medium h-64 object-cover rounded mb-4 text-center mx-auto">
  <p class="text-gray-700 mb-2"><?= nl2br(htmlspecialchars($descripcion)) ?></p>
  <p class="text-lg font-semibold">Precio: $<?= number_format($precio, 2) ?></p>
  <!--<p class="text-sm text-gray-600">Stock disponible: <?= $stock ?></p>-->
</div>
<div>
<button onclick="cerrarModal()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
  <i class="fa-regular fa-circle-xmark"></i> Cerrar
</button>
</div>

<?php
$conn->close();
?>

