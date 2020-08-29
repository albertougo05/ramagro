//
// Acciones para rellenar el formulario
//

var FILLFORM = {

	cartaDePorte: {},
	datosOk: false,

	buscarCdP: function ( id ) {		//	Buscar datos de CdP

		fetch( _pathDatosCdP + id )
			.then( response => response.json() )
			.then( function (cartadeporte) {
					//console.log( 'Número carta: ' + cartadeporte.NroCartaPorte );
					// Llamar a funcion para rellenar form
					FILLFORM.fillCartaDePorte( cartadeporte );
			})
			.catch( error => toastr["error"]("No se obtuvieron datos !!", "Error !!") );
		return null;
	},

	fillCartaDePorte: function ( cdp ) {		// Rellena formulario con datos desde BD

		$('#numeroCp').val( cdp.NroCartaPorte ); $('#tipoCarta').val( cdp.TipoCP );
		$('#nroCtg').val( cdp.NroCTG ); $('#nroCee').val( cdp.NroCEE );
		$('#fechaCarga').val( cdp.FechaCarga );
		$('#fechaVenc').val( cdp.FechaVencimiento );

		this.intervinientes( cdp );
		this.granosEspecies( cdp );
		this.destino( cdp );
		this.transporte( cdp );
		this.destinoDescarga( cdp );
		this.otrosDatos( cdp );
		return null;
	},

	intervinientes: function ( cdp ) {		// 1. INTERVINIENTES

		$('#selTitular').val( cdp.idTitular );
		$('#cuitTitular').val( $('#selTitular').children("option:selected").attr('data-cuit') );
		$('#selInterm').val( cdp.idIntermediario || 0 );
		$('#cuitInterm').val( $('#selInterm').children("option:selected").attr('data-cuit') );
		$('#selRemitCom').val( cdp.idRemitenteCom || 0 );
		$('#cuitRemitCom').val( $('#selRemitCom').children("option:selected").attr('data-cuit') );
		$('#selCorrComp').val( cdp.idCorredor || 0 );
		$('#cuitCorrComp').val( $('#selCorrComp').children("option:selected").attr('data-cuit') );

		$('#selMercaTerm').val( cdp.idMercaTerm || 0 );
		$('#cuitMercaTerm').val( $('#selMercaTerm').children("option:selected").attr('data-cuit') );

		$('#selCorrVend').val( cdp.idCorrVend || 0 );
		$('#cuitCorrVend').val( $('#selCorrVend').children("option:selected").attr('data-cuit') );
		$('#selRepresEnt').val( cdp.idEntregador || 0 );
		$('#cuitRepresEnt').val( $('#selRepresEnt').children("option:selected").attr('data-cuit') );
		$('#selDestinat').val( cdp.idDestinatario || 0 );
		$('#cuitDestinat').val( $('#selDestinat').children("option:selected").attr('data-cuit') );
		$('#selDestino').val( cdp.idDestino || 0 );
		$('#cuitDestino').val( $('#selDestino').children("option:selected").attr('data-cuit') );
		$('#selIntermFlete').val( cdp.idIntermFlete || 0 );
		$('#cuitIntermFlete').val( $('#selIntermFlete').children("option:selected").attr('data-cuit') );
		$('#selTransport').val( cdp.idTransportista || 0 );
		$('#cuitTransport').val( $('#selTransport').children("option:selected").attr('data-cuit') );
		$('#selChofer').val( cdp.idChofer || 0 );
		$('#cuitChofer').val( $('#selChofer').children("option:selected").attr('data-cuit') );
		return null;
	},

	granosEspecies: function ( cdp ) {		// 2.- GRANOS / ESPECIE

		$('#cosecha').val( cdp.Cosecha );
		$('#selGrano').val( cdp.idCodCereal );
		$('#selTipoGrano').val( cdp.idTipoGrano );
		$('#contrato').val( cdp.Contrato );
		if ( cdp.PesadoEnDest === 1 ) $(".myCheckbox").prop('checked', true);
		$("input:radio[name='radCalidad'][value='" + cdp.CalidadOpc + "']").prop('checked', true);
		$('#kgsEstimados').val( cdp.KgsEstimados );
		$('#pesoBruto2').val( cdp.PesoBruto2 );
		$('#pesoTara2').val( cdp.PesoTara2 );
		$('#pesoNeto2').val( cdp.KgsNetoCarga );
		$('#observac2').val( cdp.Observac2 );
		$('#establecim').val( cdp.Establecim );
		$('#direccionProc').val( cdp.DirProcedencia );
		$('#localidadProc').val( cdp.LocProcedencia );
		$('#provinciaProc').val( cdp.ProvProcedencia );
		return null;
	},

	destino: function ( cdp ) {		// 3.- DESTINO

		$('#direccionDest').val( cdp.DirDestino );
		$('#localidadDest').val( cdp.LocDestino );
		$('#provinciaDest').val( cdp.ProvDestino );
		return null;
	},

	transporte: function ( cdp ) {		// 4.- DATOS DEL TRANSPORTE

		$('#pagadorFle').val( cdp.PagadorFlete || '' );
		$('#camion').val( cdp.PatenteCamion || '' );
		$('#acoplado').val( cdp.PatenteAcopl || '' );
		$('#kmsRecorrer').val( cdp.KmsaRecorrer || 0 );
		$('#tarifaRef').val( cdp.TarifaReferencia || 0 );
		$('#tarifa').val( cdp.TarifaxTon || 0 );
		return null;
	},

	destinoDescarga: function ( cdp ) {		// 5.- DATOS A COMPLETAR EN LUGAR DE DESTINO Y DESCARGA

		$('#fechaArribo').val( cdp.FechaArriboDestino );
		$('#horaArribo').val( cdp.HoraArribo );
		$('#fechaDescarga').val( cdp.FechaDescarga );
		$('#horaDescarga').val( cdp.HoraDescarga );
		$('#turnoNro').val( cdp.TurnoNro || 0 );
		$('#pesoBruto5').val( cdp.PesoBruto5 || 0 );
		$('#pesoTara5').val( cdp.PesoTara5 || 0 );
		$('#pesoNeto5').val( cdp.KgsNetoDescarga || 0 );
		$('#observac5').val( cdp.Observac5 || '' );
		//$('#idReDestino').val( cdp.idReDestino );
		//$('#codLocReDestino').val( cdp.CodReLocDestino );
		//$('#codReDestino').val( cdp.CodReDestino );
		return null;
	},

	otrosDatos: function ( cdp ) {		// 6.- OTROS DATOS

		$('#nroMuestra').val( cdp.NroMuestra || 0 );
		$('#humedad').val( cdp.Humedad || 0 );
		$('#idLote').val( cdp.idLote || '' );
		$('#idSilo').val( cdp.idSilo || '' );
		return null;
	},

};
