<?php
session_start();
session_destroy();
header('Location: ../pantallas/cliente/elegirSucursal.php');