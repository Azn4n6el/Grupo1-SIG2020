<?php
include 'Conexion.php';
session_start();
$obj = new Conexion();

if (isset($_POST)){

    $categoria = $_POST['categoria'];
    $producto = $_POST['producto'];
    $id_producto = $_POST['id-producto'];
    $tamaño = $_POST['tamaño'];

    $msg = $obj->AddNuevosProductos($categoria,$producto,$id_producto,$tamaño);


    $_SESSION['mensaje'] = $msg;
    header('Location: ../pantallas/cliente/main.php');

}