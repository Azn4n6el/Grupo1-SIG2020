<?php
include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['user-data'])) {
    header('Location: login.php');
}

$obj = new Conexion();
$ruc_centro = $_SESSION['user-data']['ruc_centro'];
$notificaciones = $obj->GetNotificaciones($ruc_centro);
$historial = $obj->GetHistorial($ruc_centro);
$productosComprados = $obj->GetProductosMasComprados($ruc_centro);
$sucursalesComprados = $obj->GetSucursalesMasCompras($ruc_centro);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Compra</title>
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
                    <h1>Historial de Compras</h1>
                </div>
                <?php if (count($historial) != 0) : ?>
                    <div class="responsive-table">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Provincia</th>
                                    <th>Super</th>
                                    <th>Sucursal</th>
                                    <th>Categoría</th>
                                    <th>Producto</th>
                                    <th>Tamaño</th>
                                    <th>Cantidad</th>
                                    <th>Precio/caja</th>
                                    <th>Fecha de Compra</th>
                                </tr>
                            </thead>
                            <tbody id="table-data">
                                <?php for ($i = 0; $i < count($historial); $i++) : ?>
                                    <tr>
                                        <td><?= $historial[$i]['provincia'] ?></td>
                                        <td><?= $historial[$i]['super'] ?></td>
                                        <td><?= $historial[$i]['sucursal'] ?></td>
                                        <td><?= $historial[$i]['categoria'] ?></td>
                                        <td><?= $historial[$i]['producto'] ?></td>
                                        <td><?= $historial[$i]['tamano'] ?></td>
                                        <td><?= $historial[$i]['cantidad'] ?> cajas</td>
                                        <td>$<?= $historial[$i]['precio'] ?></td>
                                        <td><?= date('d-m-Y', strtotime($historial[$i]['fecha'])) ?></td>
                                    </tr>
                                <?php endfor ?>

                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <h2>No se ha realizado ninguna compra en el momento.</h2>
                <?php endif ?>
                <div class="charts-pie-container">
                    <div class="chartPie-container">
                        <canvas id="ProductosMas"></canvas>
                    </div>
                    <div class="chartPie-container">
                        <canvas id="SucursalesMas"></canvas>
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
    document.getElementsByClassName('cant-notifications')[0].innerHTML = <?= count($notificaciones) ?>;
    let links = document.getElementsByClassName('list-links');
    links[3].style.cssText = 'background-color:var(--form-color); transform:scale(1.1);'

    let productosMas = document.getElementById('ProductosMas');
    let sucursalChart = document.getElementById('SucursalesMas');


    //DATOS PARA LA GRAFICA
    let productos = [],sucursales = [], dataProduct = [], labelProduct = [], dataSucursal = [], labelSucursal = [];
    productos = <?= json_encode($productosComprados)?>;
    sucursales = <?= json_encode($sucursalesComprados) ?>;
                    console.log(productos);
    for (let i = 0; i < productos.length; i++){
        dataProduct.push(productos[i].cantidad);
        labelProduct.push(productos[i].producto + ', ' + productos[i].tamano);
        if (i == 4) {
            break;
        }
    }

    console.log(sucursales);

    for (let i = 0; i < sucursales.length; i++){
        dataSucursal.push(sucursales[i].gastos);
        labelSucursal.push(sucursales[i].super + ', ' + sucursales[i].sucursal);
        if (i == 4) {
            break;
        }
    }
    

    //GRAFICAS
    let myChart = new Chart(productosMas, {
        type: 'pie',
        data: {
            datasets: [{
                data: dataProduct,
                backgroundColor: ['rgba(218, 186, 255,0.8)', 'rgba(186, 219, 255,0.8)', 'rgba(250, 156, 127, 0.8)','rgba(235, 255, 191,0.8)','rgba(115, 148, 145, 0.8)'],
                borderColor: ['#dabaff', '#badbff', '#fa9c7f','#ebffbf','#739491'],
                borderWidth:4
            }],
            labels: labelProduct
        },
        options: {
            title: {
                display: true,
                fontSize: 24,
                fontColor: ' #f1471d',
                fontFamily: 'Barlow, sans-serif',
                text: 'Productos Más Vendidos (Cajas)'
            },
            legend: {
                display: true,
                onClick: (e) => e.stopPropagation,
            },
        }
    })

    var stackedBar = new Chart(sucursalChart, {
        type: 'horizontalBar',
        data: {
            labels: labelSucursal,
            datasets: [{
                label: ['Ventas $'],
                data: dataSucursal,
                backgroundColor: ['rgba(131, 195, 169, 0.3)', 'rgba(128, 176, 255, 0.3)','rgba(219, 153, 255, 0.3)', 'rgba(194, 255, 153,0.3)', 'rgba(255, 196, 153, 0.3)'],
                borderColor: ['#83c3a9', '#80b0ff','#db99ff', '#c2ff99', '#ffc499'],
                borderWidth: 2
            }]
        },
        options: {
            title: {
                display: true,
                fontSize: 24,
                fontColor: ' #f1471d',
                fontFamily: 'Barlow, sans-serif',
                text: 'Clientes Más Frecuentes'
            },
            legend: {
                display: false,
                onClick: (e) => e.stopPropagation,
     
            },
            scales: {
                xAxes: [{
                    stacked:true,

                }],
                yAxes: [{
                    stacked:true,
                }]
            }
        }
    });
</script>