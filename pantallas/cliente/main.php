<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../css/clientes.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    <!-- scripts-->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- HEADER -->
    <?php require('header.php');?>
    <!--CAROUSEL 1 -->
    <br>
    <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-ride="carousel" data-interval="5000">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
     <div class="carousel-inner">
        <div class="carousel-item active">
            <img class="d-block w-100" src="../../images/bann11.png" alt="First slide"width="60px">
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="../../images/Bann12.png" alt="Second slide" width="60px">
        </div>
        <div class="carousel-item">
            <img class="d-block w-100" src="../../images/Bann9.png" alt="Third slide"width="60px">
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
     </a>
</div>

<!--Product CARD -->
<br>

<div class="container">
    <h2 class="text-center font-weight-light">PRODUCTOS</h2>
    <br>
        <div class="row">
            <div class="col">
                <div class="card" style="width: 18rem;">
                    <img src="../../images/cebion.png" class="card-img-top" alt="...">
                    <div class="card-body">
                    <p class="text-center card-text">Vitamina C</p>
                 </div>
            </div>
        </div>
             <div class="col">
                <div class="card" style="width: 18rem;">
                    <img src="../../images/welchito.png" class="card-img-top" alt="..." >
                        <div class="card-body">
                             <p class="text-center card-text">Jugo de Uva</p>
                        </div>
                </div>
             </div>
            <div class="col">
                 <div class="card" style="width: 18rem;">
                    <img src="../../images/cherry.png" class="card-img-top" alt="..." >
                        <div class="card-body">
                             <p class="text-center card-text">Soda de cereza </p>
                         </div>
                </div>
            </div>
         
    </div>    
</div>
   	<!-- Footer -->
<footer class="page-footer font-small teal pt-4">
   <br> 
  <div class="footer-copyright text-center py-3">© 2020 SuperInstant1
  </div>
</footer>
<!-- Footer -->        
</body>
</html>


