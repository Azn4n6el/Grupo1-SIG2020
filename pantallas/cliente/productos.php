<?php

include '../../procesos/Conexion.php';
session_start();

if (!empty($_REQUEST['sucursal']['categoria'])){
    $sucursal=$_REQUEST['sucursal'];
    $categoria=$_REQUEST['categoria'];
    $obj = new Conexion();
    $message1 = $obj->GetSucursal($sucursal);
    $message2 = $obj->GetSucursal($categoria);
    
}else{
    $errorMessage="No ha seleccionado una sucursal";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link rel="stylesheet" href="../../css/clientes-products.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>

<body>
    <?php require('header.php'); ?>
    <nav class="categoria" id="categoria">
    <?php ?>
    <a class="links-image" href="#"><img src="../../images/noun_product_1375596.svg" alt="imgProducto" width="30" height="30">Productos</a>
    

                   
    </nav>
</body>
<footer>
</footer>
</html>