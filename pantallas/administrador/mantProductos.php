<?php

include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['user-data'])) {
    header('Location: login.php');
}

$obj = new Conexion();

$ruc_centro = $_SESSION['user-data']['ruc_centro'];
$notificaciones = $obj->GetNotificaciones($ruc_centro);
$suministros = $obj->GetSuministros();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento de Productos</title>
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
                    <h1>Mantenimiento de Productos</h1>
                </div>
                <div class="add-button-container">
                    <a href="#"><button class="type-button">Agregar Producto</button></a>
                </div>
                <div class="responsive-table responsive-table2">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Tamaño</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="table-data">
                            <?php for ($i = 0; $i < count($suministros); $i++) : ?>
                                <tr>
                                    <td><?= $suministros[$i]['id_suministro'] ?></td>
                                    <td><?= $suministros[$i]['producto'] ?></td>
                                    <td><?= $suministros[$i]['categoria'] ?></td>
                                    <td><?= $suministros[$i]['tamano'] ?></td>
                                    <td><a href="actualizarProducto.php?id_suministro=<?= $suministros[$i]['id_suministro'] ?>" class="edit-button" title="Editar"><img src="https://img.icons8.com/android/26/026f9e/edit.png" /></a>
                                        <a href="#" title="Eliminar" onclick="deleteProduct(<?= $suministros[$i]['id_suministro'] ?>)"><img src="https://img.icons8.com/metro/26/851800/trash.png" /></a></td>
                                </tr>
                            <?php endfor ?>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
<script>
    document.getElementsByClassName('cant-notifications')[0].innerHTML = <?= count($notificaciones) ?>;
    /* NAV NINGUNO SELECCIONADO */
    let links = document.getElementsByClassName('list-links');
    for (const item of links) {
        item.style.cssText = "background-color:#20373B; transform:scale(1)";
        item.style.cssText = "type-button:hover";
    }

    /* ENVIAR PEDIDO AL SERVIDOR */
    const sendPedidos = (numero) => {
        document.getElementById('dataNumber').value = numero;
        document.getElementById('dataPedido').submit();
    }

    const deleteProduct = (id) => {
        console.log(id);
    }
</script>