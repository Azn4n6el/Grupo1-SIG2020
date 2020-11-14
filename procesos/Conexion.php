<?php
DEFINE('SERVIDOR', "localhost");
DEFINE('USER', "root");
DEFINE('PASSWD', "");
DEFINE('BASE_DATOS', "superinstant1");
class Conexion
{
    private $conexion_bd;


    /* TRAE TODAS LAS NOTIFICACIONES */
    function GetNotificaciones()
    {
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL get_notificacionesByCentroRUC(123)';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res) {
                $result = [];
                $res_array = mysqli_fetch_array($res);

                while ($res_array) {
                    $result[] = $res_array;
                    $res_array = mysqli_fetch_array($res);
                }
                mysqli_free_result($res);
                mysqli_close($this->conexion_bd);
                return $result;
            } else {
                return 'Error en la consulta: ' . mysqli_error($this->conexion_bd);
            }
        } else {
            return "Error en la conexion: " . mysqli_connect_errno() . ' ' . mysqli_connect_error();
        }
    }

    function GetSucursales()
    {
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL get_sucursalesByCentroRUC(123)';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res) {
                $result = [];
                $res_array = mysqli_fetch_array($res);

                while ($res_array) {
                    $result[] = $res_array;
                    $res_array = mysqli_fetch_array($res);
                }
                mysqli_free_result($res);
                mysqli_close($this->conexion_bd);
                return $result;
            } else {
                return 'Error en la consulta: ' . mysqli_error($this->conexion_bd);
            }
        } else {
            return "Error en la conexion: " . mysqli_connect_errno() . ' ' . mysqli_connect_error();
        }
    }

    function GetSuministros()
    {
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL get_suministros()';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res) {
                $result = [];
                $res_array = mysqli_fetch_array($res);

                while ($res_array) {
                    $result[] = $res_array;
                    $res_array = mysqli_fetch_array($res);
                }
                mysqli_free_result($res);
                mysqli_close($this->conexion_bd);
                return $result;
            } else {
                return 'Error en la consulta: ' . mysqli_error($this->conexion_bd);
            }
        } else {
            return "Error en la conexion: " . mysqli_connect_errno() . ' ' . mysqli_connect_error();
        }
    }

    function AddReabastece($id_suministro, $ruc_sucursal, $ruc_centro, $precio, $cantidad)
    {
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL add_reabastece('.$id_suministro.','.$ruc_sucursal.','.$ruc_centro.','.$precio.','.$cantidad.')';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res) {
                mysqli_close($this->conexion_bd);
                return 'Reabastecimiento Realizado!';
            } else {
                return 'Error en la consulta: ' . mysqli_error($this->conexion_bd);
            }
        } else {
            return "Error en la conexion: " . mysqli_connect_errno() . ' ' . mysqli_connect_error();
        }
    }

    function DeleteNotifica($id_notifica){
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL delete_notificaByID('.$id_notifica.')';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res) {
                mysqli_close($this->conexion_bd);
                return 'Notificación Removida!';
            } else {
                return 'Error en la consulta: ' . mysqli_error($this->conexion_bd);
            }
        } else {
            return "Error en la conexion: " . mysqli_connect_errno() . ' ' . mysqli_connect_error();
        }
    }
}
