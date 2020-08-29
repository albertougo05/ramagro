<?php 

namespace App\Controllers\Firmas;

use App\Models\Firma;
use App\Models\Localidades;

use App\Controllers\Controller;

/**
 * 
 * Clase CartaDePorte
 * 
 */
class FirmasController extends Controller
{
	/**
	 * Firmas (inicio)
	 * Name: firmas
	 * 
	 * @param  Request  $request
	 * @param  Response $response
	 * @return View 
	 */
	public function firmas($request, $response)
	{
		$condFiscal  = array('Consumidor final', 'Exento', 'Monotributo', 'Responsable Inscripto');

		$datos = array( 'titulo'   => 'Ramagro - Firmas',
						'condFiscal'  => $condFiscal,
		);

		return $this->view->render($response, 'firmas/firmas.twig', $datos);
	}

	/**
	 * Guardar datos de firma en BD
	 * 
	 * @param  Request $request 
	 * @param  Response $response 
	 * @return json
	 */
	public function firmasGuardar($request, $response)
	{
		$data = $request->getParsedBody();
    	$firma_data = [];
    	$firma_id   = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);

    	$firma_data['Nombre']    = filter_var($data['Nombre'], FILTER_SANITIZE_STRING);
    	$firma_data['Fantasia']  = filter_var($data['Fantasia'], FILTER_SANITIZE_STRING);
    	$firma_data['Direccion'] = filter_var($data['Direccion'], FILTER_SANITIZE_STRING);

    	$firma_data['Localidad'] = $this->_quitarParentesisConCodigo($data['Localidad']);
    	$firma_data['CodLoc']    = filter_var($data['CodLoc'], FILTER_SANITIZE_NUMBER_INT);

		$firma_data['Provincia'] = $this->_quitarParentesisConCodigo($data['Provincia']);
    	$firma_data['CodProv']   = filter_var($data['CodProv'], FILTER_SANITIZE_NUMBER_INT);

    	$firma_data['CodPostal'] = filter_var($data['CodPostal'], FILTER_SANITIZE_STRING);
    	$firma_data['Telefono']  = filter_var($data['Telefono'], FILTER_SANITIZE_STRING);
    	$firma_data['Celular']   = filter_var($data['Celular'], FILTER_SANITIZE_STRING);
    	$firma_data['CUIT']      = filter_var($data['CUIT'], FILTER_SANITIZE_STRING);

    	$email = filter_var($data['Email'], FILTER_VALIDATE_EMAIL);
    	$firma_data['Email'] = ($email) ? $email : '';     // Valida email. Si es falso va string vacio

    	$firma_data['CondicionFiscal'] = filter_var($data['CondicionFiscal'], FILTER_SANITIZE_STRING);
    	$firma_data['Estado']          = filter_var($data['Estado'], FILTER_SANITIZE_STRING);
    	// Opciones tipo de firma
		$firma_data['Productor']      = empty($data['Productor']) ? 0 : $data['Productor'];
		$firma_data['Acopio']         = empty($data['Acopio']) ? 0 : $data['Acopio'];
		$firma_data['Entregador']     = empty($data['Entregador']) ? 0 : $data['Entregador'];
		$firma_data['Fletero']        = empty($data['Fletero']) ? 0 : $data['Fletero'];
		$firma_data['Chofer']         = empty($data['Chofer']) ? 0 : $data['Chofer'];

#echo "<pre>"; print_r($data); echo "<pre><br><br>";
#echo "Valor email: $email - Tipo: ". gettype($email) ."<br><br>";
#echo "<pre>"; print_r($firma_data); echo "<pre><br>"; die('Ver...');

		$firma = Firma::lockForUpdate()
					   ->updateOrInsert([ 'id' => $firma_id ], $firma_data );

		return json_encode(['Status' => 'Ok']);
	}

	/**
	 * Popup para buscar firma
	 * Name: firmas.buscar
	 * 
	 * @param  Request  $request
	 * @param  Response $response
	 * @return View 
	 */
	public function firmasBuscar($request, $response)
	{
		$firmas = Firma::orderBy('Nombre')->get();

		$datos = array( 'titulo'   => 'Ramagro - Buscar Firma',
						'firmas'  => $firmas,
		);

		return $this->view->render($response, 'firmas/firmasbuscar.twig', $datos);
	}

	/**
	 * Devuelva json con datos de una firma
	 * Name: firma.getfirma
	 * 
	 * @param  Request $request
	 * @param  Response $response
	 * @param  array $args
	 * @return json
	 */
	public function getFirma($request, $response, $args)
	{
		$id = $request->getParam('id');
		$id = filter_var( $id, FILTER_SANITIZE_NUMBER_INT );
		$firma = Firma::find( $id );

		return json_encode($firma->toArray());
	}


	private function _quitarParentesisConCodigo( $value )
	{
    	$parentesis = ' (';			// Para buscar el código de localidad o prov. que viene del form
    	$string     = filter_var( $value, FILTER_SANITIZE_STRING );
    	$posParent  = strpos($string, $parentesis);
    	if ( $posParent ) {
    		$string = substr($string, 0, $posParent);
    	}

    	return $string;
	}



}
