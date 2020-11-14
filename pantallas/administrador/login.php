<?php 
session_start();
if (isset($_SESSION['message'])){
    $error = $_SESSION['message'];
    unset($_SESSION['message']);
} else {
    $error = '';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../css/administrador.css">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body class="login-body-background">
    <div class="login-header">
        <div class="images-container">
            <img src="../../images/mujercompra.png" alt="" srcset="">
            <img src="../../images/basket.png" alt="basket" srcset="" class="basket-image">
        </div>
        <form action="../../procesos/validarLogin.php" class="login-form" method="POST">
            <div class="form-header-container">
                <div class="form-circle">
                    <div class="form-circle2">
                        <div class="form-rope"></div>
                    </div>
                </div>
                <div class="form-title">
                    Centro Logístico
                </div>
                <div class="form-circle">
                    <div class="form-circle2">
                        <div class="form-rope"></div>
                    </div>
                </div>
            </div>
            <div class="login-title">
                Inicio de Sesión
            </div>
            <div class="form-input">
                <label for="cedula">Cédula:</label>
                <input type="text" id="cedula" name="cedula" class="input-text" autofocus required>
            </div>
            <div class="form-input">
                <label for="passwd">Contraseña:</label>
                <input type="password" id="passwd" name="passwd" class="input-text" required>
            </div>
            <?php if ($error != '') :?>
            <div class="error-message">
                <h4><?=$error?></h4>
            </div>
            <?php endif ?>
            <div class="login-button">
                <input type="submit" value="Iniciar" class="sign-button">
            </div>
        </form>
    </div>
</body>
</html>