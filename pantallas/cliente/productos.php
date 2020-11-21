<?php
include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['sessionSucursal'])) {
    header('Location: elegirSucursal.php');
}
if (!isset($_SESSION['categoriaSelected'])) {
    header('location: main.php');
}

$categoriaSelected = $_SESSION['categoriaSelected'];
$ruc_sucursal = $_SESSION['sessionSucursal'];

$categoriaProducts = [];
$uniqueTamanos = [];
$obj = new Conexion();
$suministros = $obj->GetInventarioBySucursal($ruc_sucursal);

for ($i = 0; $i < count($suministros); $i++) {
    if (strpos($suministros[$i]['categoria'], $categoriaSelected[0]) !== false) {
        $categoriaProducts[] = $suministros[$i];
    }
}

for ($i = 0; $i < count($categoriaProducts); $i++) {
    if (!in_array($categoriaProducts[$i]['tamaño'], $uniqueTamanos)) {
        $uniqueTamanos[] = $categoriaProducts[$i]['tamaño'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="../../css/clientes.css">

</head>

<body>
    <?php require('head.html'); ?>
    <div class="main-container">
        <nav class="categoria-container">
            <div class="categoria-icon">
                <img src="<?= $categoriaSelected[1] ?>" alt="" srcset="" width="80" height="80">
                <div class="categoria-title">
                    <span><?= $categoriaSelected[0] ?></span>
                </div>
            </div>
            <div class="search-bar">
                <input type="text" class="search" name="search" id="search" placeholder="Buscar por producto" onkeyup="searchProduct(this.value, 'producto')">
                <img class="search-icon" src="https://img.icons8.com/pastel-glyph/64/000000/search--v1.png" width="40" />
            </div>
            <div class="size-bar">
                <div class="select-container">
                    <select name="tamanos" id="tamanos" class="filter-select enviar-input custom-select" onchange="searchProduct(this.value,'tamaño')">
                        <option value="" disabled selected>Por Tamaño</option>
                        <option value="0">Todos</option>
                        <?php for ($i = 0; $i < count($uniqueTamanos); $i++) : ?>
                            <option value="<?= $uniqueTamanos[$i] ?>"><?= $uniqueTamanos[$i] ?></option>
                        <?php endfor ?>
                    </select>
                </div>
            </div>
        </nav>
        <section class="wrap-products products-container">
            <?php for ($i = 0; $i < count($categoriaProducts); $i++) : ?>
                <div class="category-product product">
                    <img src="https://d13lnhwm7sh4hi.cloudfront.net/wp-content/uploads/2020/03/20143131/4946406_coke-cola-origina-lata-12-oz-355-ml-011.jpg" alt="" class="product-img">
                    <div class="product-description">
                        <div class="product-label">
                            <?= $categoriaProducts[$i]['producto'] ?>
                        </div>
                        <div class="product-size">
                            <?= $categoriaProducts[$i]['tamaño'] ?>
                        </div>
                        <div class="product-price">
                            $<?= $categoriaProducts[$i]['precio'] ?> / caja
                        </div>
                        <div class="buy-container">
                            <button type="button" id="<?= $suministros[$i]['id_suministro'] ?>" onclick="agregarCarrito(this.id)">Añadir</button>
                        </div>
                    </div>
                </div>
            <?php endfor ?>
        </section>
    </div>
    <!-- Footer -->
    <?php require('footer.html'); ?>

</body>

<!-- MODAL -->
<?php require('custom-modal.html') ?>

</html>
<script src="../../js/globalFunctions.js"></script>
<script>
    //CANTIDAD DE PRODUCTOS EN EL CARRITO
    let carritoProducts = window.localStorage.getItem('carrito');
    let compras = document.getElementsByClassName('cant-compras');
    if (carritoProducts != null && carritoProducts != undefined && carritoProducts != "") {
        carritoProducts = JSON.parse(window.localStorage.getItem('carrito'));
        compras[0].innerHTML = carritoProducts.length;
    } else {
        carritoProducts = [];
    }

    let categoriaProducts = <?= json_encode($categoriaProducts) ?>;


    //BUSCAR PRODUCTOS
    const searchProduct = (value, type) => {
        let container = document.getElementsByClassName('products-container');
        container[0].innerHTML = '';
        if (value == '0'){
            value = '';
        }

        for (let i = 0; i < categoriaProducts.length; i++) {
            if (categoriaProducts[i][type].toUpperCase().indexOf(value.toUpperCase()) > -1) {
                container[0].insertAdjacentHTML('beforeend', 
                `<div class="category-product product">
                    <img src="https://d13lnhwm7sh4hi.cloudfront.net/wp-content/uploads/2020/03/20143131/4946406_coke-cola-origina-lata-12-oz-355-ml-011.jpg" alt="" class="product-img">
                    <div class="product-description">
                        <div class="product-label">
                            ${categoriaProducts[i].producto}
                        </div>
                        <div class="product-size">
                        ${categoriaProducts[i].tamaño}
                        </div>
                        <div class="product-price">
                        ${categoriaProducts[i].precio}
                        </div>
                        <div class="buy-container">
                            <button type="button" id="${categoriaProducts[i].id_suministro}" onclick="agregarCarrito(this.id)">Añadir</button>
                        </div>
                    </div>
                </div>`)
            }
        }


    }
</script>