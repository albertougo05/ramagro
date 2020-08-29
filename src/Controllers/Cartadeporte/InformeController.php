<?php 

namespace App\Controllers\Cartadeporte;

use App\Models\Firma;
use App\Models\Cereal;
use App\Models\CartaDePorte;

use App\Controllers\Controller;


/**
 * 
 * Clase InformeController
 * 
 */
class InformeController extends Controller
{

	/**
	 * Informe de cartas de porte
	 * Name: cartadeporte.informe (GET)
	 * 
	 * @param  Request  $request
	 * @param  Response $response
	 * @return View
	 */
	public function informe($request, $response)
	{
		$fechas      = $this->utils->setFechas();
		$cereales    = $this->_getCereales();
		$tiposcp     = $this->CartadeporteController->listaTiposCp();
		$productores = $this->_getProductores();
		$destinos    = $this->_getDestinos();
		$campanias   = $this->_getCampanias();
		$cartasporte = $this->_getCartasPorte( $fechas );

		$datos = array( 'titulo'      => 'Ramagro - Informe CP',
						'fechaDesde'  => $fechas['mesantes'],
						'fechaHasta'  => $fechas['hoy'],
						'cereales'    => $cereales,
						'tiposcp'     => $tiposcp,
						'productores' => $productores,
						'cartasporte' => $cartasporte,
						'destinos'    => $destinos,
						'campanias'   => $campanias
		);

		return $this->view->render($response, 'cartadeporte/informe.twig', $datos);
	}

	/**
	 * Imprime el informe (GET)
	 * Name: cartadeporte.informe.imprime
	 * 
	 * @param  Request  $request
	 * @param  Response $response
	 * @return View
	 */
	public function imprime($request, $response)
	{
		$parametros = $request->getParams();
		$listaCPs = $this->_getSelectCP($parametros);		// Hace consulta a DB

/*echo "<pre>"; print_r($listaCPs); echo "</pre><br>"; die('Ver listado...');*/

		$urlArchivo = $this->_setParamsUrlArchivoAfip($listaCPs);		// Para armar url p/grabar archivo afip (./cartadeporte/archivoafip?ids=4,... )
		$filtros = $this->_getFiltrosPedidos($parametros);

		$datos = [ 'titulo'      => 'Ramagro - Imp.Info CP',
				   'fecha'       => date('d/m/Y'),
				   'desde'       => $request->getParam('desde'),
				   'hasta'       => $request->getParam('hasta'),
				   'urlarchivo'  => $urlArchivo,
				   'tiposcps'    => ['','Recibida', 'Recibida c/cambio dest.', 'Emitida'],
				   'filtros'     => $filtros,
				   'cartasporte' => $listaCPs,
		];

		return $this->view->render($response, 'cartadeporte/informe_print.twig', $datos);
	}

	/**
	 * Devuelve tabla cereales
	 * 
	 * @return array
	 */
	private function _getCereales()
	{
		return Cereal::select('Codigo', 'Descripcion')
					 ->orderBy('Descripcion')
					 ->get()
					 ->toArray();
	}

	/**
	 * Devuelve los destinos únicos de cartas de porte (para filtrar)
	 *
	 * @return array
	 */
	private function _getDestinos( $idCp='' )
	{
		return CartaDePorte::select('LocDestino', 'CodLocDestino')
							->distinct()
							->orderBy('LocDestino')
							->get()
							->toArray();
	}

	private function _getProductores()
	{
		return Firma::select('id', 'Nombre', 'Cuit')
							 ->where( [ ['Productor', '=', 1], 
										['Estado', '=', 'Activo'] ] )
							 ->orderBy('Nombre')
							 ->get()
							 ->toArray();
	}

	private function _getCartasPorte( $fechas )	
	{
		return CartaDePorte::where( [ ['FechaCarga', '>=', $fechas['mesantes']], 
											  ['FechaCarga', '<=', $fechas['hoy']] ])
							 ->orderBy('FechaCarga', 'desc')
							 ->get()
							 ->toArray();
	}

	private function _getCampanias( $idCp = '' )
	{
		return CartaDePorte::select('Cosecha')
							->distinct()
							->orderBy('Cosecha')
							->get()
							->toArray();
	}

	/**
	 * Devuelve array con lista de CP selecionadas para informe
	 * 
	 * @param  Array $params 
	 * @return array
	 */
	private function _getSelectCP($params)
	{
		$isWhere = false;		//	Para saber cuando se puso un where antes...
		$sql = $this->_getSqlBasico();

		if ($params['idprod'] !== '0') {		// Filtro productor
			$sql = $sql . " WHERE cp.idTitular = " . $params['idprod'];
			$isWhere = true; }

		if ($params['iddest'] !== '0') {		// Filtro Localidad Destino
			$sql = $sql . (($isWhere) ? " AND " : " WHERE ") . "cp.CodLocDestino =  " . $params['iddest'];
			$isWhere = true; } 

		if ($params['idcere'] !== '0') {
			$sql = $sql .(($isWhere) ? " AND " : " WHERE ") . "cp.idCodCereal =  " . $params['idcere'];
			$isWhere = true; } 

		if ($params['idcamp'] !== '0') {
			$sql = $sql .(($isWhere) ? " AND " : " WHERE ") . "cp.Cosecha =  '" . $params['idcamp'] . "'";
			$isWhere = true; } 

		if ($params['idtipo'] !== '0') {
			$sql = $sql .(($isWhere) ? " AND " : " WHERE ") . "cp.TipoCP =  " . $params['idtipo'];
			$isWhere = true; } 

		$whereFechas = $this->utils->getWhereFechas($params['desde'], $params['hasta'], "cp.FechaCarga", $isWhere); 

		if ($whereFechas !== '') {
			$sql = $sql .(($isWhere) ? "" : " WHERE "). $whereFechas; }
		
		$sql = $sql . " ORDER BY cp.FechaCarga desc";
		$lista = $this->pdo->pdoQuery($sql);		# Envio la consulta

		return $lista;
	}

	private function _getSqlBasico()
	{
		$sql = "SELECT cp.id, cp.NroCartaPorte, cp.TipoCP, cp.FechaCarga, cp.NroCEE, ";
		$sql = $sql . "cp.NroCTG, cp.idTitular, firTit.Nombre as Titular, ";
		$sql = $sql . "cp.idCodCereal, cer.Descripcion as Cereal, cp.Cosecha, ";
		$sql = $sql . "cp.idDestino, firDes.Nombre as Destino, cp.Establecim, ";
		$sql = $sql . "cp.LocProcedencia, cp.KgsNetoCarga, cp.LocDestino, cp.KgsNetoDescarga ";
		$sql = $sql . "FROM ra_CartaDePorte AS cp ";
		$sql = $sql . "LEFT JOIN ra_Firmas AS firTit ON cp.idTitular = firTit.id ";
		$sql = $sql . "LEFT JOIN ra_Firmas AS firDes ON cp.idDestino = firDes.id ";
		$sql = $sql . "LEFT JOIN ra_Cereales AS cer ON cp.idCodCereal = cer.Codigo";

		return $sql;
	}

	private function _setParamsUrlArchivoAfip($listaCps)
	{
		$params = "?ids=";
		$count  = 1;
		$cant   = count($listaCps);

		foreach ($listaCps as $value) {
			$params = $params . $value['id'];

			if ($count < $cant) {
				$params = $params . ",";
			}
			$count++;
		}

		return $params;
	}

	private function _getFiltrosPedidos($params)
	{
		$filtros = '';

		if ($params['idprod'] !== '0') {		// Filtro productor
			$filtros = "Titular"; }

		if ($params['iddest'] !== '0') {		// Filtro Localidad Destino
			$filtros = $filtros . (($filtros !== '') ? ", " : " ") . "Destino"; } 

		if ($params['idcere'] !== '0') {
			$filtros = $filtros . (($filtros !== '') ? ", " : " ") . "Cereal"; } 

		if ($params['idcamp'] !== '0') {
			$filtros = $filtros . (($filtros === '') ? ", " : " ") . "Campaña"; } 

		if ($params['idtipo'] !== '0') {
			$filtros = $filtros . (($filtros === '') ? ", " : " ") . "Tipo CP"; } 

		return $filtros;
	}

}
