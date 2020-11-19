<?php
session_start();
$categoria = $_GET['categoria'];
$_SESSION['categoriaSelected'] = $categoria;

header('Location: ../pantallas/cliente/productos.php');
?>
