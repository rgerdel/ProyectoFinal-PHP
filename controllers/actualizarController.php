<?php

session_start(); 

require_once "../connection.php";

if ($_GET){

    $id = $_GET["id"] ?? null;
    $estatus = $_GET["estatus"] ?? null;
    if ($id) {
        $consulta = $conn->prepare('SELECT id, nombre, descripcion, precio, stock, imagen_url, estado FROM productos WHERE id = ?'); 
        $consulta->bind_param('i', $id);
        $consulta->execute();
        $consulta->bind_result($id, $nombre, $descripcion, $precio, $stock, $imagen_url, $estado);
        $consulta->fetch();
        $consulta->close();

        $_SESSION["id"] =  $id;
        $_SESSION["producto"] =  $nombre;
        $_SESSION["descripcion"] =  $descripcion;      
        $_SESSION["precio"] =  $precio;
        $_SESSION["stock"] =  $stock;
        $_SESSION["imagen_url"] =  $imagen_url;
        $_SESSION["estado"] =  $estado;
 
header('Location: ../productos.php?id='.$id.'&estatus='.$estatus);
exit;
}
}
?>