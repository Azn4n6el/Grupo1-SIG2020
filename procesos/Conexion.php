<?php
DEFINE('SERVIDOR', "localhost");
DEFINE('USER', "root");
DEFINE('PASSWD', "");
DEFINE('BASE_DATOS', "superinstant1");
class Conexion
{
    private $conexion_bd;

    function ValidateLogin($cedula, $contrasena)
    {
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL get_login("'.$cedula.'","'.$contrasena.'")';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res) {
                $res_array = mysqli_fetch_array($res, MYSQLI_ASSOC);
                mysqli_free_result($res);
                mysqli_close($this->conexion_bd);
                return $res_array;
            } else {
                return 'Error en la consulta: ' . mysqli_error($this->conexion_bd);
            }
        } else {
            return "Error en la conexion: " . mysqli_connect_errno() . ' ' . mysqli_connect_error();
        }
    }



    /* TRAE TODAS LAS NOTIFICACIONES */
    function GetNotificaciones($ruc_centro)
    {
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL get_notificacionesByCentroRUC("'.$ruc_centro.'")';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res) {
                $result = [];
                $res_array = mysqli_fetch_array($res, MYSQLI_ASSOC);

                while ($res_array) {
                    $result[] = $res_array;
                    $res_array = mysqli_fetch_array($res, MYSQLI_ASSOC);
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

    function GetSucursales($ruc_centro)
    {
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL get_sucursalesByCentroRUC("'.$ruc_centro.'")';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res) {
                $result = [];
                $res_array = mysqli_fetch_array($res, MYSQLI_ASSOC);

                while ($res_array) {
                    $result[] = $res_array;
                    $res_array = mysqli_fetch_array($res, MYSQLI_ASSOC);
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
                $res_array = mysqli_fetch_array($res, MYSQLI_ASSOC);

                while ($res_array) {
                    $result[] = $res_array;
                    $res_array = mysqli_fetch_array($res, MYSQLI_ASSOC);
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
            $sql = 'CALL add_reabastece(' . $id_suministro . ',' . $ruc_sucursal . ',' . $ruc_centro . ',' . $precio . ',' . $cantidad . ')';
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

    function DeleteNotifica($id_notifica)
    {
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL delete_notificaByID(' . $id_notifica . ')';
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

    function GetHistorial($ruc_centro){
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
        if ($this->conexion_bd) {
            $sql = 'CALL get_historial("'.$ruc_centro.'")';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res) {
                $result = [];
                $res_array = mysqli_fetch_array($res, MYSQLI_ASSOC);

                while ($res_array) {
                    $result[] = $res_array;
                    $res_array = mysqli_fetch_array($res, MYSQLI_ASSOC);
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
}
