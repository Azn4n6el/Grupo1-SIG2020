<?php

include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['user-data'])){
    header('Location: login.php');
}

$obj = new Conexion();

$ruc_centro = $_SESSION['user-data']['ruc_centro'];
$notificaciones = $obj->GetNotificaciones($ruc_centro);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
    <link rel="stylesheet" href="../../css/administrador.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>
    <div class="dashboard-container">
        <?php require 'dashboard-nav.php' ?>
        <div class="dashboard-content">
            <?php require 'dashboard-header.php' ?>
            <div class="dashboard-body">
                <div class="body-title">
                    <h1>Notificaciones</h1>
                </div>
                <?php if (count($notificaciones) != 0) :?>
                <div class="responsive-table">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Sucursal</th>
                                <th>Categoría</th>
                                <th>Producto</th>
                                <th>Tamaño</th>
                                <th>Cantidad</th>
                                <th>Fecha del Pedido</th>
                                <th>Enviar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < count($notificaciones); $i++) : ?>
                                <tr>
                                    <td><?= $notificaciones[$i]['direccion'] ?></td>
                                    <td><?= $notificaciones[$i]['categoria'] ?></td>
                                    <td><?= $notificaciones[$i]['producto'] ?></td>
                                    <td><?= $notificaciones[$i]['tamano'] ?></td>
                                    <td><?= $notificaciones[$i]['cantidad'] ?> cajas</td>
                                    <td><?= date('d-m-Y', strtotime($notificaciones[$i]['fecha_pedido'])) ?></td>
                                    <td><a onclick="sendPedidos(<?= $i ?>)" class="table-send" title="Enviar Pedidos"><img src="../../images/noun_send_889264.svg" alt="EnviarProductos" width="50" height="45"></a></td>
                                </tr>
                            <?php endfor ?>

                        </tbody>
                    </table>
                </div>
                <?php else :?>
                    <h2>No tiene ninguna notificación en el momento</h2>
                <?php endif ?>
                <form action="../../procesos/dataPedido.php" method="POST" id="dataPedido">
                    <input id="dataNumber" type="text" name="dataNumber" value="" hidden>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    /* NAV NINGUNO SELECCIONADO */
    let links = document.getElementsByClassName('list-links');
    for (const item of links) {
        item.style.cssText = "background-color:#20373B; transform:scale(1)";
    }

    /* ENVIAR PEDIDO AL SERVIDOR */
    const sendPedidos = (numero) => {
        document.getElementById('dataNumber').value = numero;
        document.getElementById('dataPedido').submit();
    }
</script>