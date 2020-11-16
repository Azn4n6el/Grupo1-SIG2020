<?php 
include 'Conexion.php';
session_start();

$obj = new Conexion();

$cedula = $_POST['cedula'];
$contrasena = $_POST['passwd'];

$data = $obj->ValidateLogin($cedula, $contrasena);
var_dump($data);

if ($data == NULL){
    $_SESSION['message'] = 'Cédula o contraseña incorrecta.';
    header('Location: ../pantallas/administrador/login.php');
} else {
    $_SESSION['user-data'] = $data;
    header('Location: ../pantallas/administrador/clientes.php');
}

