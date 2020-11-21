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
                                    <th>Sucursal</th>
                                    <th>Categoría</th>
                                    <th>Producto</th>
                                    <th>Tamaño</th>
                                    <th>Cantidad</th>
                                    <th>Precio/caja</th>
                                    <th>Fecha de Compra</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count($historial); $i++) : ?>
                                    <tr>
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
                <form action="../../procesos/dataPedido.php" method="POST" id="dataPedido">
                    <input id="dataNumber" type="text" name="dataNumber" value="" hidden>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    document.getElementsByClassName('cant-notifications')[0].innerHTML = <?= count($notificaciones) ?>;
    let links = document.getElementsByClassName('list-links');
    links[3].style.cssText = 'background-color:var(--form-color); transform:scale(1.1);'
</script>