<?php

use Slim\Http\Request;
use Slim\Http\Response;
use App\Middleware\AuthMiddleware;
// ver: https://www.youtube.com/watch?v=ZLmCdbNYHAo&list=PLfdtiltiRHWGc_yY90XRdq6mRww042aEC&index=28

// RUTAS INICIO

	# Index
	$app->get('/', 'HomeController:login')->setName('login');
	# Post Login para validar ingreso
	$app->post('/postlogin', 'HomeController:postLogin')->setName('postlogin');
	# Home. Pagina con menus y usuario
	$app->get('/home', 'HomeController:index')->setName('home');

//
//  OTRAS RUTAS
//  

# CARTA DE PORTE

# Grupo de todas las páginas, valida ingreso el middleware AuthMiddleware
$app->group('/cartadeporte', function () use ($app, $container) {

	# Carta de porte
	$app->get('', 'CartadeporteController:cartadeporte')->setName('cartadeporte');
	# Carta de porte / Guardar
	$app->post('/guardar', 'CartadeporteController:guardarCP')->setName('cartadeporte.guardar');
	# Carta de porte / Tipos de granos
	$app->get('/tiposgrano', 'CartadeporteController:tiposGrano')->setName('cartadeporte.tiposgrano');
	# Carta de porte / Firmas via ajax
	$app->get('/localidadesviaajax', 'CartadeporteController:localidadesViaAjax')->setName('cartadeporte.localidadesviaajax');
	# Carta de porte / Buscar
	$app->get('/buscar', 'CartadeporteController:buscar')->setName('cartadeporte.buscar');
	# Carta de porte / Lista CdP para Buscar
	$app->get('/listacartas', 'CartadeporteController:listaCartas')->setName('cartadeporte.listacartas');
	# Carta de porte / datos
	$app->get('/datos/{id}', 'CartadeporteController:datos')->setName('cartadeporte.datos');

	# Carta de porte / Archivo Afip
	$app->get('/archivoafip', 'ArchivoafipController:archivoAfip')->setName('cartadeporte.archivoafip');

	# Carta de porte / Informe
	$app->get('/informe', 'InformeController:informe')->setName('cartadeporte.informe');
	# Carta de porte / Informe
	$app->get('/informe/imprime', 'InformeController:imprime')->setName('cartadeporte.informe.imprime');


})->add(new AuthMiddleware($container));

# FIRMAS
$app->group('/firmas', function () use ($app, $container) {

	# Firmas
	$app->get('', 'FirmasController:firmas')->setName('firmas');
	# Firmas / Guardar (POST)
	$app->post('/guardar', 'FirmasController:firmasGuardar')->setName('firmas.guardar');
	# Firmas / Buscar
	$app->get('/buscar', 'FirmasController:firmasBuscar')->setName('firmas.buscar');
	# Firmas / GetFirma
	$app->get('/getfirma', 'FirmasController:getFirma')->setName('firmas.getfirma');


})->add(new AuthMiddleware($container));


