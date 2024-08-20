<?php
class Restaurante{
    private $_listaMesas;
    private $_listaMozos;
    private $_listaEmpleadosCocina;
    private $_listaCocineros;
    private $_listaBartenders;
    private $_listaCervezeros;
    private $_listaPasteleros;

    public function __construct(){

    }
    public function GetMesas(){
        return $this->_listaMesas;
    }
    public function GetMozos(){
        return $this->_listaMozos;
    }

    
    public function BuscarEmpleadoLibre($tipoEmpleado){
        $tipoEmpleado = "_".$tipoEmpleado;
        $empleadoLibre = $this->$tipoEmpleado[0];
        foreach($this->$tipoEmpleado as $empleado){
            if($empleadoLibre->GetCantTareas()<$empleado->GetCantTareas()){
                $empleadoLibre = $empleado;
            }
        }
        return $empleadoLibre;
    }

    public static function BuscarMozoLibreDB(){
        $retorno = false;
        $con = Herramientas::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "SELECT * FROM mozo ORDER BY cantidad_mesas_asignadas ASC LIMIT 1";
                $result = $con->prepare($consulta);
                $result->execute();
                if($result){
                    $retorno = $result->fetch(PDO::FETCH_ASSOC);
                }else{
                    $retorno = false;
                }
            }catch(PDOException $e){
                return $e->getMessage();
            }       
        }
        return $retorno;
    }
}