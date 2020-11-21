<?php
include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['sessionSucursal'])) {
    header('Location: elegirSucursal.php');
}
$ruc_sucursal = $_SESSION['sessionSucursal'];

$obj = new Conexion();
$suministros = $obj->GetInventarioBySucursal($ruc_sucursal);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/clientes.css">

</head>

<body>
    <?php require('head.html'); ?>
    <div class="main-container">
        <div class="carrito-title">
            <h1>CHECKOUT</h1>
        </div>
    </div>

    <!-- Footer -->
    <?php require('footer.html'); ?>
</body>

<!-- MODAL -->
<?php require('custom-modal.html') ?>

</html>
<script src="../../js/globalFunctions.js"></script>
<script>

    let ruc_sucursal = <?= $ruc_sucursal ?>;
    //CANTIDAD DE PRODUCTOS EN EL HEADER
    let carritoProducts = window.localStorage.getItem('carrito');
    let compras = document.getElementsByClassName('cant-compras');
    if (carritoProducts != null && carritoProducts != undefined && carritoProducts != "") {
        carritoProducts = JSON.parse(window.localStorage.getItem('carrito'));
        compras[0].innerHTML = carritoProducts.length;
    } else {
        carritoProducts = [];
    }

    let suministros = <?= json_encode($suministros) ?>;
    let productsDetails = [];
    let container = document.getElementById('products-container2');
    
</script>