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

$id_suministro = $_GET['id_suministro'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Productos</title>
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
                    <h1>Actualización de Producto</h1>
                </div>
                <div class="add-button-container">
                    <a href="mantProductos.php"><button class="type-button">Regresar</button></a>
                </div>
                <form action="../../procesos/actualizarProducto.php" method="post" id="enviarProductosForm" class="productosForm">
                    <div class="form-group">
                        <div class="form-input2">
                            <label for="id">ID</label>
                            <input type="text" class="enviar-input disabled-total" name="id" id="id" value="<?= $id_suministro ?>" readonly>
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
                            <input type="text" class="enviar-input" name="producto" id="producto">
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
                    <div class="enviar-button-container">
                        <input type="submit" class="enviar-button" value="Actualizar">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
<?php require '../cliente/custom-modal.html' ?>

</html>
<script src="../../js/globalFunctions.js"></script>
<script>
    document.getElementsByClassName('cant-notifications')[0].innerHTML = <?= count($notificaciones) ?>;
    /* NAV NINGUNO SELECCIONADO */
    let links = document.getElementsByClassName('list-links');
    for (const item of links) {
        item.style.cssText = "background-color:#20373B; transform:scale(1)";
        item.style.cssText = "type-button:hover";
    }

    let message = <?= json_encode($mensaje); ?>;
    let suministros = <?php echo json_encode($suministros); ?>;
    let id_suministro = <?= $id_suministro ?>;

    let productoText = document.getElementById('producto');
    let categoriasSelect = document.getElementById('categoria');
    let tamanosSelect = document.getElementById('tamano');

    let categorias = [];
    let tamanos = [];

    let uniqueCategory = [];
    let uniqueTamanos = [];

    let tamanoSelected = 0;
    let categoriaSelected = 0;

    for (let i = 0; i < suministros.length; i++) {
        if (suministros[i]['id_suministro'] == id_suministro) {
            productoText.value = suministros[i]['producto'];
            categoriaSelected = suministros[i]['id_categoria'];
            tamanoSelected = suministros[i]['id_tamano'];
        }
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


    const showProducts = (value) => {

        //VACIAR LOS SELECTS
        tamanos = [];
        uniqueTamanos = [];

        tamanosSelect.innerText = '';

        for (const item of suministros) {
            if (item.id_categoria == value) {

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

        tamanosSelect.disabled = false;

        //LLENAR SELECTS
        for (let i = 0; i < tamanos.length; i++) {
            let option = document.createElement('option');
            option.text = tamanos[i].tamano;
            option.value = tamanos[i].tamanoID;
            tamanosSelect.appendChild(option);
        }
    }


    showProducts(categoriaSelected);
    categoriasSelect.value = categoriaSelected;
    tamanosSelect.value = tamanoSelected

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
</script>