<?php
session_start();

unset($_SESSION['dataNotifications']);
header('location: ../pantallas/administrador/enviarPedidos.php');