<?php
session_start();
session_destroy();

header('Location: ../pantallas/administrador/login.php');