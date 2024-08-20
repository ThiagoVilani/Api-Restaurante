<?php
require_once "empleado.php";
class EmpleadoCocina extends Empleado{
    private $_listaTareas = array();
    private $_tipo;
    public function __construct($nombre,$email,$clave,$tipo,$id=null){
        parent::__construct($nombre,$email,$clave,$id);
        $this->_tipo = $tipo;
    }    

    
    public function AltaEmpleado(){
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            self::InsertarEmpleadoDB($con);
        }
    }

    public function InsertarEmpleadoDB($coneccion){
        try{
            $consulta = "INSERT INTO empleado_cocina (nombre,email,clave,tipo) 
                         VALUES (:nombre,:email,:clave,:tipo)";
            $result = $coneccion->prepare($consulta);
            $result->execute([':nombre'=>$this->_nombre,
                                ':email'=>$this->_email,
                                ':clave'=>$this->_clave,
                                'tipo'=>$this->_tipo]);
            echo "Alta de Empleado procesada";
        }catch(PDOException $e){
            return $e->getMessage();
        }
    }

    public static function ListarEmpleadosPorTipo($tipo){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{ 
                $consulta = "SELECT * FROM empleado_cocina WHERE tipo = :tipo";
                //TIPO puede ser cocinero, bartender, cervezero, pastelero
                $result = $con->prepare($consulta);
                $result->bindParam(':tipo', $tipo, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    $retorno = $result->fetchAll(PDO::FETCH_ASSOC);
                }else{
                    $retorno = false;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }


    public static function EmpleadoMasLibre($tipo){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{ 
                $consulta = "SELECT * FROM empleado_cocina WHERE tipo = :tipo ORDER BY cantidad_tareas_asignadas ASC LIMIT 1";
                //TIPO puede ser cocina, barra, cerveceria, pasteleria
                $result = $con->prepare($consulta);
                $result->bindParam(':tipo', $tipo, PDO::PARAM_STR);
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

    
    public static function AsignarTarea($idEmpleado,$producto,$productoDePedido){
        self::SumarORestarTarea($idEmpleado,"s");
        $tiempoInicio = date("H:i:s", time());
        Producto::DescontarStock($producto["idProducto"]);
        $tiempoEstimado = self::CalcularTiempo($idEmpleado,$producto);
        Producto::SetEstadoYTiemposProdPedido($productoDePedido["id"],
                                            "en preparacion",
                                            $tiempoEstimado,
                                            $tiempoInicio);
    }


    public static function CalcularTiempo($idEmpleado,$producto){
        $cantTareas = self::GetCantTareas($idEmpleado);
        switch($cantTareas){
            case $cantTareas>3:
                $tiempo = 5;        
                break;
            case $cantTareas>6:
                $tiempo = 10;
                break;
            case $cantTareas>9:
                $tiempo = 15;
                break;
        }
        return $tiempo+$producto["tiempoPreparacion"];
    }

    public static function GetCantTareas($idEmpleado){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{ 
                $consulta = "SELECT cantidad_tareas_asignadas FROM empleado_cocina WHERE id = :id";
                //TIPO puede ser cocina, barra, cerveceria, pasteleria
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $idEmpleado, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    $retorno = $result->fetch(PDO::FETCH_ASSOC);
                }else{
                    $retorno = false;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function SumarTarea($idEmpleado){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "UPDATE empleado_cocina SET cantidad_tareas_asignadas = cantidad_tareas_asignadas + 1 WHERE id = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $idEmpleado, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    echo "Tarea agregada";
                    $retorno = true;
                }else{
                    $retorno = false;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function SumarORestarTarea($idEmpleado,$accion){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            switch($accion){
                case "r":
                    $operador = "-";
                    $consulta = "UPDATE empleado_cocina SET cantidad_tareas_asignadas = cantidad_tareas_asignadas - 1 WHERE id = :id"; 
                    break;
                case "s":
                    $operador = "+";
                    $consulta = "UPDATE empleado_cocina SET cantidad_tareas_asignadas = cantidad_tareas_asignadas + 1 WHERE id = :id";
                    break;
            }
            try{    //PROBLEMAS CON LA CONSULTA VARIABLE
                // $consulta = "UPDATE empleado_cocina SET cantidad_tareas_asignadas = cantidad_tareas_asignadas $operador 1 WHERE id = :id;";
                $result = $con->prepare($consulta);
                $result->bindParam(':id', $idEmpleado, PDO::PARAM_STR);
                $result->execute();
                if($result){
                    echo "Lista de tareas actualizada";
                    $retorno = true;
                }else{
                    $retorno = false;
                }
            }catch(PDOException $e){
                echo $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function TerminarTarea($idProducto,$idEmpleado){
        Producto::SetEstadoYTiemposProdPedido($idProducto,"listo para entregar");
        self::SumarORestarTarea($idEmpleado,"r");
        //  Creo que con esto ya estaria para terminar el pedido
        //  Ahora deberia entregarlo el mozo en la mesa y a partir de ahi 
    }
}