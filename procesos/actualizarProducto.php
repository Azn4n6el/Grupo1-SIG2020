<?php
include 'Conexion.php';
session_start();

$obj = new Conexion();
$ruc_centro = $_SESSION['user-data']['ruc_centro'];
$suministros = $obj->GetSuministros();
$repetido = false;

if (!empty($_POST)){
    $id_suministro = $_POST['id'];
    $categoria = $_POST['categoria'];
    $tamano = $_POST['tamano'];
    $producto = $_POST['producto'];
    for ($i = 0; $i < count($suministros); $i++){
        if ($suministros[$i]['id_categoria'] == $categoria && 
        $suministros[$i]['id_tamano'] == $tamano && 
        $suministros[$i]['producto'] == $producto){
            $repetido = true;
        break;
        }
    }
    if (!$repetido){
        $msj = $obj->UpdateSuministros($id_suministro, $categoria, $tamano, $producto);
    } else {
        $msj = 'Error: No se pudo actualizar, el producto ya existe.';
    }
}

$_SESSION['message'] = $msj;
header('location: ../pantallas/administrador/actualizarProducto.php?id_suministro='.$id_suministro);
?>