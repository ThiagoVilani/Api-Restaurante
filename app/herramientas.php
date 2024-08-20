<?php
class Herramientas{
    public static function EstablecerConeccion($host,$nombreDB){
        try{
            $strDB = "mysql:host=$host; dbname=$nombreDB";
            $base = new PDO($strDB,"root","");
            $base->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $base->exec("SET CHARACTER SET utf8");
            return $base;
        }
        catch(PDOException $e){
            echo $e->getMessage();
            return false;
        }
    }

    public static function ListarTablaDB($tabla){
        $retorno = false;
        $con = self::EstablecerConeccion("localhost","TPComanda");
        if($con){
            try{
                $consulta = "SELECT * FROM $tabla";
                $result = $con->prepare($consulta);
                $result->execute();
                if($result){
                    $retorno = $result->fetchAll(PDO::FETCH_ASSOC);
                }else{
                    $retorno = false;
                }
                echo "Lista obtenida";
            }catch(PDOException $e){
                return $e->getMessage();
            }       
        }
        return $retorno;
    }

    public static function ValidarEmail($email){
        $patronEmail = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
        return preg_match($patronEmail, $email);
    }
    
    public static function ValidarPalabra($palabra){
        $patronPalabra = "/^[a-zA-Z]{3,100}$/";
        return preg_match($patronPalabra, $palabra);
    }

    public static function ValidarClave($clave){
        $patronClave = "/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,12}$/";
        return preg_match($patronClave,$clave);
    }

    public static function ValidarNumero($numero){
        $esNumero = false;
        if(is_numeric($numero)){
            if($numero > 0){
                $esNumero = true;
            }
        }
        return $esNumero;
    }

    public static function ValidarTipoEmpleadoCocina($tipo){
        $retorno = false;
        if(self::ValidarPalabra($tipo)){
            if($tipo == "cocinero" || $tipo == "pastelero" || $tipo == "cervezero" || $tipo == "bartender"){
                $retorno = true;
            }
        }
        return $retorno;
    }

    public static function ValidarTipoProducto($tipo){
        $retorno = false;
        if(self::ValidarPalabra($tipo)){
            if($tipo == "cocina" || $tipo == "postre" || $tipo == "cerveza" || $tipo == "tragos"){
                $retorno = true;                                                                                           
            }
        }
        return $retorno;
    }
}