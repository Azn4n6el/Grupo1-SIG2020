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
    <title>Carrito</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/clientes.css">

</head>

<body>
    <?php require('head.html'); ?>
    <div class="main-container">
        <div class="carrito-title">
            <h1>CARRITO DE COMPRA</h1>
        </div>
        <div class="carrito-body">
            <div class="carrito-products">
                <div class="carrito-subtitle">
                    <div>
                        <h2>PRODUCTO</h2>
                    </div>
                    <div>
                        <h2>CANTIDAD</h2>
                    </div>
                    <div>
                        <h2>PRECIO (CAJA)</h2>
                    </div>
                </div>
                <div id="products-container2">
                </div>
            </div>
            <div class="carrito-total">
                <div class="cart-icon">
                    <img src="https://img.icons8.com/carbon-copy/100/000000/shopping-cart.png" />
                </div>
                <div class="c-subtotal">
                    <div class="subtotal-label">
                        Subtotal:
                    </div>
                    <div class="subtotal-amount" id="subtotal">
                        $1.00
                    </div>
                </div>
                <div class="c-impuesto">
                    <div class="impuesto-label">
                        Impuestos 7%:
                    </div>
                    <div class="impuesto-amount" id="impuesto">
                        $0.07
                    </div>
                </div>
                <div class="c-descuentos">
                    <div class="descuento-label">
                        Descuentos:
                    </div>
                    <div class="descuento-amount" id="descuento">
                        $0.00
                    </div>
                </div>
                <div class="c-total">
                    <div class="total-label">
                        Total:
                    </div>
                    <div class="total-amount" id="total">
                        $1.07
                    </div>
                </div>
                <div class="checkout-container">
                    <button type="button" class="list-links" onclick="guardarCompra()">Checkout</button>
                </div>

            </div>
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
        document.getElementsByClassName('carrito-body')[0].innerHTML = '<h2>No tiene productos agregados por el momento </h2>';
    }

    let suministros = <?= json_encode($suministros) ?>;
    let productsDetails = [];
    let container = document.getElementById('products-container2');


    //CONSEGUIR DETALLE DEL PRODUCTO EN EL CARRITO
    for (let i = 0; i < carritoProducts.length; i++) {
        for (const item of suministros) {
            if (item.id_suministro == carritoProducts[i]) {
                productsDetails.push(item);
                break;
            }
        }
    }

    if (productsDetails.length != 0) {
        for (let i = 0; i < productsDetails.length; i++) {
            container.insertAdjacentHTML('beforeend', `<div class="product-details-container" id="product-${productsDetails[i].id_suministro}">
                        <div class="c-img-details">
                        <img class="c-product-image" src="${productsDetails[i].imagen}" alt="producto-imagen" width="150" height="150">
                            <div class="product-details">
                                <div class="c-proudct-stock">
                                   Stock: ${productsDetails[i].cantidad}
                                </div>
                                <div class="c-product-title">
                                    ${productsDetails[i].producto}
                                </div>
                                <div class="c-product-category">
                                   ${productsDetails[i].categoria}
                                </div>
                                <div class="c-product-size">
                                    ${productsDetails[i].tamaño}
                                </div>
                            </div>
                           
                        </div>
                        <div class="c-cantidad">
                            <div class="number-container">
                                <input type="number" onfocusout="validateCant(this,${productsDetails[i].cantidad})" min="1" max="${productsDetails[i].cantidad}" name="cantidad" id="${productsDetails[i].id_suministro}" class="input-cantidad enviar-input number-input" title="Introduzca un número positivo" value=1>
                                <div class="number-button-container">
                                    <button type="button" class="number-button" onclick="upNumber(${productsDetails[i].id_suministro},${productsDetails[i].cantidad})">▲</button>
                                    <button type="button" class="number-button down-number" onclick="downNumber(${productsDetails[i].id_suministro})">▼</button>
                                </div>
                            </div>
                        </div>
                        <div class="c-product-price">
                        $<span class="c-precios">${productsDetails[i].precio}</span>
                        </div>
                        <img class="c-product-delete" onclick="deleteProduct(${productsDetails[i].id_suministro})" src="https://img.icons8.com/ios-filled/35/AD241B/delete.png" alt="Borrar Producto" />
                    </div>`)
        }
    }


    const upNumber = (id, max) => {
        let idElement = document.getElementById(id);
        let upNumber = idElement.value;
        if (upNumber == "" || upNumber >= max) {
            upNumber = max - 1;
        }
        idElement.value = parseInt(upNumber) + 1;
        calculateTotals();
    }

    const downNumber = (id) => {
        let idElement = document.getElementById(id);
        let downNumber = idElement.value;
        if (downNumber == "" || downNumber < 2) {
            downNumber = 2;
        }
        idElement.value = parseInt(downNumber) - 1;
        calculateTotals();
    }

    const validateCant = (id, max) => {
        if (id.value > max) {
            id.value = max;
        } else if (id.value < 1) {
            id.value = 1;
        }
        calculateTotals();
    }

    const deleteProduct = (id) => {
        let product = document.getElementById('product-' + id);

        product.classList.add('delete-product-anim');
        setTimeout(() => {
            product.remove();
            let index = carritoProducts.indexOf(id);
            carritoProducts.splice(index, 1);
            compras[0].innerHTML = carritoProducts.length;
            window.localStorage.setItem('carrito', JSON.stringify(carritoProducts));
            calculateTotals();
            if (carritoProducts.length < 1) {
                window.localStorage.removeItem('carrito');
                document.getElementsByClassName('carrito-body')[0].innerHTML = '<h2>No tiene productos agregados por el momento </h2>';
            }
        }, 1000)



    }

    const calculateTotals = () => {

        let subtotal = document.getElementById('subtotal');
        let impuesto = document.getElementById('impuesto');
        let descuento = document.getElementById('descuento');
        let total = document.getElementById('total');

        let cantidades = document.getElementsByClassName('input-cantidad');
        let precios = document.getElementsByClassName('c-precios');

        let subtotalMonto = 0;
        let impuestoMonto = 0;

        for (let i = 0; i < cantidades.length; i++) {
            subtotalMonto += cantidades[i].value * Number(precios[i].innerHTML);
        };


        subtotal.innerHTML = '$' + subtotalMonto.toFixed(2);

        impuestoMonto = subtotalMonto * 0.07;

        impuesto.innerHTML = '$' + impuestoMonto.toFixed(2);

        total.innerHTML = '$' + (subtotalMonto + impuestoMonto).toFixed(2);
    }

    const guardarCompra = () => {
        let cantidades = document.getElementsByClassName('input-cantidad');
        let precios = document.getElementsByClassName('c-precios');
        let totalMonto = document.getElementById('total');

        let compras = [];
        for (let i = 0; i < cantidades.length; i++) {
            let compra = {
                id_suministro: parseInt(carritoProducts[i]),
                ruc_sucursal: ruc_sucursal,
                cantidad: parseInt(cantidades[i].value)
            }
            compras.push(compra);
        }

        window.localStorage.setItem('compra', JSON.stringify(compras));
        location.href = "checkout.php";
    }

    calculateTotals();
</script>