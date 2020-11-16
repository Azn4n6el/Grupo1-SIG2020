<?php
include '../../procesos/Conexion.php';
session_start();
if (!empty($_REQUEST['sucursal'])){
    $sucursal=$_REQUEST['sucursal'];
    $obj = new Conexion();
    $message = $obj->GetSucursal($sucursal);
    
}else{
    $errorMessage="No ha seleccionado una sucursal";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sucursal</title>
    <link rel="stylesheet" href="../../css/clientes.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>
    <header class="header-sucursal" >
        <div class="left-side-header">
            <div class="logo">
                <a href="main.php"><img src="../../images/super instant1.png" alt="logoEmpresa" width="150"></a>
             </div>
    </header>
<body>
    <div class="left-side">
            <div class="image-container">
                <img src="../../images/cajero.png" alt="cashier" width="600">
            </div>
    <div>
    <div class="right-side">
        <div class="circle">
                <div class="sucursal-info">
                    <h3>Bienvenido!</h3>
                    <h4><label for="select-sucursal">Elegir Sucursal:</label></h4>
                    <div class="select-container">
                    <select id="sucursal">
                    <option disabled selected>Selecione una sucursal</option>
                    <?php
                        $cnn = new Conexion();
                        $sucursal = $cnn->GetSucursal();
                        for ($i = 0; $i < count($sucursal); $i++) : ?>
                            <option value=<?= $sucursal[$i]['ruc_sucursal'] ?>> <?= strtoupper($sucursal[$i]['nombre']) ?></option>
                            <small><?=$errorMessage?></small>
                    <?php endfor ?>
                    </select>
                        </div>
                    <form action="../../procesos/Conexion.php" method="POST" id="sucursal">
                    <button class="next-button">Siguiente</button>
                </form>
                </div>   
        </div>  
    </div>  
   
</body>
</html>

