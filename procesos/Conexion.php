<?php
DEFINE('SERVIDOR', "localhost");
DEFINE('USER', "root");
DEFINE('PASSWD', "");
DEFINE('BASE_DATOS', "superinstant1");
class Conexion
{
    private $conexion_bd;

    function Conexion(){
        $this->conexion_bd =  @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
    }

    function GetNotificaciones(){

        if ($this->conexion_bd){
            $sql = 'CALL get_notificaciones()';
            $res = mysqli_query($this->conexion_bd, $sql);
            if ($res){
                $result = [];
                $res_array = mysqli_fetch_array($res);

                while ($res_array){
                    $result[] = $res_array;
                    $res_array = mysqli_fetch_array($res);
                }
                mysqli_free_result($res);
                mysqli_close($this->conexion_bd);
                return $result;
                
            } else {
                return 'Error en la consulta: '. mysqli_error($this->conexion_bd);
            }
        } else {
            return "Error en la conexion: ".mysqli_connect_errno().' '.mysqli_connect_error();
        }
    }

    function GetSucursal(){
        {
            $conexion_bd = @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
            if ($conexion_bd) {
                $sql = "CALL get_sucursal()";
                $res_array = [];
                $result_array = [];
                if ($result = mysqli_query($conexion_bd, $sql)) {
                    $res_array = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    while ($res_array) {
                        echo $res_array;
                        $result_array[] = $res_array;
                        $res_array = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    }
                    mysqli_free_result($result);
                    mysqli_close($conexion_bd);
                    return $result_array;
                } else {
                    return [];
                }
            } else {
                echo 'error al conectarse';
                mysqli_close($conexion_bd);
            }
        }
    }

    function Get_Categorias(){
        {
            $conexion_bd = @mysqli_connect(SERVIDOR, USER, PASSWD, BASE_DATOS);
            if ($conexion_bd) {
                $sql = "CALL get_categoria()";
                $res_array = [];
                $result_array = [];
                if ($result = mysqli_query($conexion_bd, $sql)) {
                    $res_array = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    while ($res_array) {
                        echo $res_array;
                        $result_array[] = $res_array;
                        $res_array = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    }
                    mysqli_free_result($result);
                    mysqli_close($conexion_bd);
                    return $result_array;
                } else {
                    return [];
                }
            } else {
                echo 'error al conectarse';
                mysqli_close($conexion_bd);
            }
        }
    }
}