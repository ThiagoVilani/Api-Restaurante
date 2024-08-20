<?php
class Administrador extends Usuario{
    public function __construct($nombre,$email,$clave,$id=null)
    {
        parent::__construct($nombre,$email,$clave,$id);   
    }
}