<?php
include '../../procesos/Conexion.php';
session_start();

$obj = new Conexion();
$notificaciones = $obj->GetNotificaciones();
$dataNumber = $_POST['dataNumber'];

$_SESSION['dataNotifications'] = $notificaciones[$dataNumber];
header('location: enviarPedidos.php');
?>