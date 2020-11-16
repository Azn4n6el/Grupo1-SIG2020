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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas</title>
    <link rel="stylesheet" href="../../css/administrador.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>
    <div class="dashboard-container">
        <?php require 'dashboard-nav.php' ?>
        <div class="dashboard-content">
            <?php require 'dashboard-header.php' ?>
            <div class="dashboard-body">
                <div class="user-title">
                    <h1>Bienvenido, <?= $nombre ?></h1>
                </div>
                <div class="body-title">
                    <h1>Ventas</h1>
                </div>
                <div class="filtros-container">
                    <div class="filtros-title">
                        <h2>Filtros</h2>
                        <div class="filter-type">
                            <button type="button" class="type-button" id="general" onclick="showGeneral()">General</button>
                            <button type="button" class="type-button" id="porSucursal" onclick="showSucursal()">Por Sucursal</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-input3">
                            <label for="sucursal">Sucursal</label>
                            <div class="select-container">
                                <select name="sucursal" id="sucursal" class="enviar-input custom-select" required disabled onchange="validateType()">
                                </select>
                            </div>
                        </div>
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
                        <canvas id="ventas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<script src="../../js/chart_v2.9.4.js"></script>
<script src="../../js/globalFunctions.js"></script>
<script>
    //SELECCIONADO EN EL NAVBAR LA PANTALLA CORRESPONDIENTE
    let links = document.getElementsByClassName('list-links');

    links[1].style.cssText = 'background-color:var(--form-color); transform:scale(1.1);';

    let sucursales = <?php echo json_encode($sucursales); ?>;
    let suministros = <?php echo json_encode($suministros); ?>;
    let reabastece = <?php echo json_encode($reabastece); ?>;

    let sucursalSelect = document.getElementById('sucursal');
    let categoriasSelect = document.getElementById('categoria');
    let tamanosSelect = document.getElementById('tamano');
    let ventasChart = document.getElementById('ventas');


    let categorias = [];
    let tamanos = [];
    let productos = [];

    let uniqueCategory = [];
    let uniqueTamanos = [];
    let uniqueProducts = [];

    let isAgua = false;

    //FILTRO SELECCIONADO
    let general = document.getElementById('general');
    let porSucursal = document.getElementById('porSucursal');

    general.style.cssText = 'background-color: #fcd06f;transform:scale(1.1)';



    //INICIALIZAR GRÁFICA
    let chart = createChart(ventasChart, 0, 0, 'Ventas de la Empresa','Ganancias', 'Productos', 'Ventas ($)');

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
        porSucursal.style.cssText = 'background-color: var(--button-color);transform:scale(1.0)';
        sucursalSelect.disabled = true;
        let suministrosName = [];
        let ventas = [];
  
        if (isAgua) {
            for (const item of tamanos) {
                suministrosName.push(item.tamano);
            }
    
            if (suministrosName.length > 0) {
                for (let i = 0; i < suministrosName.length; i++) {
                    let ganancias= 0;
                    for (const item of reabastece) {
                        if (item.producto == 'Agua' && item.tamano == suministrosName[i]) {
                            ganancias = ganancias + (item.precio * item.cantidad)
                        }
                    }
                    ventas.push(ganancias);
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
                    let ganancias = 0;
                    for (const item of reabastece) {
                        if (item.producto == suministrosName[i] && item.id_tamano == tamanosSelect.value) {
                            ganancias = ganancias + (item.precio * item.cantidad)
                        }
                    }
                    ventas.push(ganancias);
                }
            }
        }


        updateChart(chart, suministrosName, ventas);
    }

    const showSucursal = () => {
        porSucursal.style.cssText = 'background-color: var(--form-color);transform:scale(1.1)';
        general.style.cssText = 'background-color: var(--button-color);transform:scale(1.0)';
        sucursalSelect.disabled = false;
        let suministrosName = [];
        let ventas = [];

        //SI LA CATEGORIA ES AGUA
        if (isAgua) {

            //GUARDAR LOS NOMBRES PARA EL AXIS X
            for (const item of tamanos) {
                suministrosName.push(item.tamano);
            }

            //SUMA DE CANTIDADES
            if (suministrosName.length > 0) {
                for (let i = 0; i < suministrosName.length; i++) {
                    let ganancias = 0;
                    for (const item of reabastece) {
                        if (item.producto == 'Agua' && item.tamano == suministrosName[i] && item.ruc_sucursal == sucursalSelect.value) {
                            ganancias = ganancias + (item.precio * item.cantidad)
                        }
                    }
                    ventas.push(ganancias);
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
                    let ganancias = 0;
                    for (const item of reabastece) {
                        if (item.producto == suministrosName[i] && item.id_tamano == tamanosSelect.value && item.ruc_sucursal == sucursalSelect.value) {
                            ganancias = ganancias + (item.precio * item.cantidad)
                        }
                    }
                    ventas.push(ganancias);
                }
            }
        }

        updateChart(chart, suministrosName, ventas);

    }

    const validateType = () => {
        if (sucursalSelect.disabled == true){
            showGeneral();
        } else {
            showSucursal();
        }
    }

    showProducts(categorias[0].categoriaID, 'Sodas');
</script>