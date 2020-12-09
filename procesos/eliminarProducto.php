<?php
include 'Conexion.php';
session_start();

$obj = new Conexion();
$ruc_centro = $_SESSION['user-data']['ruc_centro'];
$suministros = $obj->GetSuministros();
$repetido = false;

if (!empty($_GET)) {
    $id_producto = $_GET['id_producto'];
    $msj = $obj->EliminarProducto($id_producto);
}

$_SESSION['message'] = $msj;
header('location: ../pantallas/administrador/mantProductos.php');
