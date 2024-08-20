<?php
require_once "usuario.php";
class Cliente extends Usuario{
    public static function ValidarCliente($email,$clave){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{ 
                $consulta = "SELECT * FROM cliente WHERE email = :email AND clave = :clave";
                $result = $con->prepare($consulta);
                $result->bindParam(':email', $email, PDO::PARAM_STR);
                $result->bindParam(':clave', $clave, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    $info = $result->fetch(PDO::FETCH_ASSOC);
                    if(!empty($info)){
                        $retorno = $info;
                        // var_dump($info);
                        echo "Usuario encontrado";
                    }else{
                        echo "Usuario no encontrado";
                    }
                }
            }catch(PDOException $e){
                return $e->getMessage();
            }       
        }
        return $retorno;
    }


    // public function AltaCliente(){
    //     $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
    //     if($con){
    //         self::InsertarClienteDB($con);
    //     }
    // }

    // public function InsertarClienteDB($coneccion){
    //     try{
    //         $consulta = "INSERT INTO cliente (nombre,email,clave,dinero) 
    //                      VALUES (:nombre,:email,:clave,:dinero)";
    //         $result = $coneccion->prepare($consulta);
    //         $result->execute([':nombre'=>$this->_nombre,
    //                             ':email'=>$this->_email,
    //                             ':clave'=>$this->_clave,
    //                             ':dinero'=>$this->_dineroDisponible]);
    //         echo "Alta de cliente procesada";
    //     }catch(PDOException $e){
    //         return $e->getMessage();
    //     }
    // }


    public static function CrearDesdeDB($id){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "SELECT * FROM cliente WHERE idCliente = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $id, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    $retorno = $result->fetch(PDO::FETCH_ASSOC);
                    echo "Cliente creado";
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function ListarClientesRegistrados(){
        return Herramientas::ListarTablaDB("cliente");
    }

    public static function RealizarPedido($idCliente,$idMozo,$listaIdProductos,$idMesa){
        if(Mozo::TomarPedido($listaIdProductos,$idCliente,$idMozo,$idMesa)){
            echo "Pedido realizado";
            return true;
        }else{return false;}
    }
    
    public static function PedirLaCuenta($idPedido){ 
        $total = Mozo::CalcularTotal($idPedido);
        return $total;
    }

    public static function PagarCuenta($idPedido,$idCliente){
        Mozo::CobrarCuenta($idPedido, $idCliente);
    }


    public static function Pagar($idCliente,$monto){
        if(self::ActualizarDinero($idCliente,$monto)){
            return true;
        }else{ return false; };
    }


    public static function ActualizarDinero($idCliente,$montoARestar){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "UPDATE cliente SET dinero = dinero - :monto WHERE idCliente = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $idCliente, PDO::PARAM_STR);
                $result->bindParam(':monto', $montoARestar, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    echo "Dinero actualizado";
                    $retorno = true;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }
}