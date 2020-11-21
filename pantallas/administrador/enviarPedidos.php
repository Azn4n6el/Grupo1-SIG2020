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
    <title>Enviar Pedidos</title>
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
                    <h1>Enviar Productos</h1>
                </div>
                <form action="../../procesos/reabastece.php" method="post" id="enviarProductosForm" class="productosForm">
                    <div class="form-group">
                        <div class="form-input2">
                            <label for="sucursal">Sucursal</label>
                            <div class="select-container">
                                <select name="sucursal" id="sucursal" class="enviar-input custom-select" required>
                                    <option disabled selected>Selecione una sucursal</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-input2">
                            <label for="categoria">Categoría</label>
                            <div class="select-container">
                                <select name="categoria" id="categoria" class="enviar-input custom-select" required onchange="showProducts(this.value)">
                                    <option disabled selected>Selecione una categoría</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-input2">
                            <label for="producto">Producto</label>
                            <div class="select-container">
                                <select name="producto" id="producto" class="enviar-input custom-select" required disabled>
                                    <option disabled selected>Selecione un producto</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-input2">
                            <label for="tamano">Tamaño</label>
                            <div class="select-container">
                                <select name="tamano" id="tamano" class="enviar-input custom-select" required disabled>
                                    <option disabled selected>Selecione un tamaño</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="form-group">

                        <div class="form-input2">
                            <label for="precio">Precio ($) / caja </label>
                            <div class="number-container">
                                <input type="number" min="0" step="0.01" name="precio" id="precio" class="enviar-input number-input" title="Introduzca un número positivo" onchange="calculateTotal()" required>
                                <div class="number-button-container">
                                    <button type="button" class="number-button" onclick="upNumber('precio')">▲</button>
                                    <button type="button" class="number-button down-number" onclick="downNumber('precio')">▼</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-input2">
                            <label for="cantidad">Cantidad / caja</label>
                            <div class="number-container">
                                <input type="number" min="0" name="cantidad" id="cantidad" class="enviar-input number-input" title="Introduzca un número positivo" onchange="calculateTotal()" required>
                                <div class="number-button-container">
                                    <button type="button" class="number-button" onclick="upNumber('cantidad')">▲</button>
                                    <button type="button" class="number-button down-number" onclick="downNumber('cantidad')">▼</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-input2">
                            <label for="total" class="total-text">Total</label>
                            <input type="text" class="enviar-input disabled-total" id="total-money" readonly>
                        </div>
                    </div>
                    <div class="enviar-button-container">
                        <input type="text" value="0" name="notificacion" id="notificacion" hidden>
                        <input type="text" value="0" name="notificacionSuministro" id="notificacionSuministro" hidden>
                        <input type="text" value="0" name="notificacionSucursal" id="notificacionSucursal" hidden>
                        <input type="text" value="0" name="notificacionCentro" id="notificacionCentro" hidden>
                        <input type="submit" value="Enviar" class="enviar-button">
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    document.getElementsByClassName('cant-notifications')[0].innerHTML = <?=count($notificaciones) ?>;


    let links = document.getElementsByClassName('list-links');
    for (const item of links) {
        item.style.cssText = "background-color:#20373B; transform:scale(1)";
    }

    let sucursales = <?php echo json_encode($sucursales); ?>;
    let suministros = <?php echo json_encode($suministros); ?>;
    let notification = <?= $notification ?>;

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

    //SI SELECCIONA UNA NOTIFICACION
    if (notification) {
        console.log('hey');
        let notificacionInput = document.getElementById('notificacion');
        let notificacionSuministro = document.getElementById('notificacionSuministro');
        let notificacionSucursal = document.getElementById('notificacionSucursal');
        let notificationCentro = document.getElementById('notificacionCentro');


        let dataNotificacion = <?php echo json_encode($data) ?>;
        console.log(dataNotificacion);
        notificacionInput.value = dataNotificacion.id_notifica;
        notificacionSuministro.value = dataNotificacion.id_suministro;
        notificacionSucursal.value = dataNotificacion.ruc_sucursal;
        notificacionCentro.value = dataNotificacion.ruc_centro;

        //RELLENAR LOS SELECTS CON LOS DATOS DE LA NOTIFICACION
        let option = document.createElement('option');
        option.text = dataNotificacion.direccion;
        option.selected = true;
        sucursalSelect.appendChild(option);
        sucursalSelect.disabled = true;

        option = document.createElement('option');
        option.text = dataNotificacion.categoria;
        option.selected = true;
        categoriasSelect.appendChild(option);
        categoriasSelect.disabled = true;

        option = document.createElement('option');
        option.text = dataNotificacion.producto;
        option.selected = true;
        productosSelect.appendChild(option);

        option = document.createElement('option');
        option.text = dataNotificacion.tamano;
        option.selected = true;
        tamanosSelect.appendChild(option);

        document.getElementById('cantidad').value = dataNotificacion.cantidad;

    }



    // TRAE TODAS LAS CATEGORIAS UNICAS
    for (const item of suministros) {
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
    for (const item of sucursales) {
        let option = document.createElement('option');
        option.text = item.direccion;
        option.value = item.ruc_sucursal;
        sucursalSelect.appendChild(option);
    }

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

        for (const item of suministros) {
            if (item.id_categoria == value) {
                if (uniqueProducts.indexOf(item.producto) < 0) {
                    uniqueProducts.push(item.producto);
                    let productsObject = {
                        producto: item.producto,
                        productoID: item.id_producto
                    }
                    productos.push(productsObject);
                }

                if (uniqueTamanos.indexOf(item.tamano) < 0) {
                    uniqueTamanos.push(item.tamano);
                    let tamanosObject = {
                        tamano: item.tamano,
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


    }



    const upNumber = (id) => {
        let idElement = document.getElementById(id);
        let upNumber = idElement.value;
        if (upNumber == "") {
            upNumber = 0;
        }
        if (id == 'cantidad') {
            idElement.value = parseInt(upNumber) + 1;
        } else {
            idElement.value = (parseFloat(upNumber) + 0.01).toFixed(2);
        }
        calculateTotal();
    }

    const downNumber = (id) => {
        let idElement = document.getElementById(id);
        let downNumber = idElement.value;
        if (downNumber == "") {
            downNumber = 0;
        }
        if (id == 'cantidad') {
            idElement.value = parseInt(downNumber) - 1;
        } else {
            idElement.value = (parseFloat(downNumber) - 0.01).toFixed(2);
        }
        calculateTotal();
    }

    const calculateTotal = () => {
        console.log('hey');
        let total = document.getElementById('total-money');
        let cantidad = document.getElementById('cantidad').value;
        let precio = document.getElementById('precio').value;

        if (cantidad != "" && precio != "" && cantidad >= 0 && precio >= 0) {
            let formatter = new Intl.NumberFormat('en-US', {
                style: "currency",
                currency: 'USD'
            });

            total.value = formatter.format(parseInt(cantidad) * parseFloat(precio));
        }
    }
</script>