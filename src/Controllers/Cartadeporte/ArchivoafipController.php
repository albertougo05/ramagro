<?php 

namespace App\Controllers\Cartadeporte;

use App\Models\Firma;
use App\Models\CartaDePorte;

use App\Controllers\Controller;


/**
 * 
 * Clase ArchivoafipController
 * 
 */
class ArchivoafipController extends Controller
{
	/**
	 * Crea y guarda en disco archivo a Afip
	 * Name: cartadeporte.archivoafip (GET)
	 * 
	 * @param  Request  $request
	 * @param  Response $response
	 * @return View   // por ahora json
	 */
	public function archivoAfip($request, $response)
	{
		// Recibe array con ids de cartas de porte (./cartadeporte/archivoafip?ids=4,... )
		$arIds = explode(",", $request->getParam('ids'));
		$arLineas = [];
		$resp = [ 'status' => 'Ok', 'file' => '' ];

		foreach ($arIds as $value) {
			# busco data CP
			$dataCp = CartaDePorte::find($value);
			# Armo linea para archivo...
			$linea = $this->_armoLinea( $dataCp );
			# Guardo línea en array
			$arLineas[] = $linea;
			$linea = '';
		}

		$result = $this->_crearArchivo($arLineas);

		if (!$result['status']) {
			$resp['status'] = 'Archivo NO creado';
		}

		$resp['file'] = $result['file'];		// Paso el nombre del archivo

#echo "Array de ids: <br><pre>";
#print_r($arIds);
#echo "</pre><br>";
#echo "Linea: ".$arLineas[0];
#echo "<br>";
#die('Ver ids...');

		return json_encode($resp);
	}

	/**
	 * Arma línea de datos...
	 * 
	 * @param  Array $data
	 * @return string
	 */
	private function _armoLinea( $data )
	{
		$linea = $this->_datosBasicos( $data );
		$linea = $linea . $this->_intervinientes( $data );
		$linea = $linea . $this->_granosEspecie( $data );
		$linea = $linea . $this->_destinoTransp( $data );

		if ( $data->TipoCP === 1 ) {		// si es Recibida

			$linea = $linea . $this->_datosRecibida( $data );
		}

		$tarifa = number_format( $data->TarifaReferencia, 2, ',', '' );
		$linea = $linea . str_pad( $tarifa, 8, "0", STR_PAD_LEFT );	// 30/36 - Flete-Tarifa de referencia

		return $linea;
	}

	/**
	 * Datos básicos
	 * 
	 * @param  Array $dat
	 * @return String
	 */
	private function _datosBasicos( $dat ) 
	{
		$lin = '1';						// 01 - Tipo Transporte (1)
		$lin = $lin . $dat->TipoCP;		// 02 - Tipo CP (1)
		$lin = $lin . str_pad( $dat->NroCartaPorte, 12, "0", STR_PAD_LEFT );	// 03 - Nro.CP (12)
		$lin = $lin . str_pad( $dat->NroCEE, 14, "0", STR_PAD_LEFT );	// 04 - Nro. CEE (14)
		$lin = $lin . str_pad( $dat->NroCTG, 8, "0", STR_PAD_LEFT );	// 05 - Nro. CTG (8)
		$lin = $lin . date('dmY', strtotime( $dat->FechaCarga ) );  	// 06 - Fecha carga (8)
		return $lin;
	}

	private function _intervinientes( $dat )
	{
		$lin = $this->_cuitInterv( $dat->idTitular );				// 07 - Cuit Titular (11)
		$lin = $lin . $this->_cuitInterv( $dat->idIntermediario );	// 08 - Cuit Intermed (11)
		$lin = $lin . $this->_cuitInterv( $dat->idRemitenteCom );	// 09 - Cuit Remit.Com (11)
		$lin = $lin . $this->_cuitInterv( $dat->idCorredor );		// 10 - Cuit Corr.Comp (11)
		$lin = $lin . $this->_cuitInterv( $dat->idEntregador );		// 11 - Cuit Repres.Entr (11)
		$lin = $lin . $this->_cuitInterv( $dat->idDestinatario );	// 12 - Cuit Destinatario (11)
		$lin = $lin . $this->_cuitInterv( $dat->idDestino );		// 13 - Cuit Estab.Dest (11)
		$lin = $lin . $this->_cuitInterv( $dat->idTransportista );	// 14 - Cuit Transportis (11)
		$lin = $lin . $this->_cuitInterv( $dat->idChofer );			// 15 - Cuit Chofer (11)
		return $lin;
	}

	/**
	 * Devuelve cuit de la firma sin los guiones
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	private function _cuitInterv( $id )
	{
		if ( $id !== 0) {
			//$firma = Firma::find($id);
			$cuit = Firma::find($id)->CUIT;	// Busca cuit de la firma, o devuelve 0...
			$cuit = str_replace('-', '', $cuit);		// Quita los guiones...

		} else $cuit = '0';

		return str_pad( $cuit, 11, "0", STR_PAD_LEFT );
	}

	private function _granosEspecie( $dat )
	{
		$lin = $dat->Cosecha;				// 16 - Cosecha (5) AA-AA
		$lin = $lin . $dat->idCodCereal;	// 17 - Codigo especie (3)
		$lin = $lin . $dat->idTipoGrano;	// 18 - Tipo de grano (2)
		$lin = $lin . str_pad( $dat->Contrato, 20, " ", STR_PAD_LEFT ); // 19 - Contrato / Boleta compra-venta (20) A
		$lin = $lin . $dat->TipoPesado;									// 20 - Tipo de pesado (1)

		$kilos = number_format($dat->KgsNetoCarga, 2, ',', '');
		$lin = $lin . str_pad( $kilos, 11, "0", STR_PAD_LEFT );	// 21 - Peso neto de carga / Peso total despacho (8 int , 2 dec [00001234,56])

		$lin = $lin . str_pad( $dat->CodProcedencia, 6, "0", STR_PAD_LEFT );	// 22 - Código Establecimiento de procedencia
		$lin = $lin . str_pad( $dat->CodLocProcedencia, 5, "0", STR_PAD_LEFT );	// 23 - Código Localidad de procedencia

		return $lin;
	}

	private function _destinoTransp( $dat )
	{
		$lin = str_pad( $dat->CodDestino, 6, "0", STR_PAD_LEFT );			// 24 - Código Establecimiento Destino
		$lin = $lin . str_pad( $dat->CodLocDestino, 5, "0", STR_PAD_LEFT );	// 25 - Código Localidad Destino
		$lin = $lin . str_pad( $dat->KmsaRecorrer, 4, "0", STR_PAD_LEFT );	// 26 - Kms a recorrer
		$lin = $lin . str_pad( $dat->PatenteCamion, 11, " ", STR_PAD_LEFT );	// 27 - Patente camion
		$lin = $lin . str_pad( $dat->PatenteAcopl, 11, " ", STR_PAD_LEFT );		// 28 - Patente acoplado

		$tarifa = number_format($dat->TarifaxTon, 2, ',', '');
		$lin = $lin . str_pad( $tarifa, 8, "0", STR_PAD_LEFT );			// 29 - Tarifa por tonelada

		return $lin;
	}

	private function _datosRecibida( $dat )
	{
		$lin = date('dmY', strtotime( $dat->FechaDescarga ) ); 			// 30 - Fecha descarga
		$lin = $lin . date('dmY', strtotime( $dat->FechaArriboDestino ) );	// 31 - Fecha arribo a destino/redestino
		$kilos = number_format($dat->KgsNetoDescarga, 2, ',', '');
		$lin = $lin . str_pad( $kilos, 11, "0", STR_PAD_LEFT );			// 32 - Peso neto de descarga
		$lin = $lin . $this->_cuitInterv( $dat->idReDestino ); 			// 33 - CUIT establecimiento Redestino
		$lin = $lin . str_pad( $dat->CodLocReDestino, 5, "0", STR_PAD_LEFT );// 34 - Código localidad Redestino
		$lin = $lin . str_pad( $dat->CodReDestino, 6, "0", STR_PAD_LEFT );// 35 - Código establecimiento Redestino

		return $lin;
	}

	/**
	 * Crea archivo de carta de porte
	 * 
	 * @param  $request
	 * @return json     // Puede ser un boolean
	 */
	private function _crearArchivo($arLineasCp)
	{
		$seGuardoArch = [ 'status' => false, 'file' => '' ];
		$nombre       = "cartasdeporte-".date('ymd');
		$nombreArch   = "cartasporte/" . $nombre . ".txt";

		if ( $fileAfip = fopen( $nombreArch, "w" ) ) {

			foreach ($arLineasCp as $value) {
				fwrite( $fileAfip, $value . "\n" );
			}
            
        	fclose( $fileAfip );
        	$seGuardoArch['status'] = true;
        	$seGuardoArch['file'] = $nombreArch;
		}

		return $seGuardoArch;
	}


}
