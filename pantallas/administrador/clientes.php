<?php
include '../../procesos/Conexion.php';
session_start();
$obj = new Conexion();
$notificaciones = $obj->GetNotificaciones();
if (isset($_SESSION['message'])) {
    $mensaje = $_SESSION['message'];
    unset($_SESSION['message']);
} else {
    $mensaje = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
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
    <div class="custom-modal" id="custom-modal" onclick="closeModal()">
        <div class="modal-box">
            <div class="img-success">
                <img src="https://img.icons8.com/flat_round/100/000000/checkmark.png" id="msg-icon"/>
            </div>
            <div class="modal-message">
                <h2 id="modal-msg">¡Reabastecido Satisfactoriamente!</h2>
            </div>
            <div class="modal-footer">
                <button type="button" class="ok-button" onclick="closeModal()">OK</button>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    let links = document.getElementsByClassName('list-links');
    let message = <?php echo json_encode($mensaje) ?>;
    links[0].style.cssText = 'background-color:var(--form-color); transform:scale(1.1);';

    //MOSTRAR MENSAJE
    if (message != '') {
        let modal = document.getElementById('custom-modal');
        let msg = document.getElementById('modal-msg');
        if (message.indexOf('Error') >= 0) {
            document.getElementById('msg-icon').src = "https://img.icons8.com/officel/100/000000/high-risk.png";
        }
        msg.innerHTML = message;
        modal.style.display = 'block';
    }

    //CERRAR MODAL
    const closeModal = () => {
        let modal = document.getElementById('custom-modal');
        modal.style.display = "none";
    }
</script>