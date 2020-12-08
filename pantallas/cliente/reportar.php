<?php
include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['sessionSucursal'])) {
    header('Location: elegirSucursal.php');
}
$ruc_sucursal = $_SESSION['sessionSucursal'];

$obj = new Conexion();
$inventario = $obj->GetInventarioBySucursal($ruc_sucursal);

if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
} else {
    $mensaje = '';
}

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
            <h1>REPORTAR PRODUCTO</h1>
        </div>
        <form action="../../procesos/reportarProducto.php" method="POST" id="form-compra" class="form-compra">
            <div class="form-group">
                <div class="form-input2">
                    <label for="codigo">Código de Validación:</label>
                    <input type="text" name="codigo" id="codigo" class="compra-input enviar-input" placeholder="Solo los administradores lo tienen" required autocomplete="off">
                </div>
                <div class="form-input2">
                    <label for="categoria">Categoría</label>
                    <div class="select-container">
                        <select name="categoria" id="categoria" class="compra-input enviar-input custom-select" required onchange="showProducts(this.value)">
                            <option disabled selected>Selecione una categoría</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-input2">
                    <label for="producto">Producto</label>
                    <div class="select-container">
                        <select name="producto" id="producto" class="compra-input enviar-input custom-select" required disabled onchange="validarCantidad()">
                            <option disabled selected>Selecione un producto</option>
                        </select>
                    </div>
                </div>
                <div class="form-input2">
                    <label for="tamano">Tamaño</label>
                    <div class="select-container">
                        <select name="tamano" id="tamano" class="compra-input enviar-input custom-select" required disabled onchange="validarCantidad()">
                            <option disabled selected>Selecione un tamaño</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="form-group">
                <div class="form-input2">
                    <label for="cantidad">Cantidad / caja</label>
                    <div class="number-container number-container2">
                        <input type="number" min="1" name="cantidad" id="cantidad" class="enviar-input number-input compra-input" required>
                        <div class="number-button-container">
                            <button type="button" class="number-button" onclick="upNumber('cantidad')">▲</button>
                            <button type="button" class="number-button down-number" onclick="downNumber('cantidad')">▼</button>
                        </div>
                    </div>
                    <small id="cantidadMax">En stock: </small>
                </div>
                <div class="form-input2">
                    <label for="motivo">Motivo de Devolución</label>
                    <input type="text" name="motivo" id="motivo" class="compra-input enviar-input" required autocomplete="off">
                </div>
            </div>
            <div class="compra-submit checkout-container">
                <input type="submit" value="Devolver">
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
    //MENSAJE DE COMPRA
    let mensaje = <?= json_encode($mensaje) ?>;

    if (mensaje != '') {
        let noButton = document.getElementsByClassName('ok-button');
        let modal = document.getElementById('custom-modal');
        let msg = document.getElementById('modal-msg');

        noButton[0].style.display = 'inline-block';
        noButton[0].innerHTML = 'OK';

        noButton[1].style.display = 'none';

        document.getElementById('msg-icon').src = "https://img.icons8.com/flat_round/64/000000/error--v1.png";
        msg.innerHTML = mensaje;

        modal.style.display = 'block';
    }


    //CANTIDAD DE PRODUCTOS EN EL HEADER
    let carritoProducts = window.localStorage.getItem('carrito');
    let compras = document.getElementsByClassName('cant-compras');
    if (carritoProducts != null && carritoProducts != undefined && carritoProducts != "") {
        carritoProducts = JSON.parse(window.localStorage.getItem('carrito'));
        compras[0].innerHTML = carritoProducts.length;
    } else {
        carritoProducts = [];
    }

    let inventario = <?= json_encode($inventario) ?>;
    let cantidadMax = document.getElementById('cantidadMax');
    cantidadMax.style.display = 'none';


    let sucursalSelect = document.getElementById('sucursal');
    let categoriasSelect = document.getElementById('categoria');
    let productosSelect = document.getElementById('producto');
    let tamanosSelect = document.getElementById('tamano');

    let categorias = [];
    let productos = [];
    let tamanos = [];

    let uniqueCategory = [];
    let uniqueProducts = [];
    let uniqueTamanos = [];

    // TRAE TODAS LAS CATEGORIAS UNICAS
    for (const item of inventario) {
        if (uniqueCategory.indexOf(item.categoria) < 0) {
            uniqueCategory.push(item.categoria);
            let categoryObject = {
                categoria: item.categoria,
                categoriaID: item.id_categoria
            }
            categorias.push(categoryObject);
        }
    }

    // LLENANDO LOS SELECTS
    for (let i = 0; i < categorias.length; i++) {
        let option = document.createElement('option');
        option.text = categorias[i].categoria;
        option.value = categorias[i].categoriaID;
        categoriasSelect.appendChild(option);
    }

    const showProducts = (value) => {

        //VACIAR LOS SELECTS
        productos = [];
        tamanos = [];
        uniqueProducts = [];
        uniqueTamanos = [];

        productosSelect.length = 0;
        tamanosSelect.innerText = '';

        for (const item of inventario) {
            if (item.id_categoria == value) {
                if (uniqueProducts.indexOf(item.producto) < 0) {
                    uniqueProducts.push(item.producto);
                    let productsObject = {
                        producto: item.producto,
                        productoID: item.id_producto
                    }
                    productos.push(productsObject);
                }

                if (uniqueTamanos.indexOf(item.tamaño) < 0) {
                    uniqueTamanos.push(item.tamaño);
                    let tamanosObject = {
                        tamano: item.tamaño,
                        tamanoID: item.id_tamano
                    }
                    tamanos.push(tamanosObject);
                }
            }
        }

        productosSelect.disabled = false;
        tamanosSelect.disabled = false;

        //LLENAR SELECTS
        for (let i = 0; i < productos.length; i++) {
            let option = document.createElement('option');
            option.text = productos[i].producto;
            option.value = productos[i].productoID;
            productosSelect.appendChild(option);
        }

        for (let i = 0; i < tamanos.length; i++) {
            let option = document.createElement('option');
            option.text = tamanos[i].tamano;
            option.value = tamanos[i].tamanoID;
            tamanosSelect.appendChild(option);
        }
        validarCantidad();
    }



    const upNumber = (id) => {
        let idElement = document.getElementById(id);
        let upNumber = idElement.value;
        if (upNumber == "") {
            upNumber = 0;
        }
        if (id == 'cantidad') {
            idElement.value = parseInt(upNumber) + 1;
        }
    }

    const downNumber = (id) => {
        let idElement = document.getElementById(id);
        let downNumber = idElement.value;
        if (downNumber == "" || downNumber < 2) {
            downNumber = 2;
        }
        if (id == 'cantidad') {
            idElement.value = parseInt(downNumber) - 1;
        }
    }

    const validarCantidad = () => {

        let cantidadID = document.getElementById('cantidad');
        let cantidad = 0;
        for (const item of inventario) {
            if (item.id_producto == productosSelect.value && item.id_categoria == categoriasSelect.value && item.id_tamano == tamanosSelect.value) {
                cantidad = parseInt(item.cantidad);
            }
        }

        cantidadMax.innerHTML = "En stock: " + cantidad;
        cantidadID.max = cantidad;
        cantidadMax.style.display = 'block';


    }
</script>