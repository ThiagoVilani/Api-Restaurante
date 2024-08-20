<?php
class Producto{
    private $_id;
    private $_nombre;
    private $_tiempoPreparacion;
    private $_estadoPreparacion;
    private $_precio;
    private $_tipo;
    private $_stock;
    public function __construct($nombre,$tiempoPreparacion,$precio,$tipo,$stock,$id=null)
    {
        if($id){ $this->_id = $id; }
        $this->_nombre = $nombre;
        $this->_tiempoPreparacion = $tiempoPreparacion;
        $this->_precio = $precio;
        $this->_tipo = $tipo;
        $this->_stock = $stock;
    }
    

    public static function AltaProducto($nombre,$tiempoPreparacion,$precio,$tipo,$stock){
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            self::InsertarProductoDB($con,$nombre,$tiempoPreparacion,$precio,$tipo,$stock);
            $con = null;
        }
    }

    public static function InsertarProductoDePedido($coneccion,$idPedido,$idProducto){
        try{
            $consulta = "INSERT INTO producto_de_pedido (idPedido,idProducto) 
                         VALUES (:idPedido,:idProducto)";
            $result = $coneccion->prepare($consulta);
            $result->execute([':idPedido'=>$idPedido,':idProducto'=>$idProducto]);
            echo "Alta de producto procesada";
            return $coneccion->lastInsertId();
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }


    public static function InsertarProductoDB($coneccion,$nombre,$tiempoPreparacion,$precio,$tipo,$stock){    
        try{
            $consulta = "INSERT INTO producto (nombre,tiempoPreparacion,precio,tipo,stock) 
                         VALUES (:nombre,:tiempoPreparacion,:precio,:tipo,:stock)";
            $result = $coneccion->prepare($consulta);
            $result->execute([':nombre'=>$nombre,    
                                ':tiempoPreparacion'=>$tiempoPreparacion,
                                ':precio'=>$precio,
                                'tipo'=>$tipo,
                                'stock'=>$stock]);
            if($result!=false){
                echo "Alta de producto procesada";
            }
        }catch(PDOException $e){
            echo $e->getMessage();
            return $e->getMessage();
        }
    }

    public function ListarProductosDB(){
        return Herramientas::ListarTablaDB("producto");
    }

    public static function TraerDesdeDB($idProducto,$dePedido=false){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = $dePedido ?
                "SELECT * FROM producto_de_pedido WHERE id = :idProducto" :
                "SELECT * FROM producto WHERE idProducto = :idProducto";
                // var_dump($idProducto);
                $result = $con->prepare($consulta);
                $result->bindParam(':idProducto', $idProducto, PDO::PARAM_STR);
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

    public static function SetDemora($idProducto){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "UPDATE producto_de_pedido SET demora = 1 WHERE id = :id";
                $result = $con->prepare($consulta);
                $result->bindParam(":id",$idProducto,PDO::PARAM_INT);
                $result->execute();
                if($result){
                    echo "estado de la demora modificado";
                    $retorno = true;
                }
            }catch(PDOException $e){
                echo $e;
            }
        }
        return $retorno;
    }

    public static function SetEstadoYTiemposProdPedido($idProducto,$estado,$tiempoEstimado=null,$horaInicio=null){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{    
                if($horaInicio!=null){
                    $consulta = "UPDATE producto_de_pedido 
                                SET estado = :estado,
                                    hora_inicio_preparacion = :horaInicio,
                                    tiempo_estimado = :tiempoEstimado
                                WHERE id = :id;";
                    $result = $con->prepare($consulta);
                    $result->bindParam(':horaInicio', $horaInicio, PDO::PARAM_STR);
                    $result->bindParam(':tiempoEstimado', $tiempoEstimado, PDO::PARAM_INT);
                }else{
                    $consulta = "UPDATE producto_de_pedido SET estado = :estado WHERE id = :id";
                    $result = $con->prepare($consulta);
                }
                $result->bindParam(':id', $idProducto, PDO::PARAM_INT);
                $result->bindParam(':estado', $estado, PDO::PARAM_STR);
                $result->execute();
                if($result!=false){
                    echo "Estado modificado";
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function DescontarStock($idProducto){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{                    
                $consulta = "UPDATE producto SET stock = stock - 1 WHERE idProducto = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $idProducto, PDO::PARAM_INT);
                $result->execute();
                if($result!=false){
                    echo "Stock modificado";
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }
}