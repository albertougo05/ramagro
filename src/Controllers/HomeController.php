<?php 

namespace App\Controllers;

#use App\Models\Usuario;
use Slim\Views\Twig as View;


/**
 * Clase controladora desde Login a Home
 * 
 */
class HomeController extends Controller
{
	/**
	 * Name: 'home'
	 * 
	 */
	public function index($request, $response)
	{
		$auth = $this->auth->check();

		if (!$auth) {
			# Si no está logueado no ingresa. Va a login de nuevo
			$this->flash->addMessage('error', 'Acceso incorrecto. Ingrese sus datos');

			return $response->withRedirect($this->router->pathFor('login'));
		}

		// Borrar mensajes Flash
		$this->flash->clearMessages();

		$variables = array('titulo' => 'Ramagro - Menú');

		return $this->view->render($response, 'home.twig', $variables);
	}


	/**
	 * Va a pantalla de Acceso (Login)
	 * Name: /
	 * 
	 * @param  Request $request
	 * @param  Response $response
	 * @return view
	 */
	public function login($request, $response)
	{
		if ( isset($_SESSION['user']) ) {
			// Grabo log de logout usuario
			$this->logger->info('Logout:', array('username' => $_SESSION['user']));
		}

		$this->auth->logout();

		$variables = array('titulo' => 'Ramago - Login');

		return $this->view->render($response, 'login.twig', $variables);
	}

	/**
	 * POST de LOGIN - Verifica login
	 * 
	 * @param  $request
	 * @param  $response
	 * @return Redirige a /Home
	 */
	public function postLogin($request, $response)
	{
		$auth = $this->auth->attempt( $request->getParam('user'),
									  $request->getParam('passw') );

		if (!$auth) {
			# Si no hay coincidencia...
			$this->flash->addMessage('error', 'Datos incorrectos para el acceso.');

			return $response->withRedirect($this->router->pathFor('login'));
		}

		// Grabo log de acceso usuario
		$this->logger->info('Login:', array('username' => $request->getParam('user')));

		return $response->withRedirect($this->router->pathFor('home'));
	}


}
