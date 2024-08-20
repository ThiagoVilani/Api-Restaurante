<?php
class Pedido{    
    public static function InsertarPedidoYProductos($coneccion,$idMozo,$idCliente,$listaIdProductos){
        $idPedido = self::InsertarPedido($coneccion,$idMozo,$idCliente,"en preparacion");
        $listaIdProductosDePedidos = array();
        if($idPedido){
            foreach($listaIdProductos as $idProducto){
                $id = Producto::InsertarProductoDePedido($coneccion,$idPedido,$idProducto);
                array_push($listaIdProductosDePedidos,$id);
            }
            echo "Productos insertados correctamente";
        }
        return array("idPedido"=>$idPedido,"listaIdProductos"=>$listaIdProductosDePedidos);
    }


    public static function InsertarPedido($coneccion,$idMozo,$idCliente,$estado){
        try{
            $consulta = "INSERT INTO pedido (idCliente,idMozo,estado)
                         VALUES (:idCliente,:idMozo,:estado)";
            $result = $coneccion->prepare($consulta);
            $result->execute([':idMozo'=>$idMozo,':idCliente'=>$idCliente,':estado'=>$estado]);
            if($result){
                echo "Alta de pedido procesada";
                return $coneccion->lastInsertId();
            }
        }catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }


    public static function CrearDesdeDB($idPedido){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "SELECT * FROM pedido WHERE id = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $idPedido, PDO::PARAM_STR);
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



    
    // NO estoy SEGURO de si esta funcion deberia estar aca o 
    // mejor en el archivo de PRODUCTO 
    public static function GetListaProductos($idPedido, $noEntregados=false){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "SELECT * FROM producto_de_pedido WHERE idPedido = :idPedido";
                if($noEntregados){
                    $consulta .= " AND estado = 'en preparacion'";
                }
                $result = $con->prepare($consulta);
                $result->bindParam(':idPedido', $idPedido, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    $retorno = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function GetPedidosPorMesa($idMesa){
        $retorno = false;
        if($con=Herramientas::EstablecerConeccion("localhost","TPComanda")){
            try{
                $consulta = "SELECT pp.id, pp.estado
                            FROM mesa m 
                            JOIN pedido p ON m.idPedido = p.idPedido
                            JOIN producto_de_pedido pp ON p.idPedido = pp.idPedido
                            WHERE m.id = :idMesa";
                $result = $con->prepare($consulta);
                $result->bindparam(':idMesa', $idMesa, PDO::PARAM_INT);
                $result->execute();
                if($result){
                    $retorno = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            }catch(PDOException $exception){
                echo $exception;
            }
        }
        return $retorno;
    }

    public static function EstanListos($listaProductosDePedidos){
        $todosListos = true;
        foreach($listaProductosDePedidos as $producto){
            if($producto["estado"] == "en preparacion"){
                $todosListos = false;
                break;
            }
        }
        return $todosListos;
    }

    public static function RevisarEstadoProductosPedidosPorMesa($idMesa){
        $listaPedidos = self::GetPedidosPorMesa($idMesa);
        if(self::EstanListos($listaPedidos)){
                Mesa::SetEstado($idMesa, "cliente comiendo");
        }
    }
    


    public static function GetPedidosListosPorMozo($idMozo){
        //Buscar en todos los pedidos que no esten entregados,
        //cuales estan listos para servir
        //Cuando los encuentro tengo que cambiar el estado a "entregado" como para simular que lo estoy entregnado
        $retorno = false;
        if($con=Herramientas::EstablecerConeccion("localhost","TPComanda")){
            try {
                echo "En teoria esta todo bien";
                $consulta = "SELECT pp.id,pp.idPedido,pp.idProducto
                                FROM producto_de_pedido pp 
                                JOIN pedido p ON pp.idPedido = p.idPedido
                                JOIN mozo m ON p.idMozo = m.idMozo
                                WHERE m.idMozo = :idMozo AND pp.estado = 'listo para servir'";
                $result = $con->prepare($consulta);
                $result->bindParam(':idMozo', $idMozo, PDO::PARAM_INT);
                $result->execute();
                if ($result) {
                    $retorno = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
            }           
        }
        return $retorno;
    } 


    public static function GetListaProductosPorTipo($tipo,$noEntregados=false){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "SELECT p.idPedido,
                                    p.idCliente,
                                    p.estado,
                                    pp.id,
                                    pp.estado,
                                    pp.demora,
                                    pp.hora_inicio_preparacion,
                                    pp.tiempo_estimado,
                                    pr.nombre
                            FROM pedido p
                            JOIN producto_de_pedido pp ON p.idPedido = pp.idPedido
                            JOIN producto pr ON pp.idProducto = pr.idProducto
                            WHERE pr.tipo = :tipo";    
                if($noEntregados){
                    $consulta .= " AND pp.estado = 'en preparacion'";
                }
                $result = $con->prepare($consulta);    
                $result->bindParam(':tipo', $tipo, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    $retorno = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }


    public static function GetListaPedidos($noEntregados=false){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                if($noEntregados){
                    $consulta = "SELECT * FROM pedido";
                }else{
                    $consulta = "SELECT * FROM pedido WHERE estado = entregado";
                }
                $result = $con->prepare($consulta);
                $result->execute();
                if($result){
                    $retorno = $result->fetchAll(PDO::FETCH_ASSOC);
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }


    public static function GetTiempoRestante($idPedido){
        $tiempoMaximo = 0;
        $hayDemora = false;
        $pedido = self::CrearDesdeDB($idPedido); //obtengo la info del pedido
        //  Tengo que traer solo los que no esten entregados
        $listaProductos = self::GetListaProductos($pedido["id"], true);//obtengo la lista del pedido
        if($listaProductos)
        foreach($listaProductos as $productoDePedido){//por cada producto del pedido

            //  PARA QUE CARAJO ESTOY TRAYENDO ESTE PRODUCTO SI DESPUES NO LO USO?
            $producto = Producto::TraerDesdeDB($productoDePedido["idProducto"]);//obtengo la info del producto

            //obtengo la hora a la cual empezo a hacerse
            $horaInicioSTR = $productoDePedido["hora_inicio_preparacion"];

            //paso la hora a objeto date
            $horaInicio = DateTime::createFromFormat('H:i:s', $horaInicioSTR);
            $horaActual = new DateTime();

            //obtengo la diferencia que hubo entre el comienzo y ahora
            $intervalo = $horaActual->diff($horaInicio);
            $minutosEnCocina = $intervalo->h * 60 + $intervalo->i;            
            
            //Si el tiempo supera al anterior, es el nuevo tiempo a esperar
            $tiempoRestante = $productoDePedido["tiempo_estimado"] - $minutosEnCocina;
            if($tiempoRestante<0){
                Producto::SetDemora($productoDePedido["id"]);    
                $hayDemora = true;
            }

            if($tiempoRestante > $tiempoMaximo){
                $tiempoMaximo = $tiempoRestante;   
            }
        }
        return ["tiempo"=>$tiempoMaximo,"demora"=>$hayDemora];
    }

    public static function GetTiempoPedidos(){
        $infoPedidos = [];
        $listaPedidos = self::GetListaPedidos(true);
        if($listaPedidos){
            foreach($listaPedidos as $pedido){
                $tiempoPedido = self::GetTiempoRestante($pedido["id"]);
                if($tiempoPedido){
                    array_push($infoPedidos,["id"=>$pedido["id"],
                                            "tiempo"=>$tiempoPedido["tiempo"]
                                            ,"demora"=>$tiempoPedido["demora"]]);
                }
            }
        }
        return $infoPedidos;
    }
}