<?php
include 'Conexion.php';
session_start();
$obj = new Conexion();
$ruc_sucursal = $_SESSION['sessionSucursal'];
$code = $ruc_sucursal.$ruc_sucursal;
$inventario = $obj->GetInventarioBySucursal($ruc_sucursal);
$id_suministro = 0;


if (!empty($_POST)){
    $codigo = $_POST['codigo'];
    $categoria = $_POST['categoria'];
    $producto = $_POST['producto'];
    $tamano = $_POST['tamano'];
    $cantidad = $_POST['cantidad'];
    $motivo = $_POST['motivo'];
    if ($codigo == $code){
        for ($i = 0; $i < count($inventario); $i++){
            if ($inventario[$i]['id_categoria'] == $categoria && $inventario[$i]['id_producto'] == $producto && $inventario[$i]['id_tamano'] == $tamano){
                $id_suministro = $inventario[$i]['id_suministro'];
            break;
            }
        }
        var_dump($id_suministro);
        $mensaje = $obj->AddDevuelve($id_suministro,$ruc_sucursal,$cantidad,$motivo);
    } else {
        $mensaje = 'Código no válido.';
    }
}
$_SESSION['mensaje'] = $mensaje;
header('location: ../pantallas/cliente/reportar.php');