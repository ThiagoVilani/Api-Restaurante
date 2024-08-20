<?php
require_once "usuario.php";
class Empleado extends Usuario{
    protected $_listaPedidos;  

    public function __construct($nombre,$email,$clave,$id=null){
        parent::__construct($nombre,$email,$clave,$id);
    }
    
    public function GetListaPedidos(){
        return $this->_listaPedidos;
    }
}