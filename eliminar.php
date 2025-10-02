<?php
session_start();
require_once "connection.php";

if ($_GET){
    $id = $_GET["id"] ?? null;

    if ($id) {

      //Obtener la ruta de la imagen actual
        $consulta = $conn->prepare('SELECT imagen_url FROM productos WHERE id = ?');    
        $consulta->bind_param('i', $id);
        $consulta->execute();
        $consulta->bind_result($imagen_url);
        if ($consulta->fetch() && $imagen_url && file_exists($imagen_url)) {
            unlink($imagen_url); // Eliminar la imagen del servidor unlink
        }
        $consulta->close();
        // Eliminar el producto de la base de datos
        
        $consulta = $conn->prepare('DELETE FROM productos WHERE id = ?');
        $consulta->bind_param('i', $id);
        if ($consulta->execute()) {
            $_SESSION["success"] = "Producto eliminado exitosamente. ";
        } else {
            $_SESSION["error"] = "Error al eliminar el producto.";
        }
        $consulta->close();
    } else {
        $_SESSION["error"] = "ID de producto no proporcionado.";
    }
    header("Location: productos.php");
    exit();
} else {
    $_SESSION["error"] = "Solicitud inválida.";
    header("Location: productos.php");
    exit();

}
?>