<?php
include '../../procesos/Conexion.php';
session_start();
$obj = new Conexion();
$notificaciones = $obj->GetNotificaciones();
if (isset($_SESSION['dataNotifications'])){
   // hace algo
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Pedidos</title>
    <link rel="stylesheet" href="../../css/administrador.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>
    <div class="dashboard-container">
        <?php require 'dashboard-nav.php' ?>
        <div class="dashboard-content">
            <?php require 'dashboard-header.php' ?>
        </div>
    </div>
</body>

</html>
<script>
    let links = document.getElementsByClassName('list-links');
    links.foreach(element => element.style.cssText = "background-color:#20373B; transform:scale(1);")
</script>