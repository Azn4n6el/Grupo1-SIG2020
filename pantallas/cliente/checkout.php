<?php
include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['sessionSucursal'])) {
    header('Location: elegirSucursal.php');
}
$ruc_sucursal = $_SESSION['sessionSucursal'];

$obj = new Conexion();

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
        <form action="../../procesos/guardarCompra.php" method="POST" id="form-compra" class="form-compra">
            <div class="form-group">
                <div class="form-input2">
                    <label for="cedula">Cédula</label>
                    <input type="text" name="cedula" id="cedula" class="compra-input enviar-input" placeholder="1-111-1111" required>
                </div>
                <div class="form-input2">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="compra-input enviar-input" placeholder="Ex: Pedro Lopez" required>
                </div>
            </div>
            <div class="form-group">
                <div class="form-input2">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="compra-input enviar-input" placeholder="12345678" onkeydown="validateNumber(event,this.id)" required>
                </div>
                <div class="form-input2">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" class="compra-input enviar-input" placeholder="Bethania, Torre 88, Apt 12C" required>
                </div>
            </div>
            <div class="form-group2">
                <div class="radio-input">
                    <input type="radio" name="pago" id="efectivo" value="Efectivo" onclick="formaPago(this.id)" required>
                    <label for="efectivo">Efectivo</label>
                </div>
                <div class="radio-input">
                    <input type="radio" name="pago" id="tarjeta" value ="Tarjeta" onclick="formaPago(this.id)" required>
                    <label for="tarjeta">Tarjeta</label>
                </div>
            </div>
            <div class="checkout-card form-group">
                <div class="form-input2">
                    <label for="propietario">Propietario de Tarjeta</label>
                    <input type="text" name="propietario" id="propietario" class="compra-input enviar-input" placeholder="Pedro Lopez">
                </div>
                <div class="form-input2">
                    <label for="fecha">Fecha de Expiración</label>
                    <input type="month" name="fecha" id="fecha" class="compra-input enviar-input" placeholder="2020-07">
                </div>
                <div class="form-input2">
                    <label for="numTarjeta">Número de Tarjeta</label>
                    <input type="text" name="numTarjeta" id="numTarjeta" class="compra-input enviar-input" onkeydown="validateNumber(event,this.id)" placeholder="0000-0000-0000-0000" autocomplete="off">
                </div>
                <div class="form-input2">
                    <label for="cvv">CVV</label>
                    <input type="text" name="cvv" id="cvv" class="compra-input enviar-input" maxlength="3" onkeydown="validateNumber(event,this.id)" placeholder="123">
                </div>
            </div>

            <input type="hidden" name="compras" id="compras">
            <div class="compra-submit checkout-container">
                <input type="submit" value="Comprar">
            </div>

        </form>
    </div>

    <!-- Footer -->
    <?php require('footer.html'); ?>
</body>

<!-- MODAL -->
<?php require('custom-modal.html') ?>

</html>
<script src="../../js/globalFunctions.js"></script>
<script>
    //CANTIDAD DE PRODUCTOS EN EL HEADER
    let carritoProducts = window.localStorage.getItem('carrito');
    let compras = document.getElementsByClassName('cant-compras');
    if (carritoProducts != null && carritoProducts != undefined && carritoProducts != "") {
        carritoProducts = JSON.parse(window.localStorage.getItem('carrito'));
        compras[0].innerHTML = carritoProducts.length;
    } else {
        carritoProducts = [];
    }

    let inputCompra = document.getElementById('compras');
    let compra = window.localStorage.getItem('compra');
    if (compra != null && compra != undefined && compra != "") {
        compra = JSON.parse(window.localStorage.getItem('compra'));
        inputCompra.value = JSON.stringify(compra);
    } else {
        location.href = "carrito.php";
    }


    const formaPago = (id) => {
        let card = document.getElementsByClassName('checkout-card');
        let propietario = document.getElementById('propietario');
        let fecha = document.getElementById('fecha');
        let numTarjeta = document.getElementById('numTarjeta');
        let cvv = document.getElementById('cvv');

        if (id == 'tarjeta') {
            card[0].style.display = 'flex';
            propietario.required = true;
            fecha.required = true;
            numTarjeta.required = true;
            cvv.required = true;
        } else {
            card[0].style.display = 'none';
            propietario.required = false;
            fecha.required = false;
            numTarjeta.required = false;
            cvv.required = false;
        }
    }

    const validateNumber = (event, id) => {
        let value = document.getElementById(id);
        if (!(event.keyCode == 8 || (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 173 || event.keyCode == 109)) {
            event.preventDefault();
        }
    }
</script>