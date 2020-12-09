<?php

include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['user-data'])) {
    header('Location: login.php');
}

$obj = new Conexion();
if (isset($_SESSION['message'])) {
    $mensaje = $_SESSION['message'];
    unset($_SESSION['message']);
} else {
    $mensaje = '';
}


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
                    <a href="agregarProductos.php"><button class="type-button">Agregar Producto</button></a>
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
                                        <a href="#" title="Eliminar" onclick="deleteProduct(<?= $suministros[$i]['id_producto'] ?>,'<?= $suministros[$i]['producto'] ?>')"><img src="https://img.icons8.com/metro/26/851800/trash.png" /></a></td>
                                </tr>
                            <?php endfor ?>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</body>
<?php require '../cliente/custom-modal.html' ?>

</html>
<script src="../../js/globalFunctions.js"></script>
<script>
    let message = <?= json_encode($mensaje) ?>

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

    const deleteProduct = (id, nombre) => {
        let modal = document.getElementById('custom-modal');
        let msg = document.getElementById('modal-msg');
        let noButton = document.getElementsByClassName('ok-button');

        document.getElementById('msg-icon').src = "https://img.icons8.com/officel/100/000000/high-risk.png";
        msg.innerHTML = '¿Estas seguro que quieres eliminar el producto ' + nombre + '?';

        noButton[0].style.display = 'inline-block';
        noButton[0].innerHTML = 'NO';

        noButton[2].style.display = 'inline-block';
        noButton[2].innerHTML = 'SI';
        noButton[2].onclick = function(){location.href='../../procesos/eliminarProducto.php?id_producto=' + id}
        console.log(noButton[2]);
        modal.style.display = 'block';
    }
</script>