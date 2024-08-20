<?php
require_once "empleado.php";
class Mozo extends Empleado{

    public function AltaMozo(){
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            self::InsertarMozoDB($con);
        }
    }

    public function InsertarMozoDB($coneccion){
        try{
            $consulta = "INSERT INTO mozo (nombre,email,clave) 
                         VALUES (:nombre,:email,:clave)";
            $result = $coneccion->prepare($consulta);
            $result->execute([':nombre'=>$this->_nombre,
                                ':email'=>$this->_email,
                                ':clave'=>$this->_clave]);
            echo "Alta de mozo procesada";
        }catch(PDOException $e){
            return $e->getMessage();
        }
    }


    // Mejora para las conecciones
    // La funcion de listar puede ser solo una a la cual solo le 
    // paso la consulta? Hay que ver el tema de parametrizar.


    public static function ListarMozosRegistrados(){
        return Herramientas::ListarTablaDB("mozo");
    }

    

    public static function AsignarMesa($idCliente,$idMozo){
        $mesa = Mesa::BuscarMesaLibre();
        if($mesa){
            Mesa::SetMozo($mesa["id"],$idMozo);
            Mesa::SetCliente($mesa["id"],$idCliente);
            Mesa::SetEstado($mesa["id"],"Con cliente pidiendo");
        }else{echo "No hay mesa libre";}
    }
    

    public static function TomarPedido($listaIdProductos,$idCliente,$idMozo,$idMesa){
        $con = Herramientas::EstablecerConeccion("localhost","tpcomanda");
        $retorno = false;
        if($con){
            Mesa::SetEstado($idMesa,"cliente esperando pedido");
            $infoPedido = Pedido::InsertarPedidoYProductos($con,$idMozo,$idCliente,$listaIdProductos);
            Mesa::SetPedido($idMesa,$infoPedido["idPedido"]);
            self::DividirPedido($infoPedido["listaIdProductos"],$infoPedido["idPedido"]);
            echo "Pedido realizado";
            $retorno = true;
        }
        return $retorno;
    }



    
    public static function DividirPedido($listaIdProductos,$idPedido){
        //IDPEDIDO IDPEDIDO
        foreach($listaIdProductos as $idProducto){
            //Claro lo que pasa aca es que recibo el id del producto de pedido
            //Entonces tengo que extraer el id del producto original,
            //que esta en la tabla de productosDePedidos
            $infoProductoDePedido = Producto::TraerDesdeDB($idProducto,true);
            $infoProducto = Producto::TraerDesdeDB($infoProductoDePedido["idProducto"]);
            if($infoProducto!=false){
                $empleado = EmpleadoCocina::EmpleadoMasLibre($infoProducto["tipo"]);
                if($empleado==false){
                    echo $infoProducto["tipo"];
                }
                EmpleadoCocina::AsignarTarea($empleado["id"],$infoProducto,$infoProductoDePedido);
            }
        }
    }

    
    public static function GuardarFoto($foto,$mesa){
        if($mesa["idCliente"]!=null){
            $cliente = Cliente::CrearDesdeDB($mesa["idCliente"]);
            if($cliente){
                $nombreArchivo = "idPedido_".$mesa["idPedido"]."idMesa_".$mesa["id"]."_nombreCliente_".$cliente["nombre"]."_idMozo_".$mesa["idMozo"];
                $ruta = "C:\\xampp\htdocs\ProgIII-2024\TPComanda\app\\fotos_mesas/";
                 if(move_uploaded_file($foto->getFilePath(),$ruta.$nombreArchivo.".jpg")){
                    self::InsertarFotoDB($ruta.$nombreArchivo,$mesa["idPedido"]);
                }
            }
        }
    }

    public static function InsertarFotoDB($ruta,$idPedido){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "INSERT INTO foto_mesa (rutaFoto,idPedido) 
                             VALUES (:rutaFoto,:idPedido)";
                $result = $con->prepare($consulta);
                $result->execute([':rutaFoto'=>$ruta,
                                ':idPedido'=>$idPedido]);
                echo "Foto de mesa insertada";
                $retorno = true;
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }
        return $retorno;
    }

    
    public static function EntregarPedido($idMesa, $idPedido){
        Mesa::SetEstado($idMesa,"Con cliente comiendo");
        // Iterar el pedido y cambiar el estado de cada producto a ENTREGADO    
        $listaPedido = Pedido::GetListaProductos($idPedido);
        Mesa::SetPedido($idMesa,null);
        foreach($listaPedido as $producto){
            Producto::SetEstadoYTiemposProdPedido($idPedido,"entregado",0);            
        }
    }

    public static function CalcularTotal($idPedido){
        $total = 0;
        $listaPedido = Pedido::GetListaProductos($idPedido);
        foreach($listaPedido as $producto){
            $producto = Producto::TraerDesdeDB($producto["idProducto"]);
            $total += $producto["precio"];
        }
        return $total;
    }


    public static function CobrarCuenta($idPedido,$idCliente){
        $total = self::CalcularTotal($idPedido);
        Cliente::Pagar($idCliente,$total);
        
    }
}