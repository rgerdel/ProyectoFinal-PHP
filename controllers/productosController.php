<?php
session_start();
require_once "../connection.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recibir y limpiar datos
    $nombre      = trim($_POST["nombre"]       ?? '');
    $descripcion = trim($_POST["descripcion"]  ?? '');
    $precio      = floatval($_POST["precio"]   ?? 0);
    $stock       = intval($_POST["stock"]      ?? 0);
    $estado      = $_POST["estado"]            ?? 'activo';
    $imagen      = $_FILES["imagen"]           ?? null;
    $estatus     = $_POST["estatus"]          ?? '';
    $id          = $_POST["id"]              ?? null;
    $errores     = [];

    // 2. Validaciones
    if (strlen($nombre) < 5 || strlen($nombre) > 200) {
        $errores[] = "El nombre debe tener entre 5 y 200 caracteres.";
    }
    if (strlen($descripcion) < 10 || strlen($descripcion) > 1000) {
        $errores[] = "La descripción debe tener entre 10 y 1000 caracteres.";
    }
    if ($precio < 0) {
        $errores[] = "El precio debe ser positivo.";
    }
    if ($stock < 0) {
        $errores[] = "El stock no puede ser negativo.";
    }

    // Validar imagen
    if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($imagen['type'], $allowed_types)) {
        $errores[] = "Formato no permitido (solo JPG, PNG, GIF).";
    }
    if ($imagen['size'] > 10 * 1024 * 1024) {
        $errores[] = "La imagen no debe superar 10 MB.";
    }
} elseif (!$id) {
    // Solo obligar imagen si es nuevo producto
    $errores[] = "Debes subir una imagen.";
}

    // 3. Si hay errores → volver
    if ($errores) {
        $_SESSION["error"] = implode("<br>", $errores);
        header("Location: ../productos.php");
        exit;
    }

    $imagenAnterior = null;
if ($id) {
    $consulta = $conn->prepare('SELECT imagen_url FROM productos WHERE id = ?');
    $consulta->bind_param('i', $id);
    $consulta->execute();
    $consulta->bind_result($imagenAnterior);
    $consulta->fetch();
    $consulta->close();
}

    // 4. Subir imagen
// 4. Procesar imagen solo si se subió una nueva
if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
    $uploadDir  = '../imagenes/';
    $relPath    = 'imagenes/' . uniqid('img_', true) . '.' . pathinfo($imagen['name'], PATHINFO_EXTENSION);
    $uploadFile = $uploadDir . basename($relPath);

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    if (!move_uploaded_file($imagen['tmp_name'], $uploadFile)) {
        $_SESSION["error"] = "Error al guardar la imagen.";
        header("Location: ../productos.php");
        exit;
    }
} elseif ($id) {
    // Conservar imagen anterior
    $relPath = $imagenAnterior;
} else {
    // Nuevo producto sin imagen (no debería llegar aquí por validación previa)
    $_SESSION["error"] = "Falta imagen para nuevo producto.";
    header("Location: ../productos.php");
    exit;
}

    if ($estatus === 'actualizar' && $id) {

        // Actualizar producto existente
        $actualizar = $conn->prepare(
            'UPDATE productos 
             SET nombre = ?, descripcion = ?, precio = ?, stock = ?, imagen_url = ?, estado = ? 
             WHERE id = ?'
        );
        $actualizar->bind_param("ssdissi", $nombre, $descripcion, $precio, $stock, $relPath, $estado, $id);

        if ($actualizar->execute()) {
            $_SESSION["success"] = "Producto actualizado exitosamente. ";

            // Eliminar imagen anterior si se subió una nueva
            if ($imagenAnterior && $imagen && $imagen['error'] === UPLOAD_ERR_OK) {
                $rutaCompleta = '../' . $imagenAnterior;
                if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
                    unlink($rutaCompleta);
                }
            }
        }

$actualizar->close();
$conn->close();

// Limpiar sesión
unset($_SESSION["id"], $_SESSION["producto"], $_SESSION["descripcion"], $_SESSION["precio"], $_SESSION["stock"], $_SESSION["imagen_url"], $_SESSION["estado"]);

header("Location: ../productos.php");
exit;

        header("Location: ../productos.php");
        // Eliminar imagen anterior si se subió una nueva
        if ($imagenAnterior && $imagen && $imagen['error'] === UPLOAD_ERR_OK) {
            $rutaCompleta = '../' . $imagenAnterior;
            if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
                unlink($rutaCompleta);
            }
        }
        unset($_SESSION["id"], $_SESSION["producto"], $_SESSION["descripcion"], $_SESSION["precio"], $_SESSION["stock"], $_SESSION["imagen_url"], $_SESSION["estado"]);
        exit;
    } else {
        // Nuevo producto
    
    // 5. Insertar en BD
    $insertar = $conn->prepare(
        "INSERT INTO productos (nombre, descripcion, precio, stock, imagen_url, estado)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $insertar->bind_param("ssdiss", $nombre, $descripcion, $precio, $stock, $relPath, $estado);

    if ($insertar->execute()) {
        $_SESSION["success"] = "Producto agregado exitosamente. ";
    } else {
        $_SESSION["error"] = "Error al agregar el producto. ";
    }

    $insertar->close();
    $conn->close();

    header("Location: ../productos.php");
    unset($_SESSION["id"], $_SESSION["producto"], $_SESSION["descripcion"], $_SESSION["precio"], $_SESSION["stock"], $_SESSION["imagen_url"], $_SESSION["estado"]);
    exit;
    }
}

?>