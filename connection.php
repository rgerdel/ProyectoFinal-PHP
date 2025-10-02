<?php
  
 #Crear conexión a base de datos y guardar los datos
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "e_commerce_pf";

  $conn = new mysqli($servername, $username, $password, $dbname);
  
  if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
  }

  $conn->set_charset("utf8mb4");