<?php
include '../../procesos/Conexion.php';
session_start();
$obj = new Conexion();
if (!isset($_SESSION['user-data'])) {
    header('Location: login.php');
}

$ruc_centro = $_SESSION['user-data']['ruc_centro'];
$notificaciones = $obj->GetNotificaciones($ruc_centro);
if (isset($_SESSION['dataNotifications'])) {
    $data = $_SESSION['dataNotifications'];
    $notification = 1;
} else {
    $notification = 0;
    $data = "";
}

$sucursales = $obj->GetSucursales($ruc_centro);
$suministros = $obj->GetSuministros();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Productos</title>
    <link rel="stylesheet" href="../../css/administrador.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>
    <div class="dashboard-container">
        <?php require 'dashboard-nav.html' ?>
        <div class="dashboard-content">
            <?php require 'dashboard-header.html' ?>
            <div class="dashboard-body">
                <div class="body-title">
                    <h1>Agregar - Productos</h1>
                </div>
                <form action="../../procesos/guardarProducto.php" method="post" id="enviarProductosForm" class="productosForm">


                    <div class="form-group">
                        <div class="form-input4">
                            <label for="categoria">Categoría</label>                                 
                        </div>
                        <div class="form-input4">
                            <input type="text" id="categoria" name="categoria" class="enviar-input custom-select" >                                  
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-input4">
                            <label for="Producto">Producto</label>                                 
                        </div>
                        <div class="form-input4">
                            <input type="text" id="producto" name="producto" class="enviar-input custom-select" >                                  
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-input4">
                            <label for="id-producto">ID-Producto</label>                                 
                        </div>
                        <div class="form-input4">
                            <input type="text" id="id-producto" name="id-producto" class="enviar-input custom-select" >                                  
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-input4">
                            <label for="tamaño">Tamaño</label>                                 
                        </div>
                        <div class="form-input4">
                            <input type="text" id="tamaño" name="tamaño" class="enviar-input custom-select" >                                  
                        </div>
                    </div>
                    <div class="enviar-button-container">
                        <input type="submit" value="Guardar" class="enviar-button">
                    </div>

                </form>
            </div>
        </div>
    </div>
    
</body>

</html>
