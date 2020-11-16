<header class="dashboard-header">
    <div class="header-left">
        <a href="clientes.php" class="dashboard-logo"><img src="../../images/super instant1.png" alt="logoEmpresa" width="175"></a>
        <h1 class="dashboard-title">Dashboard</h1>
    </div>
    <div class="header-right">
        <a class="links-image" href="notificaciones.php" title="Ver Notificaciones"><img src="../../images/noun_notification_1594275.svg" alt="Notificaciones" width="80" height="80">
            <div class="cant-notifications"><?= count($notificaciones) ?></div>
        </a>
        <a class="links-image" href="../../procesos/unsetNotification.php" title="Enviar Pedidos"><img src="../../images/noun_send_889264.svg" alt="EnviarProductos" width="80" height="65"></a>
    </div>
</header>