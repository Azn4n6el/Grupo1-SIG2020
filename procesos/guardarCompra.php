<?php
include 'Conexion.php';
session_start();
$obj = new Conexion();

if (isset($_POST)){

    $cedula = $_POST['cedula'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $tarjeta = $_POST['numTarjeta'];
    $direccion = $_POST['direccion'];
    $forma_pago = $_POST['pago'];
    
    $ultFactura = $obj->GetFactura();
    $currentFactura = $ultFactura[0]['id_factura'] + 1;
    $obj->AddClientes($cedula, $nombre, $telefono, $tarjeta, $direccion);
 
    $compras = json_decode($_POST['compras'],TRUE);

    for ($i = 0; $i < count($compras); $i++){
        $compras[$i]['id_factura'] = $currentFactura;
        $compras[$i]['cedula'] = $cedula;
        $compras[$i]['forma_pago'] = $forma_pago;

        $msg = $obj->AddCompra($compras[$i]['id_factura'],
         $compras[$i]['cedula'], 
         $compras[$i]['id_suministro'], 
         $compras[$i]['ruc_sucursal'], 
         $compras[$i]['forma_pago'], 
         $compras[$i]['cantidad']);
    }

    $_SESSION['mensaje'] = $msg;
    header('Location: ../pantallas/cliente/main.php');

}

