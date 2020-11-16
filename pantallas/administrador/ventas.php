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
    <title>Ventas</title>
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
    links[1].style.cssText = 'background-color:var(--form-color); transform:scale(1.1);'
</script>