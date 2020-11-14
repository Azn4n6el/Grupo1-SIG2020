<?php
include 'Conexion.php';
session_start();

$obj = new Conexion();
$notificaciones = $obj->GetNotificaciones();
$dataNumber = $_POST['dataNumber'];

$_SESSION['dataNotifications'] = $notificaciones[$dataNumber];
header('location: ../pantallas/administrador/enviarPedidos.php');
?>