<?php
include '../../procesos/Conexion.php';
session_start();
if (!isset($_SESSION['sessionSucursal'])) {
    header('Location: elegirSucursal.php');
}
$ruc_sucursal = $_SESSION['sessionSucursal'];
$obj = new Conexion();
$suministros = $obj->GetInventarioBySucursal($ruc_sucursal);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../css/clientes.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>
    <!-- HEADER -->
    <?php require('header.php'); ?>
    <div class="main-container">
        <div class="sucursal-title">
            Sucursal <?= $suministros[0]['direccion'] ?>
        </div>
        <div class="carousel-container">
            <div id="currentSlide" class="slides">
                <img src="../../images/Bann1.png" alt="" srcset="" width="100%" height="100%">
            </div>
            <div class="slides">
                <img src="../../images/Bann9.png" alt="" srcset="" width="100%" height="100%">
            </div>
            <div class="slides">
                <img src="../../images/Bann12.png" alt="" srcset="" width="100%" height="100%">
            </div>
            <div class="slides">
                <img src="../../images/Bann2.png" alt="" srcset="" width="100%" height="100%">
            </div>
            <div class="carousel-button-container">
                <button type="button" class="carousel-button" onclick="prevSlide(this)"><img src="https://img.icons8.com/ios-filled/65/83C3A9/chevron-left.png" /></button>
                <button type="button" class="carousel-button" onclick="nextSlide(this)"><img src="https://img.icons8.com/fluent-systems-filled/65/83C3A9/chevron-right--v2.png" /></button>
            </div>
        </div>
        <div class="products-container">
            <?php for ($i = 0; $i < count($suministros); $i++) : ?>
                <div class="product">
                    <img src="https://d13lnhwm7sh4hi.cloudfront.net/wp-content/uploads/2020/03/20143131/4946406_coke-cola-origina-lata-12-oz-355-ml-011.jpg" alt="" class="product-img">
                    <div class="product-description">
                        <div class="product-label">
                            <?= $suministros[$i]['producto'] ?>
                        </div>
                        <div class="product-size">
                            <?= $suministros[$i]['tamaño'] ?>
                        </div>
                        <div class="product-price">
                            $<?= $suministros[$i]['precio'] ?> / caja
                        </div>
                        <div class="buy-container">
                            <button type="button" id="<?= $suministros[$i]['id_suministro'] ?>" onclick="agregarCarrito(this.id)">Añadir</button>
                        </div>
                    </div>
                </div>
            <?php endfor ?>
            <div class="product-button-container">
                <button type="button" class="carousel-button" onclick="prevProducts(this)"><img src="https://img.icons8.com/ios-filled/65/FCBD34/chevron-left.png" /></button>
                <button type="button" class="carousel-button" onclick="nextProducts(this)"><img src="https://img.icons8.com/fluent-systems-filled/65/FCBD34/chevron-right--v2.png" /></button>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php require('footer.php'); ?>

    <!-- MODAL -->
    <div class="custom-modal" id="custom-modal">
        <div class="modal-box">
            <div class="img-success">
                <img src="https://img.icons8.com/flat_round/100/000000/checkmark.png" id="msg-icon" />
            </div>
            <div class="modal-message">
                <h2 id="modal-msg">¡Reabastecido Satisfactoriamente!</h2>
            </div>
            <div class="modal-footer">
                <button type="button" class="ok-button" onclick="closeModal()">OK</button>
                <button type="button" class="no-button ok-button" onclick="location.href='../../procesos/reselectSucursal.php'">SI</button>
            </div>
        </div>
    </div>
</body>

</html>
<script src="../../js/globalFunctions.js"></script>
<script>
    //CANTIDAD DE COMPRAS
    let carritoProducts = window.localStorage.getItem('carrito');
    let compras = document.getElementsByClassName('cant-compras');
    if (carritoProducts != null && carritoProducts != undefined && carritoProducts != "") {
        carritoProducts = JSON.parse(window.localStorage.getItem('carrito'));
        compras[0].innerHTML = carritoProducts.length;
    } else {
        carritoProducts = [];
    }

    //CATALOGO DE PRODUCTOS
    let products = document.getElementsByClassName('product');
    for (let i = 5; i < products.length; i++) {
        products[i].style.display = 'none';
    }

    let pointer2 = 5;

    //CAROUSEL
    let slides = document.getElementsByClassName('slides');
    for (let i = 1; i < slides.length; i++) {
        slides[i].style.display = 'none';
    }
    let pointer = 0;
    let maxSlides = slides.length;


    /* ANIMACION CONTINUA CAROUSEL*/
    const bannerInterval = setInterval(() => {
        let current = document.getElementById('currentSlide');
        current.id = '';
        if (pointer < maxSlides - 1) {
            pointer++;
        } else {
            pointer = 0;
        }
        slides[pointer].style.zIndex = -1;
        slides[pointer].style.display = 'block';
        slides[pointer].id = "currentSlide";
        current.classList.add('nextSlide');

        setTimeout(function() {
            slides[pointer].style.zIndex = 0;
            current.style.display = 'none';
            current.style.zIndex = '0';
            current.classList.remove('nextSlide');
        }, 1000)
    }, 3000);

    /*ANIMACION EN EL CAROUSEL MANUAL*/
    const nextSlide = (element) => {
        clearInterval(bannerInterval);
        element.disabled = true;
        element.previousElementSibling.disabled = true;
        let current = document.getElementById('currentSlide');
        current.id = '';
        if (pointer < maxSlides - 1) {
            pointer++;
        } else {
            pointer = 0;
        }
        slides[pointer].style.zIndex = -1;
        slides[pointer].style.display = 'block';
        slides[pointer].id = "currentSlide";
        current.classList.add('nextSlide');
        setTimeout(function() {
            slides[pointer].style.zIndex = 0;
            current.style.display = 'none';
            current.style.zIndex = '0';
            current.classList.remove('nextSlide');
            element.disabled = false;
            element.previousElementSibling.disabled = false;
        }, 1000)
    }

    const prevSlide = (element) => {
        clearInterval(bannerInterval);
        element.disabled = true;
        element.nextElementSibling.disabled = true;
        let current = document.getElementById('currentSlide');
        current.id = '';
        if (pointer > 0) {
            pointer--;
        } else {
            pointer = maxSlides - 1;
        }
        slides[pointer].style.zIndex = -1;
        slides[pointer].style.display = 'block';
        slides[pointer].id = "currentSlide";
        current.classList.add("prevSlide");
        setTimeout(function() {
            slides[pointer].style.zIndex = 0;
            current.style.display = 'none';
            current.style.zIndex = '0';
            current.classList.remove('prevSlide');
            element.disabled = false;
            element.nextElementSibling.disabled = false;
        }, 1000)
    }


    /* ANIMACIONES PARA LOS PRODUCTOS*/
    const nextProducts = (element) => {
        for (let i = 0; i < products.length; i++) {
            products[i].style.display = 'none';
        }

        if (pointer2 < products.length) {
            pointer2 += 5;
        } else {
            pointer2 = 5;
        }

        for (let i = pointer2 - 5; i < pointer2; i++) {
            if (i < products.length) {
                products[i].style.display = 'flex';
            }

        }

    }

    const prevProducts = (element) => {
        for (let i = 0; i < products.length; i++) {
            products[i].style.display = 'none';
        }

        if (pointer2 > 5) {
            pointer2 -= 5;
            for (let i = pointer2 - 1; i > pointer2 - 6; i--) {
                products[i].style.display = 'flex';
            }
        } else {
            pointer2 = products.length;
            while (pointer2 % 5 != 0) {
                pointer2++;
            }
            pointer2 -= 5;

            for (let i = pointer2; i < products.length; i++) {
                products[i].style.display = 'flex';
            }
            pointer2 += 5;
        }
    }

</script>