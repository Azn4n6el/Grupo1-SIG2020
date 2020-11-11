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
}
