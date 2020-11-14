<?php
include 'Conexion.php';
session_start();

$obj = new Conexion();
$ruc_centro = $_SESSION['user-data']['ruc_centro'];
$notificaciones = $obj->GetNotificaciones($ruc_centro);
$dataNumber = $_POST['dataNumber'];

$_SESSION['dataNotifications'] = $notificaciones[$dataNumber];
header('location: ../pantallas/administrador/enviarPedidos.php');
?>