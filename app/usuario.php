<?php
class usuario{
    protected $_id;
    protected $_nombre;
    protected $_email;
    protected $_clave;

    public function __construct($nombre,$email,$clave,$id=null){
        if($id){
            $this->_id = $id;
        }
        $this->_nombre = $nombre;
        $this->_email = $email;
        $this->_clave = $clave;
    }

    public static function RegistrarIngreso($idUsuario,$tipoUsuario){
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        $retorno = false;
        $fechaYHora = (new DateTime())->format("d-m-Y H:i:s");
        if($con){
            try{
                $consulta = "INSERT INTO ingreso_usuario (tipoUsuario,idUsuario,fecha) 
                             VALUES (:tipoUsuario,:idUsuario,:fecha)";
                $result = $con->prepare($consulta);
                $result->execute([':idUsuario'=>$idUsuario,
                                ':tipoUsuario'=>$tipoUsuario,
                                ':fecha'=>$fechaYHora]);
                echo "Ingreso de usuario registrado";
                $retorno = true;
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }
        return $retorno;
    }
}