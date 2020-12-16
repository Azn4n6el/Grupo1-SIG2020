<?php
include '../../procesos/Conexion.php';
session_start();
if (isset($_POST['select-sucursal'])){
    $ruc_sucursal = $_POST['select-sucursal'];
    $_SESSION['sessionSucursal'] = $ruc_sucursal;
    header('Location: ../pantallas/cliente/main.php');
} else {
    header('Location: ../pantallas/cliente/elegirSucursal.php');
}
