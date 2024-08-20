<?php

class Factura{
    public static function InsertarFacturaDB($infoFactura){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "INSERT INTO factura (idMozo,idCliente,idPedido,fecha,total) 
                             VALUES (:idMozo,:idCliente,:idPedido,:fecha,:total)";
                $result = $con->prepare($consulta);
                $result->execute([':idMozo'=>$infoFactura["idMozo"],
                                    ':idCliente'=>$infoFactura["idCliente"],
                                    ':idPedido'=>$infoFactura["idPedido"],
                                    'fecha'=>$infoFactura["fecha"],
                                    'total'=>$infoFactura["total"]]);
                echo "Factura ingresada";
            }catch(PDOException $e){
                return $e->getMessage();
            }     
        }
    }

    public static function CrearDesdeDB($idFactura){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "SELECT * FROM factura WHERE idFactura = :idFactura;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $idFactura, PDO::PARAM_STR);
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

    
    public static function FacturarPedido($idPedido){
        $pedido = Pedido::CrearDesdeDB($idPedido);

    }

    public static function CalcularTotalPedido($idPedido){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "SELECT SUM(producto.precio) AS total
                FROM producto_de_pedido productoPedido
                JOIN pedido pedido ON productoPedido.idPedido = pedido.idPedido
                JOIN producto producto ON productoPedido.idProducto = producto.idProducto
                WHERE productoPedido.idPedido = :idPedido";
                $result = $con->prepare($consulta);    
                $result->bindParam(':idPedido', $idPedido, PDO::PARAM_INT);
                $result->execute();         
                if($result){
                    $retorno = $result->fetch(PDO::FETCH_ASSOC)["total"];
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }
        return $retorno;
    }
}