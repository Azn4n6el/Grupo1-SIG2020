<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasboard</title>
    <link rel="stylesheet" href="../../css/administrador.css">
    <link rel="stylesheet" href="../../css/styles.css">
   

</head>

<body>
    <div class="dashboard-container">
        <nav class="dashboard-nav">
            <div class="nav-title">
                <h1>Reportes</h1>
            </div>
            <ul>
                <a href="clientes.php" class="list-links">
                    <li>Clientes</li>
                </a>
                <a href="ventas.php" class="list-links">
                    <li>Ventas</li>
                </a>
                <a href="inventario.php" class="list-links">
                    <li>Inventario</li>
                </a>
                <a href="historial.php" class="list-links">
                    <li>Historial</li>
                </a>
            </ul>
            <a href="login.php" class="list-links logout">Cerrar Sesión</a>
        </nav>
        <header class="dashboard-header">
            <div class="header-left">
                <a href="clientes.php" class="dashboard-logo"><img src="../../images/super instant1.png" alt="logoEmpresa" width="175"></a>
                <h1 class="dashboard-title">Dashboard</h1>
            </div>
            <div class="header-right">
                <a class="links-image" href="notificaciones.php"><img src="../../images/noun_notification_1594275.svg" alt="Notificaciones" width="80" height="80"></a>
                <a class="links-image" href="enviarPedidos.php"><img src="../../images/noun_send_889264.svg" alt="EnviarProductos" width="80" height="65"></a>
            </div>
        </header>
    </div>
</body>

</html>