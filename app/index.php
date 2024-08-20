<?php
require_once "../vendor/autoload.php";
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
include "herramientas.php";
include "./middlewares/CrearClienteMW.php";
include "./middlewares/CrearEmpleadoCocinaMW.php";
include "./middlewares/CrearMozoMW.php";
include "./middlewares/CrearProductoMW.php";
include "./middlewares/ValidarTipoEmpleadoCocinaMW.php";
include "./middlewares/ValidarEmailMW.php";
include "./middlewares/ValidarClaveMW.php";

// En un nuevo flujo ya con el tema del paso por paso
// El primer paso y  el segundo ya estan completados y funcionan bien

// Del tercer paso, el de listar ya esta hecho creo pero lo que haria falta seria
// limitar el uso de las funciones solo cuando demuestren ser del sector o ser admins

// El paso 4 ya esta listo

// El paso 5... Creo que ya esta listo. 

// Paso 6
// Debe cambiar el estado a “listo para servir” CHECK
// Pedir la lista segun el tipo de empleado CHECK

//Paso 7
// Check

//Paso 8
// Check

//Paso 9
//Cobrar la cuenta
// Sumar el precio de todos los productos
// Tengo que fijarme si el cliente tiene el dinero suficiente para pagar. 
// Restar la plata en la base del cliente
// Crear un recibo con la info del pedido
// Fecha, Monto, Cliente, Mozo, Mesa, Productos.






//Tengo que crear el autoload para no tener los include
//Tengo que crear los mw de los get

//		HAY QUE REVISAR BIEN CUALES TIENEN QUE LLEGAR POR POST, GET, PUT...

//							FLUJO
// 	Un usuario se logea (Tambien hay que probar que se registre) (Aca si que paso a la db)
//						(Ya esta hecho el login, solo falta implementar la tokenizacion)
//	El restaurante busca el mozo mas libre (hecho y probado creo)
//	El mozo le asigna una mesa al usuario (Ya esta hecho y probado. Creo...)

//	Ya lo comprobe con la db
//	El usuario hace el pedido, eligiendo los productos

//	Si bien esta hecho, no lo comprobe funcionando
// 	El mozo toma el pedido y hace la foto (Ya esta hecho, pero la foto esta hecha a parte porque el mozo tiene la opcion de no hacerla) (No esta comprobado como lo anterior)

// 	Hecho y comprobado su funcionamiento
//  El mozo divide el pedido segun el area (Este tambien ya lo hice y funciona bien con la db)

//	Hecho y comprobado, pero con el detalle de que quizas manejo mal el tiempo de la demora
// 	Cada area le pone un tiempo estimado de coccion

//	Este creo que lo hice pero no lo probe
// 	Una vez el pedido este terminado, el estado de este cambia a "Listo para entregar"(Creo que listo)

// 	El Mozo entrega el pedido (Entiendo que si, le cambio el estado a todos los productos a "entregado")
// 	El cliente pide la factura y paga(puede hacer las cosas por separado)
// 	El cliente puntua a: mesa, mozo, restaurante y cocinero del 1 al 10 c/u por separado
//  Tambien un texto de hasta 66 caracteres
//	Sigue... 	



//				Creo la app que va a manejar todas la peticiones
$app = AppFactory::create();
//


$app->post("/calcularTotal",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "factura.php";
	include "mozo.php";
	include "pedido.php";
	include "producto.php";
	var_dump(Factura::CalcularTotalPedido(103));
	echo Mozo::CalcularTotal(103);
	return $response;
});


$app->post("/listarMesas",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "mesa.php";
	var_dump(Mesa::ListarMesas());
	return $response;
});

$app->post("/pedidosListos",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "pedido.php";
	var_dump(Pedido::GetPedidosListosPorMozo($params["idMozo"]));
	return $response;
});

$app->post("/pedidosPorMesa",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "pedido.php";
	var_dump(Pedido::GetPedidosPorMesa(2));
	return $response;
});


$app->post("/realizarPedido",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "cliente.php";
	include "mozo.php";
	include "pedido.php";
	include "producto.php";
	include "empleadoCocina.php";
	require_once "mesa.php";
	Cliente::RealizarPedido(4,1,[5,5,1,7,8],2);
	return $response;
});

$app->post("/tiemposTodosPedidos",function(Request $request,Response $response,$args){
	include "pedido.php";
	include "producto.php";
	var_dump(Pedido::GetTiempoPedidos());
	return $response;
});

$app->post("/tiempoPedido",function(Request $request,Response $response,$args){
	include "pedido.php";
	include "producto.php";
	var_dump(Pedido::GetTiempoRestante(85));
	return $response;
});

$app->post("/todosPedidosTipo",function(Request $request,Response $response,$args){
	include "pedido.php";
	include "producto.php";
	var_dump(Pedido::GetListaProductosPorTipo("cocina",true));
	return $response;
});




$app->post("/insertarProducto",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "producto.php";
	Producto::AltaProducto($params["nombre"],$params["tiempoPreparacion"],$params["precio"],$params["tipo"],$params["stock"]);
	return $response;
});



$app->post("/guardarFoto",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	$files = $request->getUploadedFiles();
	include "mozo.php";
	include "cliente.php";
	include "mesa.php";
	$mesa = Mesa::GetMesa($params["idMesa"]);
	if(isset($files["fotoMesa"])){
		Mozo::GuardarFoto($files["fotoMesa"],$mesa);
	}
	return $response;
});



$app->get('/prueba', function ($request, $response, array $args) {
	$t = time();
	echo date("H:i:s", $t);
	
	return $response;
});		


$app->post('/cuantoFalta', function ($request, $response, array $args) {
	require_once "pedido.php";
	require_once "producto.php";
	$params = $request->getParsedBody();
	$tiempoRestante = Pedido::GetTiempoRestante($params["idPedido"]);
	$mensaje = "El tiempo restante aproximado es de ".$tiempoRestante["tiempo"]." minutos";
	if($tiempoRestante["demora"]){
		$mensaje .=" y hay items con demora"; 
	}
	echo $mensaje;
	return $response;
});		
	

$app->get('/AsignarMesaMozoUsuario', function ($request, $response, array $args) {
	include "restaurante.php";
	include "mozo.php";
	$mozo = Restaurante::BuscarMozoLibreDB();
	Mozo::AsignarMesa("PonerIDdelCliente",$mozo["id"]);	
	return $response;
});


//	Las anteriores son pruebas
/////////////////////////////////////
$app->post("/nuevoCliente",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "cliente.php";
	$cliente = new Cliente($params["nombre"],$params["email"],$params["clave"],$params["dinero"]);
	// $cliente->AltaCliente();
	return $response;
})->add(new CrearClienteMW());


$app->post("/nuevoEmpleadoCocina",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "empleadoCocina.php";
	$emp = new EmpleadoCocina($params["nombre"],$params["email"],$params["clave"],$params["tipo"]);
	$emp->AltaEmpleado();
	return $response;
})->add(new CrearEmpleadoCocinaMW());


$app->post("/nuevoMozo",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "mozo.php";
	$mozo = new Mozo($params["nombre"],$params["email"],$params["clave"]);
	$mozo->AltaMozo();
	return $response;
})->add(new CrearMozoMW());


// $app->post("/nuevoProducto",function(Request $request,Response $response,$args){
// 	$params = $request->getParsedBody();
// 	include "producto.php";
// 	$producto = new Producto($params["nombre"],$params["tiempoPreparacion"],$params["precio"],$params["tipo"],$params["stock"]);
// 	$producto->AltaProducto();
// 	return $response;
// })->add(new CrearProductoMW());


//Inicio Sesion
$app->post("/LoginCliente",function(Request $request,Response $response,$args){
	$params = $request->getParsedBody();
	include "cliente.php";
	$infoCliente = Cliente::ValidarCliente($params["email"],$params["clave"]);
	if($infoCliente){

		echo "Inicio de sesion realizado";
	}
	return $response;
})->add(new ValidarEmailMW())->add(new ValidarClaveMW());





// TODOS estos GET deberia estar en una sola funcion
$app->get("/listaMozos",function(Request $request,Response $response,$args){	
	include "mozo.php";
	var_dump(Mozo::ListarMozosRegistrados());
	return $response;
});

$app->get("/listaClientes",function(Request $request,Response $response,$args){	
	include "cliente.php";
	var_dump(Cliente::ListarClientesRegistrados());
	return $response;
});

$app->get("/listaEmpleadosCocina",function(Request $request,Response $response,$args){	
	include "empleadoCocina.php";
	$param = $request->getQueryParams();
	var_dump(EmpleadoCocina::ListarEmpleadosPorTipo($param["tipo"]));
	return $response;
})->add(new ValidarTipoEmpleadoCocinaMW());

$app->run();
