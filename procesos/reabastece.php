<?php

include 'Conexion.php';
session_start();
$obj = new Conexion();

if ($_POST['notificacion'] == true){
    $notifica = $_POST['notificacion'];
    $suministro = $_POST['notificacionSuministro'];
    $sucursal = $_POST['notificacionSucursal'];
    $centro = $_POST['notificacionCentro'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
   
    $msj = $obj->AddReabastece($suministro,$sucursal,$centro,$precio,$cantidad);
    var_dump($msj);
    $msj = $msj.'<br>'.$obj->DeleteNotifica($notifica);
    unset($_SESSION['dataNotificacions']);
} else {
    $suministros = $obj->GetSuministros();
    $categoria = $_POST['categoria'];
    $producto = $_POST['producto'];
    $tamano = $_POST['tamano'];

    //ENCONTRAR EL ID SUMINISTRO
    for ($i = 0; $i < count($suministros); $i++){
        if ($suministros[$i]['id_categoria'] == $categoria && $suministros[$i]['id_producto'] == $producto && $suministros[$i]['id_tamano'] == $tamano){
            $suministro = $suministros[$i]['id_suministro'];
        break;
        }
    }

    $sucursal = $_POST['sucursal'];
    $centro = 123;
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $msj = $obj->AddReabastece($suministro,$sucursal,$centro,$precio,$cantidad);
}

$_SESSION['message'] = $msj;
header("Location: ../pantallas/administrador/clientes.php");