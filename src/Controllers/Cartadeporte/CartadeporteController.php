<?php 

namespace App\Controllers\Cartadeporte;

use App\Models\Firma;
use App\Models\Cereal;
use App\Models\TipoGrano;
use App\Models\Localidades;
use App\Models\CartaDePorte;

use App\Controllers\Controller;


/**
 * 
 * Clase CartaDePorte
 * 
 */
class CartadeporteController extends Controller
{
	private $_cdp = [];			// Para salvar datos de cdp

	/**
	 * Carta de Porta
	 * Name: cartadeporte
	 * 
	 * @param  Request  $request
	 * @param  Response $response
	 * @return View 
	 */
	public function cartadeporte($request, $response)
	{
		$cereales = $this->_listaCereales();
		$fechas   = $this->utils->setFechas();
		$tiposcp  = $this->listaTiposCp();
		$productores  = Firma::where( [ ['Productor', '=', 1], ['Estado', '=', 'Activo'] ] )
							 ->orderBy('Nombre')->get();
		$acopiadores  = Firma::where( [ ['Acopio', '=', 1],  ['Estado', '=', 'Activo'] ] )
							 ->orderBy('Nombre')->get();
		$entregadores = Firma::where( [ ['Entregador', '=', 1], ['Estado', '=', 'Activo'] ] )
							 ->orderBy('Nombre')->get();
		$fleteros     = Firma::where( [ ['Fletero', '=', 1], ['Estado', '=', 'Activo'] ] )
							 ->orderBy('Nombre')->get();
		$choferes     = Firma::where( [ ['Chofer', '=', 1], ['Estado', '=', 'Activo'] ] )
							 ->orderBy('Nombre')->get();

		$datos = array( 'titulo'       => 'Ramagro - Carta de Porte',
						'fechaCarga'   => $fechas['hoy'],
						'fechaVenc'    => $fechas['diaantes'],
						'cereales'     => $cereales,
						'tiposcp'      => $tiposcp,
						'productores'  => $productores,
						'acopiadores'  => $acopiadores,
						'entregadores' => $entregadores,
						'fleteros'     => $fleteros,
						'choferes'     => $choferes
		);

		return $this->view->render($response, 'cartadeporte/cartadeporte.twig', $datos);
	}

	/**
	 * Guardar Carta de Porte
	 * Name: cartadeporte.guardar (POST)
	 * 
	 * @param  Request  $request 
	 * @param  Response $response
	 * @return json
	 */
	public function guardarCP($request, $response)
	{
		$result = ['status' => 'ok'];
		$data = $request->getParsedBody();

    	$this->_datosIniciales($data);
    	$this->_intervinientes($data);
    	$this->_granosEspecies($data);
    	$this->_destino($data);
    	$this->_transporte($data);
    	$this->_datosEnDestino($data);
    	$this->_otrosDatos($data);

#echo "<pre>"; print_r($data); echo "<pre><br><br>";
#echo "<pre>"; print_r($this->_cdp); echo "<pre><br>"; 
#die('Ver...');

    	$carta = CartaDePorte::updateOrCreate( ['NroCartaPorte' => $data['numeroCp']], $this->_cdp );

    	if (!$carta) {
    		$result = ['status' => 'error'];
    	}

		return json_encode( $result );
	}

	/**
	 * Lista de Tipos de Grano
	 * Name: cartadeporte.tiposgrano
	 * 
	 * @param  Request $request
	 * @param  Response $response
	 * @return json
	 */
	public function tiposGrano($request, $response)
	{
		$tipos = TipoGrano::all();

		return json_encode( $tipos->toArray() );
	}

	/**
	 * Devuelve json con localidades para busqueda en input por ajax
	 * Name: cartadeporte.localidadesviaajax (GET)
	 * 
	 * @param  $request
	 * @param  $response
	 * @return json
	 */
	public function localidadesViaAjax($request, $response)
	{
		// Para Typeahead...
    	$search = $request->getParam('search');
	    // Quito string de codigo...
	    if (strpos( $search, ' (') ) {
	        $search = substr($search, 0, strpos($search, ' ('));
	    }
		// Buscar localidades
		$localidadesDB = Localidades::where( 'Localidad', 'like', "$search%" )
								  ->orderBy('Localidad', 'asc')
								  ->limit(10)
								  ->get();
		$localidades = [];
		foreach ($localidadesDB as $value) {
			$localidades[] = array( 'codigo'    => $value->Codigo, 
									'localidad' => $value->Localidad,
									'cod_prov'  => $value->CodProv,
									'provincia' => $value->Provincia );
		}

	    return json_encode( $localidades );  // $localidades );  
	}

	/**
	 * Popup para buscar CdP
	 * Name: cartadeporte.buscar (GET)
	 * 
	 * @param  Request  $request
	 * @param  Response $response
	 * @return View 
	 */
	public function buscar($request, $response)
	{
		$fechas = $this->utils->setFechas();

		$cartas = CartaDePorte::select('id', 'TipoCP', 'NroCartaPorte', 'NroCEE', 'NroCTG', 'FechaCarga', 'LocProcedencia', 'LocDestino')
							  ->where( [ ['FechaCarga', '>=', $fechas['mesantes'] ], 
							  	         ['FechaCarga', '<=', $fechas['hoy'] ] ])
							  ->orderBy('FechaCarga', 'desc')
							  ->get();
		$tiposCdP = ['Recibida', 'Recibida con cambio destino', 'Emitida'];

		$datos = array( 'titulo'   => 'Ramagro - Buscar Carta Porte',
						'cartas'   => $cartas,
						'tiposCdP' => $tiposCdP,
						'desde'    => $fechas['mesantes'],
						'hasta'    => $fechas['hoy'],
		);

		return $this->view->render($response, 'cartadeporte/buscar.twig', $datos);
	}

	/**
	 * Datos de la carta de porte según id
	 * Name: cartadeporte.datos
	 * Path: .../cartadeporte/datos/{id}
	 * 
	 * @param  Request $request
	 * @param  Response $response
	 * @param  array $args
	 * @return json
	 */
	public function datos($request, $response, $args)
	{
		$datosCdP = CartaDePorte::find( $args['id'] );

		return json_encode( $datosCdP->toArray() );
	}

	/**
	 * Crea archivo de carta de porte
	 * Name: cartadeporte.archafip (POST)
	 * 
	 * @param  $request
	 * @param  $response 
	 * @return json
	 */
	public function archivoAfip($request, $response)
	{
		//$path = $_SERVER['DOCUMENT_ROOT'] . ;
		$resp = ['status' => 'Ok'];
		$texto = '';

		$arArchivo = json_decode( $request->getParam('datacp') );

		foreach ($arArchivo as $key => $value) {
			$texto = $texto . $value;

			if ($key === 2) {
				$nroCP = $resp['nroCP'] = $value;
			}
		}

		$nombreArch = "./cartasporte/cp-" . $nroCP . ".txt";
		if ( $fileAfip = fopen( $nombreArch, "a" ) ) {
            fwrite( $fileAfip, $texto );
        	fclose( $fileAfip );
		} else {
			$resp['status'] = 'Not saved file!';
		}

		return json_encode($resp);
	}

	/**
	 * Devuelve json con lista de cartas de porte segun fechas desde-hasta
	 * Name: Name: cartadeporte.listacartas (GET)
	 * 
	 * @param  Request $request
	 * @param  Response $response
	 * @return json
	 */
	public function listaCartas($request, $response)
	{

//echo "Desde: ".$request->getParam('desde')." - Hasta: ".$request->getParam['hasta']."<br>";
//die('Ver fechas...');

		$resp = CartaDePorte::select('id', 'TipoCP', 'NroCartaPorte', 'NroCEE', 'NroCTG', 'FechaCarga', 'LocProcedencia', 'LocDestino')
							  ->where( [ ['FechaCarga', '>=', $request->getParam('desde') ], 
							  	         ['FechaCarga', '<=', $request->getParam('hasta') ] ])
							  ->orderBy('FechaCarga', 'desc')
							  ->get();

		return json_encode( $resp->toArray() );
	}


	/**
	 * Devuelve lista de cereales para select
	 * 
	 * @return array
	 */
	private function _listaCereales()
	{
		//$lista = Cereal::all();

		//return $lista->toArray();
		return Cereal::all()->toArray();
	}

	/**
	 * Devuelve lista de tipos de Carta de Porte
	 * 
	 * @return array
	 */
	public function listaTiposCp()
	{
		$lista = [ ['id' => 1, 'tipo' => 'Recibida'],
				   ['id' => 2, 'tipo' => 'Recibida con cambio destino'],
				   ['id' => 3, 'tipo' => 'Emitida'] ];

		return $lista;
	}

	/**
	 * Datos iniciales carta de porte
	 * 
	 * @param  array $data
	 * @return null
	 */
	private function _datosIniciales($data)
	{
    	$this->_cdp['TipoTransporte']  = 1;		//  Va por defecto 1
    	$this->_cdp['TipoCP']          = filter_var($data['tipoCarta'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['NroCartaPorte']   = filter_var($data['numeroCp'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['NroCEE']          = filter_var($data['nroCee'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['NroCTG']          = filter_var($data['nroCtg'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['FechaCarga']      = filter_var($data['fechaCarga'], FILTER_SANITIZE_STRING);
    	$this->_cdp['FechaVencimiento'] = filter_var($data['fechaVenc'], FILTER_SANITIZE_STRING);

    	return null;
	}

	/**
	 * Pasa datos de intervinientes
	 * 
	 * @param  array $data
	 * @return null
	 */
	private function _intervinientes($data)
	{
    	// 1.- INTERVINIENTES
    	$this->_cdp['idTitular']       = filter_var($data['idTitular'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idIntermediario'] = filter_var($data['idInterm'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idRemitenteCom']  = filter_var($data['idRemitCom'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idCorredor']      = filter_var($data['idCorredor'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idMercaTerm']     = filter_var($data['idMercaTerm'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idCorrVend']      = filter_var($data['idCorrVend'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idEntregador']    = filter_var($data['idEntregador'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idDestinatario']  = filter_var($data['idDestinatario'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idDestino']       = filter_var($data['idDestino'], FILTER_SANITIZE_NUMBER_INT);
		$this->_cdp['idIntermFlete']   = filter_var($data['idIntermFlete'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idTransportista'] = filter_var($data['idTransportista'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idChofer']        = filter_var($data['idChofer'], FILTER_SANITIZE_NUMBER_INT);

    	return null;
	}

	/**
	 * Pasa datos granos y especies
	 * 
	 * @param  array $data
	 * @return null
	 */
	private function _granosEspecies($data)
	{
    	// 2.- GRANOS / ESPECIE
    	$this->_cdp['Cosecha']         = filter_var($data['Cosecha'], FILTER_SANITIZE_STRING);
    	$this->_cdp['idCodCereal']     = filter_var($data['idCodCereal'], FILTER_SANITIZE_STRING);
    	$this->_cdp['idTipoGrano']     = filter_var($data['idTipoGrano'], FILTER_SANITIZE_STRING);
    	$this->_cdp['Contrato']        = filter_var($data['Contrato'], FILTER_SANITIZE_STRING);
    	$this->_cdp['PesadoEnDest']    = filter_var($data['PesadoEnDest'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['CalidadOpc']      = filter_var($data['CalidadOpc'], FILTER_SANITIZE_STRING);
    	$this->_cdp['KgsEstimados']    = filter_var($data['KgsEstimados'], FILTER_VALIDATE_FLOAT);
    	$this->_cdp['PesoBruto2']      = filter_var($data['PesoBruto2'], FILTER_VALIDATE_FLOAT);
    	$this->_cdp['PesoTara2']       = filter_var($data['PesoTara2'], FILTER_VALIDATE_FLOAT);
    	$this->_cdp['TipoPesado']      = 1;		//  Va por defecto 1
    	$this->_cdp['KgsNetoCarga']    = filter_var($data['KgsNetoCarga'], FILTER_VALIDATE_FLOAT);
    	$this->_cdp['Observac2']       = filter_var($data['Observac2'], FILTER_SANITIZE_STRING);
    	$this->_cdp['Establecim']      = filter_var($data['Establecim'], FILTER_SANITIZE_STRING);
    	$this->_cdp['DirProcedencia']  = filter_var($data['DirProcedenc'], FILTER_SANITIZE_STRING);
    	$this->_cdp['LocProcedencia']  = filter_var($data['LocProcedenc'], FILTER_SANITIZE_STRING);
    	$this->_cdp['ProvProcedencia'] = filter_var($data['ProvProcedenc'], FILTER_SANITIZE_STRING);
    	$this->_cdp['CodProcedencia']  = 0;    // Falta en pantalla
    	$this->_cdp['CodLocProcedencia'] = filter_var($data['CodLocProcedencia'], FILTER_SANITIZE_NUMBER_INT);

   		return null;
	}

	/**
	 * Pasa datos de destino
	 * 
	 * @param  array $data
	 * @return null
	 */
	private function _destino($data)
	{
    	// 3.- DESTINO
    	$this->_cdp['DirDestino']      = filter_var($data['DirDestino'], FILTER_SANITIZE_STRING);
    	$this->_cdp['CodDestino']      = 0;    // Falta en pantalla
    	$this->_cdp['LocDestino']      = filter_var($data['LocDestino'], FILTER_SANITIZE_STRING);
    	$this->_cdp['CodLocDestino']   = filter_var($data['CodLocDestino'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['ProvDestino']     = filter_var($data['ProvDestino'], FILTER_SANITIZE_STRING);

   		return null;
	}

	/**
	 * Pasa datos transporte
	 * 
	 * @param  array $data
	 * @return null
	 */
	private function _transporte($data)
	{
    	// 4.- DATOS DEL TRANSPORTE
    	$this->_cdp['PagadorFlete']    = filter_var($data['PagadorFlete'], FILTER_SANITIZE_STRING);
    	$this->_cdp['KmsaRecorrer']    = filter_var($data['KmsaRecorrer'], FILTER_VALIDATE_FLOAT);
    	$this->_cdp['PatenteCamion']   = filter_var($data['PatenteCamion'], FILTER_SANITIZE_STRING);
    	$this->_cdp['PatenteAcopl']    = filter_var($data['PatenteAcopl'], FILTER_SANITIZE_STRING);
		$this->_cdp['TarifaReferencia'] = filter_var($data['TarifaReferencia'], FILTER_VALIDATE_FLOAT);    	
    	$this->_cdp['TarifaxTon']      = filter_var($data['TarifaxTon'], FILTER_VALIDATE_FLOAT);

   		return null;
	}

	/**
	 * Datos en destinio y descarga
	 * 
	 * @param  array $data
	 * @return null
	 */
	private function _datosEnDestino($data)
	{
    	// 5.- DATOS A COMPLETAR EN LUGAR DE DESTINO Y DESCARGA
    	$this->_cdp['FechaDescarga']   = filter_var($data['FechaDescarga'], FILTER_SANITIZE_STRING);
    	$this->_cdp['FechaArriboDestino'] = filter_var($data['FechaArriboDestino'], FILTER_SANITIZE_STRING);
    	$this->_cdp['HoraArribo']      = filter_var($data['HoraArribo'], FILTER_SANITIZE_STRING);
    	$this->_cdp['HoraDescarga']    = filter_var($data['HoraDescarga'], FILTER_SANITIZE_STRING);
    	$this->_cdp['TurnoNro']        = filter_var($data['TurnoNro'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['PesoBruto5']      = filter_var($data['PesoBruto5'], FILTER_VALIDATE_FLOAT);
    	$this->_cdp['PesoTara5']       = filter_var($data['PesoTara5'], FILTER_VALIDATE_FLOAT);
    	$this->_cdp['KgsNetoDescarga'] = filter_var($data['KgsNetoDescarga'], FILTER_VALIDATE_FLOAT);
    	$this->_cdp['Observac5']       = filter_var($data['Observac5'], FILTER_SANITIZE_STRING);
    	$this->_cdp['idReDestino']     = 0;
    	$this->_cdp['CodLocReDestino'] = 0;
    	$this->_cdp['CodReDestino']    = 0;

    	return null;
	}

	/**
	 * Otros datos
	 * 
	 * @param  array $data
	 * @return null
	 */
	private function _otrosDatos($data)
	{
    	// 6.- OTROS DATOS
    	$this->_cdp['NroMuestra']      = filter_var($data['NroMuestra'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['Humedad']         = filter_var($data['Humedad'], FILTER_VALIDATE_FLOAT);
    	$this->_cdp['idLote']          = filter_var($data['idLote'], FILTER_SANITIZE_NUMBER_INT);
    	$this->_cdp['idSilo']          = filter_var($data['idSilo'], FILTER_SANITIZE_NUMBER_INT);

    	return null;
	}

}
