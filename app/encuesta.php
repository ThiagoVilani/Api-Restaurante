<?php
class Encuesta{
    public static function Insertar($idCliente,$puntajeMesa,$puntajeRestaurante,$puntajeMozo,$puntajeCocinero,$opinion){
        $coneccion = Herramientas::EstablecerConeccion("localhost","TPComanda");
        $retorno = false;
        if($coneccion){
            try{
                $consulta = "INSERT INTO encuesta (idCliente,puntajeMesa,puntajeRestaurante,puntajeMozo,puntajeCocinero,opinion)
                             VALUES (:idCliente,:puntajeMesa,:puntajeRestaurante,:puntajeMozo,:puntajeCocinero,:opinion)";
                $result = $coneccion->prepare($consulta);
                $result->execute([':idCliente'=>$idCliente,
                                    ':puntajeMesa'=>$puntajeMesa,
                                    ':puntajeRestaurante'=>$puntajeRestaurante,
                                    ':puntajeMozo'=>$puntajeMozo,
                                    ':puntajeCocinero'=>$puntajeCocinero,
                                    ':opinion'=>$opinion]);
                echo "Encuesta registrada";
            }catch(PDOException $e){
                echo $e->getMessage();
            }
        }
        return $retorno;
    }
}