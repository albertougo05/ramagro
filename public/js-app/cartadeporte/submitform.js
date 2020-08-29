//
// Funciones para enviar el formulario (Carta de Porte)
//

var SUBMITFORM = {

	// Armar string con datos CdP para ser enviados por ajax
	armarData: function () {
		const csrf = $('form#formCsrf').serialize();
		let data = csrf;
		data += this.primerosDatos();
		data += this.intervinientes();
		data += this.granosEspecies();
		data += this.destino();
		data += this.transporte();
		data += this.destinoDescarga();

		// 6.- OTROS DATOS
		data += '&NroMuestra=' + $('#nroMuestra').val();
		data += '&Humedad=' + CARTA.stringToFloat( $('#humedad').val() );
		data += '&idLote=' + $('#idLote').val();
		data += '&idSilo=' + $('#idSilo').val();
		return data;
	},

	// Primeros datos de CdP
	primerosDatos: function () {
		let data = '';
		data += '&numeroCp=' + $('#numeroCp').val() + '&tipoCarta=' + $('#tipoCarta').val();
		data += '&nroCtg='   + $('#nroCtg').val()   + '&nroCee='    + $('#nroCee').val();
		data +=	'&fechaCarga='  + $('#fechaCarga').val() + '&fechaVenc=' + $('#fechaVenc').val();
		return data;
	},

	// Datos de: 1.- INTERVINIENTES
	intervinientes: function () {
		let data = '';
		data += '&idTitular='      + $('#selTitular').children("option:selected").val(); 
		data += '&idInterm='       + $('#selInterm').children("option:selected").val();
		data += '&idRemitCom='     + $('#selRemitCom').children("option:selected").val();
		data += '&idCorredor='     + $('#selCorrComp').children("option:selected").val();
		data += '&idMercaTerm='    + $('#selMercaTerm').children("option:selected").val();
		data += '&idCorrVend='     + $('#selCorrVend').children("option:selected").val();
		data += '&idEntregador='   + $('#selRepresEnt').children("option:selected").val();
		data += '&idDestinatario=' + $('#selDestinat').children("option:selected").val();
		data += '&idDestino='      + $('#selDestino').children("option:selected").val();
		data += '&idIntermFlete='  + $('#selIntermFlete').children("option:selected").val();
		data += '&idTransportista=' + $('#selTransport').children("option:selected").val();
		data += '&idChofer='        + $('#selChofer').children("option:selected").val();
		return data;
	},

	// Datos de: 2.- GRANOS / ESPECIE
	granosEspecies: function () {
		let data = '';
		data += '&Cosecha='        + $('#cosecha').val();
		data += '&idCodCereal='    + $('#selGrano').children("option:selected").val();
		data += '&idTipoGrano='    + $('#selTipoGrano').children("option:selected").val();
		data += '&Contrato='       + $('#contrato').val();

		data += '&PesadoEnDest='   + ( $('#chkPesadoDest').is(':checked') ? 1 : 0 );

		data += '&CalidadOpc='     + $('input:radio[name=radCalidad]:checked').val();
		data += '&KgsEstimados='   + CARTA.stringToFloat( $('#kgsEstimados').val() );
		data += '&PesoBruto2='     + CARTA.stringToFloat( $('#pesoBruto2').val() );
		data += '&PesoTara2='      + CARTA.stringToFloat( $('#pesoTara2').val() );
		data += '&KgsNetoCarga='   + CARTA.stringToFloat( $('#pesoNeto2').val() );
		data += '&Observac2='      + $('#observac2').val();
		data += '&Establecim='     + $('#establecim').val();
		data += '&DirProcedenc=' + $('#direccionProc').val();
		data += '&LocProcedenc=' + $('#localidadProc').val();
		data += '&ProvProcedenc=' + $('#provinciaProc').val();
	// falta código de procedencia (CodProcedencia) está en tabla ... (??)
		//data += '&=CodProcedencia' + this.getCodLoc( $('#codProc').val() );
		data += '&CodLocProcedencia=' + this.getCodLoc( $('#localidadProc').val() );
		return data;
	},

	// Datos de: 3.- DESTINO
	destino: function () {
		let data = '';
		data += '&DirDestino='    + $('#direccionDest').val();
	// falta codido de destino (CodDestino)...
		// data += '&CodDestino=' + this.getCodLoc( $('#codDest').val() );
		data += '&LocDestino='    + $('#localidadDest').val();
		data += '&CodLocDestino=' + this.getCodLoc( $('#localidadDest').val() );
		data += '&ProvDestino='   + $('#provinciaDest').val();
		return data;
	},

	// Datos de: 4.- DATOS DEL TRANSPORTE
	transporte: function () {
		let data = '';
		data += '&PagadorFlete='     + $('#pagadorFle').val();
		data += '&PatenteCamion='    + $('#camion').val();
		data += '&PatenteAcopl='     + $('#acoplado').val();
		data += '&KmsaRecorrer='     + CARTA.stringToFloat( $('#kmsRecorrer').val() );
		data += '&TarifaReferencia=' + CARTA.stringToFloat( $('#tarifaRef').val() );
		data += '&TarifaxTon='       + CARTA.stringToFloat( $('#tarifa').val() );
		return data;
	},

	// Datos de: 5.- DATOS A COMPLETAR EN LUGAR DE DESTINO Y DESCARGA
	destinoDescarga: function () {
		let data = '';
		data += '&FechaArriboDestino=' + $('#fechaArribo').val();
		data += '&HoraArribo=' + $('#horaArribo').val();
		data += '&FechaDescarga=' + $('#fechaDescarga').val();
		data += '&HoraDescarga=' + $('#horaDescarga').val();
		data += '&TurnoNro=' + $('#turnoNro').val();
		data += '&PesoBruto5=' + CARTA.stringToFloat( $('#pesoBruto5').val() );
		data += '&PesoTara5=' + CARTA.stringToFloat( $('#pesoTara5').val() );
		data += '&KgsNetoDescarga=' + CARTA.stringToFloat( $('#pesoNeto5').val() );
		data += '&Observac5=' + $('#observac5').val();
	// falta campo idReDestino (??)
		//data += '&idReDestino=' + $('#idReDestino').val();
	// falta campo CodLocReDestino (??)
		//data += '&CodReLocDestino=' + $('#codLocReDestino').val();
	// falta campo CodReDestino (??)
		//data += '&CodReDestino=' + $('#codReDestino').val();
		return data;
	},

	// Submit Carta de Porte
	submitCarta: function (dataCdP) {
		$.post( _pathGuardarCP, dataCdP )
			.done(function(data) 
			{
				const dataRet = JSON.parse(data);	 //$.parseJSON(data);
				console.log( "Retono: " + dataRet.status );
				toastr.clear();		// Remueve toast anterior
				toastr["success"]("Carta de Porte guardada con éxito !", "Hecho !!");
				// Salir y recargar la página. Espera 4 segundos
		    	setTimeout( function () {
		    		location.assign( _pathCartaPorte );
		    	}, 5000 );
			})
			.fail(function(data, textStatus, jqXHR) 
			{
				const dataRet = JSON.parse(data);
		console.log( "Error: " + textStatus );
				toastr["error"]("No se guardó la Carta de Porte !", "Error !!");
				SUBMITFORM.togglBtns(true);
			}
		);
 	},

	// Obtener código de localidad
	getCodLoc: function (loc) {
		const posParent1 = loc.indexOf('(') + 1,
			  posParent2 = loc.indexOf(')');
		let codigo = loc.slice(posParent1, posParent2) ;

		return codigo;
	},

	// Desactivar botones Confirma y Cancela
	togglBtns: function (verdFals) {
       	$('#btnConfirma').prop('disabled', verdFals);
       	$('#btnCancela').prop('disabled', verdFals);
	}

};
