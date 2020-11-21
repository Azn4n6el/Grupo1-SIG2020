<?php
include '../../procesos/Conexion.php';
session_start();
if (isset($_SESSION['sessionSucursal'])){
    header('Location: main.php');
}

$obj = new Conexion();
$sucursales = $obj->GetAllSucursales();

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

<body>
    <?php require('head.html'); ?>
    <div class="sucursal-body">
        <div class="image-container">
            <img src="../../images/cajero.png" alt="cashier" width="900">
        </div>
        <div class="circle">
            <form action="../../procesos/sessionSucursal.php" method="POST" class="form-sucursal">
                <div class="form-title">
                    <h1>Bienvenido!</h1>
                </div>
                <div class="form-group">
                    <div class="form-single-input">
                        <label for="select-sucursal">Elegir Sucursal:</label>
                        <div class="select-container">
                            <select id="select-sucursal" name="select-sucursal" class="custom-select enviar-input" required>
                                <option value="0" disabled selected>Seleccione una sucursal</option>
                                <?php for ($i = 0; $i < count($sucursales); $i++) : ?>
                                    <option value="<?= $sucursales[$i]['ruc_sucursal'] ?>">
                                        <?=$sucursales[$i]['direccion']?>
                                    </option>
                                <?php endfor ?>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="submit" class="next-button" value="Siguiente">
            </form>
        </div>
    </div>
</body>

</html>
<script>
    window.localStorage.removeItem('carrito');
    let logoButton = document.getElementById('logo-button');
    let sucursalButton = document.getElementsByClassName('sucursal-button');
    let productosButton = document.getElementsByClassName('productos-button');
    let carritoButton = document.getElementsByClassName('carrito-button');
    sucursalButton[0].style.display = 'none';
    productosButton[0].style.display = 'none';
    carritoButton[0].style.display = 'none';
    logoButton.setAttribute('href', '#');
</script>