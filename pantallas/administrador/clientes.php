<?php
include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['user-data'])) {
    header('Location: login.php');
}

$obj = new Conexion();
$ruc_centro = $_SESSION['user-data']['ruc_centro'];
$nombre = $_SESSION['user-data']['usuario'];

$notificaciones = $obj->GetNotificaciones($ruc_centro);
$sucursales = $obj->GetSucursales($ruc_centro);
$reabastece = $obj->GetReabastece($ruc_centro);
$suministros = $obj->GetSuministros();



if (isset($_SESSION['message'])) {
    $mensaje = $_SESSION['message'];
    unset($_SESSION['message']);
} else {
    $mensaje = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link rel="stylesheet" href="../../css/administrador.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>
    <div class="dashboard-container">
        <?php require 'dashboard-nav.html' ?>
        <div class="dashboard-content">
            <?php require 'dashboard-header.html' ?>
            <div class="dashboard-body">
                <div class="user-title">
                    <h1>Bienvenido, <?= $nombre ?></h1>
                </div>
                <div class="body-title">
                    <h1>Clientes</h1>
                </div>
                <div class="filtros-container">
                    <div class="filtros-title">
                        <h2>Filtros</h2>
                        <div class="filter-type">
                            <button type="button" class="type-button" id="general" onclick="showGeneral()">General</button>
                            <button type="button" class="type-button" id="porSucursal" onclick="showSucursal()">Por Sucursal</button>
                            <button type="button" class="type-button" id="porProvincia" onclick="showProvincia()">Por Provincia</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-input3">
                            <label for="provincia">Provincia</label>
                            <div class="select-container">
                                <select name="provincia" id="provincia" class="enviar-input custom-select" required disabled onchange="validateType()">
                                </select>
                            </div>
                        </div>
                        <div class="form-input3">
                            <label for="sucursal">Sucursal</label>
                            <div class="select-container">
                                <select name="sucursal" id="sucursal" class="enviar-input custom-select" required disabled onchange="validateType()">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-input3">
                            <label for="categoria">Categoría</label>
                            <div class="select-container">
                                <select name="categoria" id="categoria" class="enviar-input custom-select" onchange="showProducts(this.value, this.options[this.selectedIndex].text)">
                                </select>
                            </div>
                        </div>
                        <div class="form-input3">
                            <label for="tamano">Tamaño</label>
                            <div class="select-container">
                                <select name="tamano" id="tamano" class="enviar-input custom-select" onchange="validateType()">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="compras"></canvas>
                    </div>
                    <div class="responsive-table">
                        <table class="custom-table">
                            <thead>
                                <tr id="table-heading">
                                    <th>Sucursal</th>
                                    <th>Vitaminas D</th>
                                    <th>Vitaminas C</th>
                                    <th>Vitaminas Zinc</th>
                                    <th>Vitaminas Magnesio</th>
                                    <th>Vitaminas Complejo B</th>
                                </tr>
                            </thead>
                            <tbody id="table-data">
                                <tr>
                                    <td>Condado</td>
                                    <td>57</td>
                                    <td>56</td>
                                    <td>22</td>
                                    <td>33</td>
                                    <td>22</td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td>57</td>
                                    <td>56</td>
                                    <td>22</td>
                                    <td>33</td>
                                    <td>22</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php require '../cliente/custom-modal.html' ?>

</html>
<script src="../../js/chart_v2.9.4.js"></script>
<script src="../../js/globalFunctions.js"></script>
<script>
    document.getElementsByClassName('cant-notifications')[0].innerHTML = <?= count($notificaciones) ?>;
    //SELECCIONADO EN EL NAVBAR LA PANTALLA CORRESPONDIENTE
    let links = document.getElementsByClassName('list-links');
    let message = <?php echo json_encode($mensaje) ?>;
    links[0].style.cssText = 'background-color:var(--form-color); transform:scale(1.1);';

    let sucursales = <?php echo json_encode($sucursales); ?>;
    let suministros = <?php echo json_encode($suministros); ?>;
    let reabastece = <?php echo json_encode($reabastece); ?>;

    let sucursalSelect = document.getElementById('sucursal');
    let categoriasSelect = document.getElementById('categoria');
    let tamanosSelect = document.getElementById('tamano');
    let comprasChart = document.getElementById('compras');
    let provinciasSelect = document.getElementById('provincia');


    let categorias = [];
    let tamanos = [];
    let productos = [];
    let provincias = [];

    let uniqueCategory = [];
    let uniqueTamanos = [];
    let uniqueProducts = [];
    let uniqueCountry = [];

    let isAgua = false;

    //FILTRO SELECCIONADO
    let general = document.getElementById('general');
    let porSucursal = document.getElementById('porSucursal');
    let porProvincia = document.getElementById('porProvincia');

    general.style.cssText = 'background-color: #fcd06f;transform:scale(1.1)';



    //INICIALIZAR GRÁFICA
    let chart = createChart(comprasChart, 0, 0, 'Productos Más Comprados', 'Cajas Compradas', 'Productos', 'Cajas');

    //LLENAR LOS SELECTS
    for (const item of sucursales) {
        let option = document.createElement('option');
        option.text = item.direccion;
        option.value = item.ruc_sucursal;
        sucursalSelect.appendChild(option);
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

    for (let i = 0; i < categorias.length; i++) {
        let option = document.createElement('option');
        option.text = categorias[i].categoria;
        option.value = categorias[i].categoriaID;
        categoriasSelect.appendChild(option);
    }

    // TRAE TODAS LAS PROVINCIAS EXISTENTES
    for (const item of sucursales) {
        if (uniqueCountry.indexOf(item.id_provincia) < 0) {
            uniqueCountry.push(item.id_provincia);
            let countryObject = {
                provincia: item.provincia,
                provinciaID: item.id_provincia
            }
            provincias.push(countryObject);
        }
    }

    for (let i = 0; i < provincias.length; i++) {
        let option = document.createElement('option');
        option.text = provincias[i].provincia
        option.value = provincias[i].provinciaID
        provinciasSelect.appendChild(option);
    }


    const showProducts = (value, text) => {
        //VACIAR LOS SELECTS
        productos = [];
        tamanos = [];
        uniqueProducts = [];
        uniqueTamanos = [];

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

        if (text != 'Agua Embotellada') {
            isAgua = false;
            tamanosSelect.disabled = false;

            //LLENAR SELECTS
            for (let i = 0; i < tamanos.length; i++) {
                let option = document.createElement('option');
                option.text = tamanos[i].tamano;
                option.value = tamanos[i].tamanoID;
                tamanosSelect.appendChild(option);
            }
        } else {
            tamanosSelect.disabled = true;
            isAgua = true;
        }

        validateType();
    }


    const showGeneral = () => {
        general.style.cssText = 'background-color: var(--form-color);transform:scale(1.1)';
        porSucursal.style.cssText = 'background-color: var(--button-color);transform:scale(1.0);';
        porSucursal.style.cssText = "type-button:hover";
        porProvincia.style.cssText = 'background-color: var(--button-color);transform:scale(1.0);';
        porProvincia.style.cssText = "type-button:hover";

        sucursalSelect.disabled = true;
        provinciasSelect.disabled = true;
        let parent = document.getElementsByClassName("filtros-container");
        let suministrosName = [];
        let compras = [];

        if (isAgua) {
            for (const item of tamanos) {
                suministrosName.push(item.tamano);
            }

            if (suministrosName.length > 0) {
                for (let i = 0; i < suministrosName.length; i++) {
                    let cantCompra = 0;
                    for (const item of reabastece) {
                        if (item.producto == 'Agua' && item.tamano == suministrosName[i]) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    }
                    compras.push(cantCompra);
                }
            }

        } else {
            //ENCONTRAR EL ID SUMINISTRO
            for (const item of suministros) {
                if (item.id_categoria == categoriasSelect.value && item.id_tamano == tamanosSelect.value) {
                    suministrosName.push(item.producto);
                }
            }

            if (suministrosName.length > 0) {
                for (let i = 0; i < suministrosName.length; i++) {
                    let cantCompra = 0;
                    for (const item of reabastece) {
                        if (item.producto == suministrosName[i] && item.id_tamano == tamanosSelect.value) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    }
                    compras.push(cantCompra);
                }
            }


        }

        updateChart(chart, suministrosName, compras);
        updateReport(suministrosName);
    }

    const showSucursal = () => {
        porSucursal.style.cssText = 'background-color: var(--form-color);transform:scale(1.1)';
        general.style.cssText = 'background-color: var(--button-color);transform:scale(1.0)';
        general.style.cssText = "type-button:hover";
        porProvincia.style.cssText = 'background-color: var(--button-color);transform:scale(1.0);';
        porProvincia.style.cssText = "type-button:hover";

        sucursalSelect.disabled = false;
        provinciasSelect.disabled = true;
        let suministrosName = [];
        let compras = [];

        //SI LA CATEGORIA ES AGUA
        if (isAgua) {

            //GUARDAR LOS NOMBRES PARA EL AXIS X
            for (const item of tamanos) {
                suministrosName.push(item.tamano);
            }

            //SUMA DE CANTIDADES
            if (suministrosName.length > 0) {
                for (let i = 0; i < suministrosName.length; i++) {
                    let cantCompra = 0;
                    for (const item of reabastece) {
                        if (item.producto == 'Agua' && item.tamano == suministrosName[i] && item.ruc_sucursal == sucursalSelect.value) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    }
                    compras.push(cantCompra);
                }
            }

        } else {
            //GUARDAR LOS NOMBRES PARA EL AXIS X
            for (const item of suministros) {
                if (item.id_categoria == categoriasSelect.value && item.id_tamano == tamanosSelect.value) {
                    suministrosName.push(item.producto);
                }
            }

            //SUMA DE CANTIDADES
            if (suministrosName.length > 0) {
                for (let i = 0; i < suministrosName.length; i++) {
                    let cantCompra = 0;
                    for (const item of reabastece) {
                        if (item.producto == suministrosName[i] && item.id_tamano == tamanosSelect.value && item.ruc_sucursal == sucursalSelect.value) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    }
                    compras.push(cantCompra);
                }
            }
        }

        updateChart(chart, suministrosName, compras);
        updateReport(suministrosName);
    }

    const showProvincia = () => {
        porProvincia.style.cssText = 'background-color: var(--form-color);transform:scale(1.1)';
        general.style.cssText = 'background-color: var(--button-color);transform:scale(1.0)';
        general.style.cssText = "type-button:hover";
        porSucursal.style.cssText = 'background-color: var(--button-color);transform:scale(1.0);';
        porSucursal.style.cssText = "type-button:hover";

        sucursalSelect.disabled = true;
        provinciasSelect.disabled = false;

        let suministrosName = [];
        let compras = [];

        //SI LA CATEGORIA ES AGUA
        if (isAgua) {

            //GUARDAR LOS NOMBRES PARA EL AXIS X
            for (const item of tamanos) {
                suministrosName.push(item.tamano);
            }

            //SUMA DE CANTIDADES
            if (suministrosName.length > 0) {
                for (let i = 0; i < suministrosName.length; i++) {
                    let cantCompra = 0;
                    for (const item of reabastece) {
                        if (item.producto == 'Agua' && item.tamano == suministrosName[i] && item.id_provincia == provinciasSelect.value) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    }
                    compras.push(cantCompra);
                }
            }

        } else {
            //GUARDAR LOS NOMBRES PARA EL AXIS X
            for (const item of suministros) {
                if (item.id_categoria == categoriasSelect.value && item.id_tamano == tamanosSelect.value) {
                    suministrosName.push(item.producto);
                }
            }

            //SUMA DE CANTIDADES
            if (suministrosName.length > 0) {
                for (let i = 0; i < suministrosName.length; i++) {
                    let cantCompra = 0;
                    for (const item of reabastece) {
                        if (item.producto == suministrosName[i] && item.id_tamano == tamanosSelect.value && item.id_provincia == provinciasSelect.value) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    }
                    compras.push(cantCompra);
                }
            }
        }

        updateChart(chart, suministrosName, compras);
        updateReport(suministrosName);

    }

    const updateReport = (suministrosName) => {
        /*RELLENAR REPORTE TEXTUAL*/
        let titulos = document.getElementById('table-heading');
        let data = document.getElementById('table-data');
        let compras = [];
        /*Limpiar Datos*/
        titulos.innerHTML = '';
        data.innerHTML = '';

        if (provinciasSelect.disabled) {
            titulos.insertAdjacentHTML("beforeend", `<th>Sucursal</th>`)
            titulos.insertAdjacentHTML("beforeend", `<th>Super</th>`)
            titulos.insertAdjacentHTML("beforeend", `<th>Provincia</th>`)
        } else {
            titulos.insertAdjacentHTML("beforeend", `<th>Provincia</th>`)
        }


        for (let i = 0; i < suministrosName.length; i++) {
            titulos.insertAdjacentHTML("beforeend", `<th>${suministrosName[i]}</th>`)
        }


        if (provinciasSelect.disabled) {
            /*LLENANDO LA TABLA*/
            for (let i = 0; i < sucursales.length; i++) {
                data.insertAdjacentHTML("beforeend", `<tr><td>${sucursales[i]['direccion']}</td></tr>`)
                data.children[i].insertAdjacentHTML("beforeend", `<td>${sucursales[i]['nombre']}</td>`)
                data.children[i].insertAdjacentHTML("beforeend", `<td>${sucursales[i]['provincia']}</td>`)
                for (let j = 0; j < suministrosName.length; j++) {
                    let cantCompra = 0;
                    for (const item of reabastece) {
                        if (isAgua) {
                            if (item.producto == "Agua" && item.tamano == suministrosName[j] && item.ruc_sucursal == sucursales[i]['ruc_sucursal']) {
                                cantCompra = cantCompra + parseInt(item.cantidad);
                            }
                        } else {
                            if (item.producto == suministrosName[j] && item.id_tamano == tamanosSelect.value && item.ruc_sucursal == sucursales[i]['ruc_sucursal']) {
                                cantCompra = cantCompra + parseInt(item.cantidad);
                            }
                        }
                    }
                    data.children[i].insertAdjacentHTML("beforeend", `<td>${cantCompra}</td>`)
                }
            }

            let childCount = data.childElementCount;
            data.insertAdjacentHTML("beforeend", `<tr><td colspan=3><strong>Total:</strong></td></tr>`)
            for (let j = 0; j < suministrosName.length; j++) {
                let cantCompra = 0;
                for (const item of reabastece) {
                    if (isAgua) {
                        if (item.producto == "Agua" && item.tamano == suministrosName[j]) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    } else {
                        if (item.producto == suministrosName[j] && item.id_tamano == tamanosSelect.value) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    }

                }
                data.children[childCount].insertAdjacentHTML("beforeend", `<td><strong>${cantCompra}</strong></td>`)
            }
        } else {
            /* POR PROVINCIA */
            for (let i = 0; i < provincias.length; i++) {
                data.insertAdjacentHTML("beforeend", `<tr><td>${sucursales[i]['provincia']}</td></tr>`)

                for (let j = 0; j < suministrosName.length; j++) {
                    let cantCompra = 0;
                    for (const item of reabastece) {
                        if (isAgua) {
                            if (item.producto == "Agua" && item.tamano == suministrosName[j] && item.id_provincia == provincias[i].provinciaID) {
                                cantCompra = cantCompra + parseInt(item.cantidad);
                            }
                        } else {
                            if (item.producto == suministrosName[j] && item.id_tamano == tamanosSelect.value && item.id_provincia == provincias[i].provinciaID) {
                                cantCompra = cantCompra + parseInt(item.cantidad);
                            }
                        }
                    }
                    data.children[i].insertAdjacentHTML("beforeend", `<td>${cantCompra}</td>`)
                }

            }

            let childCount = data.childElementCount;
            data.insertAdjacentHTML("beforeend", `<tr><td><strong>Total:</strong></td></tr>`)
            for (let j = 0; j < suministrosName.length; j++) {
                let cantCompra = 0;
                for (const item of reabastece) {
                    if (isAgua) {
                        if (item.producto == "Agua" && item.tamano == suministrosName[j]) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    } else {
                        if (item.producto == suministrosName[j] && item.id_tamano == tamanosSelect.value) {
                            cantCompra = cantCompra + parseInt(item.cantidad);
                        }
                    }
                }
                data.children[childCount].insertAdjacentHTML("beforeend", `<td><strong>${cantCompra}</strong></td>`)
            }

        }


    }


    const validateType = () => {
        if (sucursalSelect.disabled && provinciasSelect.disabled) {
            showGeneral();
        } else if (!sucursalSelect.disabled) {
            showSucursal();
        } else if (!provinciasSelect.disabled) {
            showProvincia();
        }
    }

    //MOSTRAR MENSAJE
    if (message != '') {
        let modal = document.getElementById('custom-modal');
        let msg = document.getElementById('modal-msg');
        document.getElementById('msg-icon').src = "https://img.icons8.com/flat_round/100/000000/checkmark.png";
        if (message.indexOf('Error') >= 0) {
            document.getElementById('msg-icon').src = "https://img.icons8.com/officel/100/000000/high-risk.png";
        }
        msg.innerHTML = message;
        modal.style.display = 'block';
    }


    showProducts(categorias[0].categoriaID, 'Sodas');
</script>