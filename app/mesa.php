<?php
class Mesa{
    public static function GetMesa($id){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "SELECT * FROM mesa WHERE id = :id LIMIT 1";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $id, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    $retorno = $result->fetch(PDO::FETCH_ASSOC);
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function GetEstado($id){
        $info = self::GetMesa($id);
        return $info["estado"];
    }

    public static function SetMozo($idMesa,$idMozo){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "UPDATE mesa SET idMozo = :idMozo WHERE id = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $idMesa, PDO::PARAM_STR);
                $result->bindParam(':idMozo', $idMozo, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    echo "Mozo asignado";
                }else{
                    $retorno = false;
                }
            }catch(PDOException $e){
                return $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function SetCliente($idMesa,$idCliente){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "UPDATE mesa SET idCliente = :idCliente WHERE id = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $idMesa, PDO::PARAM_STR);
                $result->bindParam(':idCliente', $idCliente, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    echo "Cliente asignado";
                }else{
                    $retorno = false;
                }
            }catch(PDOException $e){
                return $e->getMessage();
            }       
        }
        return $retorno;
    }


    public static function SetEstado($id,$estado){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "UPDATE mesa SET estado = :estado WHERE id = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $id, PDO::PARAM_STR);
                $result->bindParam(':estado', $estado, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    echo "Estado de mesa modificado";
                    $retorno = true;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function SetPedido($id,$idPedido){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "UPDATE mesa SET idPedido = :idPedido WHERE id = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $id, PDO::PARAM_STR);
                $result->bindParam(':idPedido', $idPedido, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    echo "Id de pedido modificado";
                    $retorno = true;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function BuscarMesaLibre(){
        $retorno = false;
        if($con = Herramientas::EstablecerConeccion("localhost","TPComanda")){
            try{
                $consulta = "SELECT * FROM mesa WHERE estado = 'libre' LIMIT 1";
                $result = $con->prepare($consulta);
                $result->execute();
                if($result){
                    $retorno = $result->fetch(PDO::FETCH_ASSOC);
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function ListarMesas(){
        $retorno = false;
        if($con = Herramientas::EstablecerConeccion("localhost","TPComanda")){
            try{
                $consulta = "SELECT * FROM mesa";
                $result = $con->prepare($consulta);
                $result ->execute();
                if($result){
                    $retorno = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }
        return $retorno;
    }
}