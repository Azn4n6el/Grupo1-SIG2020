<?php
session_start();
$categoria = $_GET['categoria'];
$icon = $_GET['icon'];
$_SESSION['categoriaSelected'] = [$categoria, $icon];

header('Location: ../pantallas/cliente/productos.php');
?>
